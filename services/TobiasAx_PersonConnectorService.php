<?php

namespace Craft;

/**
 * Tobias AX person connector service
 */
class TobiasAx_PersonConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Person not found error message
     * @var string
     */
    const ERROR_PERSON_NOTFOUND = 'Ophalen persoon mislukt';

    /**
     * Creates a person
     * @param TobiasAx_PersonModel $person The person to create
     * @return GuzzleClient
     */
    public function createPerson($person)
    {
        $data = [
            'person' => $person->getCreateAttributes()
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'CreatePerson',
            'tobiasax/templates/soap/person/create',
            $data
        );

        return $request;
    }

    /**
     * Updates a person
     * @param TobiasAx_PersonModel $person The person to update
     * @return GuzzleClient
     */
    public function updatePerson($person)
    {
        $data = [
            'person' => $person->getUpdateAttributes()
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'UpdatePerson',
            'tobiasax/templates/soap/person/update',
            $data
        );

        return $request;
    }

    /**
     * Creates a person address
     * @param TobiasAx_AddressModel $address The person address to create
     * @param string $personId The id of the person to link the address to
     * @return GuzzleClient
     */
    public function createPersonAddress($address, $personId)
    {
        $data = [
            'address' => $address->getCreateAttributes(),
            'personId' => $personId
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'CreatePersonAddress',
            'tobiasax/templates/soap/person/create_address',
            $data
        );

        return $request;
    }

    /**
     * Creates a person income
     * @param TobiasAx_IncomeModel $income The person income to create
     * @param string $personId The id of the person to link the address to
     * @return GuzzleClient
     */
    public function createPersonIncome($income, $personId)
    {
        $data = [
            'income' => $income->getCreateAttributes(),
            'personId' => $personId
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'CreatePersonIncome',
            'tobiasax/templates/soap/person/create_income',
            $data
        );

        return $request;
    }

    /**
     * Creates a person communication
     * @param TobiasAx_CommunicationModel $communication The person communication to create
     * @param string $personId The id of the person to link the address to
     * @return GuzzleClient
     */
    public function createPersonCommunication($communication, $personId)
    {
        $data = [
            'communication' => $communication->getCreateAttributes(),
            'personId' => $personId
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'CreatePersonCommunication',
            'tobiasax/templates/soap/person/create_communication',
            $data
        );

        return $request;
    }

    /**
     * Gets person by username and password
     * @param  string $username user login name
     * @param  string $password user login password
     * @param  string $data additional template data
     * @return GuzzleClient
     */
    public function getPersonByLogin($username, $password, $data = [])
    {
        $data = array_merge([
            'username' => $username,
            'password' => $password,
            'expand' => 'Communications,Addresses',
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetPersonByLogin',
            'tobiasax/templates/soap/person/get_person_by_login',
            $data
        );

        return $request;
    }

    /**
     * @param  string $personId user id
     * @param  string $data additional template data
     * @return GuzzleClient
     */
    public function getPersonById($personId, $data = [])
    {
        $data = array_merge([
            'id' => $personId,
            'expand' => 'Communications,Addresses,Incomes',
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetPerson',
            'tobiasax/templates/soap/person/get_person',
            $data
        );

        return $request;
    }

    /**
     * Gets user by person id
     * @param  string $personId person id
     * @param  string $data additional template data
     * @return GuzzleClient
     */
    public function getUserByPersonId($personId, $data = [])
    {
        $data = array_merge([
            'id' => $personId,
            'expand' => '',
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetUserByPersonId',
            'tobiasax/templates/soap/person/get_user_by_person_id',
            $data
        );

        return $request;
    }
}
