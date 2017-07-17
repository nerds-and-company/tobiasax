<?php

namespace Craft;

use Exception;

/**
 * Tobias AX person service
 */
class TobiasAx_PersonService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const UNKNOWN_ERROR = "Unknown error retrieving persons: ";

    /**
     * @var string
     */
    const ERROR_LOGIN_CREDENTIALS = 'Invalid login credentials';

    /**
     * @var string
     */
    const ERROR_LOGIN_UNKOWN = 'Unknown error verifying credentials: ';

    /**
     * @var string
     */
    const EXCEPTION_ALREADY_EXISTS = 'Persoon\organisatie komt meer dan 1 keer voor in systeem.';

    /**
     * @var string
     */
    const ERROR_ALREADY_EXISTS = "Person already exists";

    /**
     * @var string
     */
    const ERROR_CREATE_UNKNOWN = "Unknown error creating person: ";

    /**
     * @var string
     */
    const ERROR_UPDATE_UNKNOWN = "Unknown error updating person: ";

    /**
     * creates a person and it's attributes
     * @param TobiasAx_PersonModel $person
     * @return TobiasAx_PersonModel
     * @throws TobiasAx_SoapException $e Thrown when a person can't be created
     */
    public function createPlenaryPerson($person)
    {
        // Seperate attributes for seperate calls
        $addresses = $person->Addresses;
        $communications = $person->Communications;
        $incomes = $person->Incomes;

        $responsePerson = $this->createPerson($person);

        // Create addresses
        $responseAddresses = craft()->tobiasAx_personAddress->createPersonAddresses($addresses, $responsePerson->Id);

        $responsePerson->Addresses = $responseAddresses;

        // Create communications
        $responseCommunications = craft()->tobiasAx_personCommunication->upsertPersonCommunications($communications, $responsePerson->Id);
        $responsePerson->Communications = $responseCommunications;

        // Create incomes
        $responseIncomes = craft()->tobiasAx_personIncome->createPersonIncomes($incomes, $responsePerson->Id);
        $responsePerson->Incomes = $responseIncomes;

        return $responsePerson;
    }

    /**
     * Updates a person and it's attributes
     * @param TobiasAx_PersonModel $person
     * @return TobiasAx_PersonModel
     */
    public function updatePlenaryPerson($person)
    {
        $updatedPerson = $this->updatePerson($person);

        $updatedAddresses[] = craft()->tobiasAx_personAddress->upsertPersonAddresses($person->Addresses, $person->Id);

        $updatedPerson->Addresses = $updatedAddresses;

        $updatedCommunications = craft()->tobiasAx_personCommunication->upsertPersonCommunications($person->Communications, $person->Id);
        $updatedPerson->Communications = $updatedCommunications;

        $updateIncomes = craft()->tobiasAx_income->updateIncomes($person->Incomes);
        $updatedPerson->Incomes = $updateIncomes;

        return $updatedPerson;
    }

    /**
     * Creates a person object
     * @param TobiasAx_PersonModel $person The person to create
     * @return TobiasAx_PersonModel
     * @throws TobiasAx_SoapException $e Thrown when a person can't be created
     */
    public function createPerson($person)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->createPerson($person));

            $result = $service->extractSingle($envelope, 'Body/xmlns:CreatePersonResponse/xmlns:CreatePersonResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_ALREADY_EXISTS)) {
                throw new TobiasAx_SoapException(static::ERROR_ALREADY_EXISTS, null, $e);
            }
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . $e->getMessage(), null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        $responsePerson = new TobiasAx_PersonModel($result);

        return $responsePerson;
    }

    /**
     * Updates a person object
     * @param TobiasAx_PersonModel $person The person to update
     * @return TobiasAx_PersonModel
     * @throws TobiasAx_SoapException $e Thrown when a person can't be created
     */
    public function updatePerson($person)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->updatePerson($person));

            $result = $service->extractSingle($envelope, 'Body/xmlns:UpdatePersonResponse/xmlns:UpdatePersonResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_ALREADY_EXISTS)) {
                throw new TobiasAx_SoapException(static::ERROR_ALREADY_EXISTS, null, $e);
            }
            throw new TobiasAx_SoapException(static::ERROR_UPDATE_UNKNOWN . $e->getMessage(), null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_UPDATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        $responsePerson = new TobiasAx_PersonModel($result);

        return $responsePerson;
    }

    /**
     * Gets person by username and password
     * @param  string $username user login name
     * @param  string $password user login password
     * @return TobiasAx_PersonModel
     * @throws TobiasAx_SoapException $e Thrown when a person can't be found
     * @throws Exception $e Thrown when an error occurred while retrieving the person
     */
    public function getPersonByLogin($username, $password)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->getPersonByLogin($username, $password));
            $result = $service->extractSingle($envelope, 'Body/xmlns:GetPersonByLoginResponse/xmlns:GetPersonByLoginResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), $service::ERROR_PERSON_NOTFOUND)) {
                throw new TobiasAx_SoapException(static::ERROR_LOGIN_CREDENTIALS, null, $e);
            }
            throw new TobiasAx_SoapException(static::ERROR_LOGIN_UNKOWN . $e->getMessage(), null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_LOGIN_UNKOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_PersonModel($result);
    }

    /**
     * @param  string $personId person id
     * @return TobiasAx_PersonModel
     * @throws TobiasAx_SoapException $e Thrown when a person can't be found
     * @throws Exception $e Thrown when an error occured while retrieving the person
     */
    public function getPersonById($personId)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->getPersonById($personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:GetPersonResponse/xmlns:GetPersonResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), 'Ophalen persoon mislukt')) {
                throw new TobiasAx_SoapException('A person with the specified ID could not be found', 0, $e);
            }
            throw new TobiasAx_SoapException(static::UNKNOWN_ERROR, 0, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::UNKNOWN_ERROR, 0, $e);
        }

        return new TobiasAx_PersonModel($result);
    }

    /**
     * Gets user by person id
     * @param  string $personId person id
     * @return TobiasAx_UserModel
     * @throws TobiasAx_SoapException $e Thrown when a person can't be found
     * @throws Exception $e Thrown when an error occured while retrieving the person
     */
    public function getUserByPersonId($personId)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->getUserByPersonId($personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:GetUserByPersonIdResponse/xmlns:GetUserByPersonIdResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), 'Ophalen persoon mislukt')) {
                throw new TobiasAx_SoapException('A user with the specified person ID could not be found', $e);
            }
            throw new TobiasAx_SoapException(static::UNKNOWN_ERROR . $e->getMessage(), 0, $e);
        }

        return new TobiasAx_UserModel($result);
    }

    /**
     * Get person by username
     * @param string $username
     * @return TobiasAx_PersonModel
     */
    public function getPersonByUsername($username)
    {
        // request temp password
        $systemTempPassword = craft()->tobiasAx_user->getTemporaryPassword($username);

        // change password using temp password
        $tempPassword = StringHelper::randomString(12, true);
        craft()->tobiasAx_user->changePasswordByTemporaryPassword($username, $systemTempPassword, $tempPassword);

        // get person by temporary credentials
        $person = craft()->tobiasAx_person->getPersonByLogin($username, $tempPassword);

        return $person;
    }

    /**
     * Get person or user account emailaddress
     * @param TobiasAx_PersonModel $person
     * @return string
     */
    public function getPersonOrAccountEmail($person)
    {
        $email = $person->getCommunicationEmail();

        if ($email == null) {
            $user = $this->getUserByPersonId($person->getId());
            $email = $user ? $user->Email : $email;
        }

        return $email;
    }
}
