<?php

namespace Craft;

/**
 * Tobias AX registration service
 */
class TobiasAx_RegistrationService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const UNKNOWN_ERROR = "Unknown error retrieving registration: ";

    /**
     * @var string
     */
    const ERROR_CREATE_UNKNOWN = 'Unknown error creating ';

    /**
     * @var string
     */
    const ERROR_UPDATE_UNKNOWN = 'Unknown error updating registration: ';

    /**
     * Get registration by person ID and status
     * @param string $personId
     * @param TobiasAX_RegistrationStatus $status
     * @return TobiasAx_SeekerRegistrationModel[]
     */
    public function getRegistrationsByPerson($personId, $status = TobiasAX_RegistrationStatus::ACTIVE)
    {
        $propertySeeker = craft()->tobiasAx_propertySeeker->getPropertySeekerByPersonId($personId);

        if ($propertySeeker == null) {
            return [];
        }

        $registrations = $propertySeeker->getRegistrationsByStatus($status);

        return $registrations;
    }

    /**
     * Gets most recent and active registration ID
     * @param string $personId
     * @return string|null
     */
    public function getActiveRegistrationId($personId)
    {
        $registrationId = null;
        $registrations = $this->getRegistrationsByPerson($personId, TobiasAX_RegistrationStatus::ACTIVE);

        if (count($registrations) > 0) {
            $registrationId = array_shift($registrations)->getId();
        }

        return $registrationId;
    }

    /**
     * @param int $registerId internal stored registration id
     * @param TobiasAx_RegistrationModel $registration
     * @return TobiasAx_RegistrationModel
     * @throws TobiasAx_SoapException $e Thrown when a registration can't be created
     */
    public function createRegistration($registerId, $registration)
    {
        $coRegistrants = $registration->CoRegistrants;
        $service = craft()->tobiasAx_registrationConnector;

        try {
            $envelope = $service->sendRequest($service->createRegistration($registration));
            $result = $service->extractSingle($envelope, 'Body/xmlns:CreateSeekerRegistrationResponse/xmlns:CreateSeekerRegistrationResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . 'registration: ' . $e->getMessage(), null, $e);
        }

        $registration = new TobiasAx_RegistrationModel($result);

        // store external registation id for future reference
        craft()->tobiasAx_registrationStore->saveRegistrationId($registerId, $registration->Id);

        // add co-registrants to created registration
        foreach ($coRegistrants as $coRegistrant) {
            $this->createCoRegistrant($coRegistrant, $registration->Id);
        }

        return $registration;
    }

    /**
     * Updates an existing registration
     * @param TobiasAx_SeekerRegistrationModel $registration
     * @return TobiasAx_RegistrationModel
     * @throws TobiasAx_SoapException $e
     * @throws Exception
     */
    public function updateRegistration($registration)
    {
        $service = craft()->tobiasAx_registrationConnector;

        try {
            $envelope = $service->sendRequest($service->updateRegistration($registration));

            $result = $service->extractSingle($envelope, 'Body/xmlns:UpdateSeekerRegistrationResponse/xmlns:UpdateSeekerRegistrationResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_UPDATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_RegistrationModel($result);
    }

    /**
     * @param TobiasAx_CoRegistrantModel $coRegistrant
     * @return TobiasAx_CoRegistrantModel
     * @throws TobiasAx_SoapException $e Thrown when a coregistrant can't be created
     */
    public function createCoRegistrant($coRegistrant, $registrationId)
    {
        $service = craft()->tobiasAx_registrationConnector;

        try {
            $envelope = $service->sendRequest($service->createCoRegistrant($coRegistrant, $registrationId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:CreateCoRegistrantResponse/xmlns:CreateCoRegistrantResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . 'CoRegistrant: ' . $e->getMessage(), null, $e);
        }

        return new TobiasAx_CoRegistrantModel($result);
    }

    /**
     * @param string $coRegistrantId
     * @return null
     * @throws TobiasAx_SoapException $e thrown when co-registrant can't be deleted
     */
    public function deleteCoRegistrant($coRegistrantId)
    {
        $service = craft()->tobiasAx_registrationConnector;

        try {
            $service->sendRequest($service->deleteCoRegistrant($coRegistrantId));
        } catch (Exception $e) {
            $message = Craft::t('Unkown error deleting co-registrant “{coRegistrantId}”. {message}.', ['coRegistrantId' => $coRegistrantId, 'message' => $e->getMessage()]);
            throw new TobiasAx_SoapException($message, null, $e);
        }
    }
}
