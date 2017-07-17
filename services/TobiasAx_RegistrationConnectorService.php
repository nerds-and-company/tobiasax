<?php

namespace Craft;

/**
 * Tobias AX registration connector service
 */
class TobiasAx_RegistrationConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Default entity expand
     * @var string
     */
    const DEFAULT_EXPAND = 'PropertySeeker';

    /**
     * Creates a registration
     * @param TobiasAx_RegistrationModel $registration The registration to create
     * @param string $data additional template data
     * @return GuzzleClient
     */
    public function createRegistration($registration, $data = [])
    {
        $data = array_merge([
            'registration' => $registration->getCreateAttributes(),
            'expand' => static::DEFAULT_EXPAND,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'CreateSeekerRegistration',
            'tobiasax/templates/soap/registration/create',
            $data
        );

        return $request;
    }

    /**
     * Updates a registration
     * @param TobiasAx_RegistrationModel $registration The registration to update
     * @return GuzzleClient
     */
    public function updateRegistration($registration)
    {
        $data = array_merge([
            'registration' => $registration->getUpdateAttributes()
        ]);

        $request = craft()->tobiasAx_request->createRequest(
            'UpdateSeekerRegistration',
            'tobiasax/templates/soap/registration/update',
            $data
        );

        return $request;
    }

    /**
     * Creates a co-registrant
     * @param TobiasAx_CoRegistrantModel $coRegistrant
     * @param string $registrationId
     * @return GuzzleClient
     */
    public function createCoRegistrant($coRegistrant, $registrationId)
    {
        $coRegistrantPerson = array();
        $coRegistrantPerson['Birthdate'] = $coRegistrant->Birthdate;
        $coRegistrantPerson['Gender'] = $coRegistrant->Gender;
        $coRegistrantPerson['Type'] = $coRegistrant->Type;

        $coRegistrantRegistration = array();
        $coRegistrantRegistration['CoContractor'] = $coRegistrant->CoContractor;
        $coRegistrantRegistration['CoRegistrantType'] = $coRegistrant->CoRegistrantType;
        $coRegistrantRegistration['Income'] = $coRegistrant->Income;
        $coRegistrantRegistration['PersonId'] = $coRegistrant->PersonId;

        $data = [
            'coregistrantPerson' => $coRegistrantPerson,
            'coregistrantRegistration' => $coRegistrantRegistration,
            'registrationId' => $registrationId
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'CreateCoRegistrant',
            'tobiasax/templates/soap/registration/create_coregistrant',
            $data
        );

        return $request;
    }

    /**
     * Deletes a co-registrant
     * @param string $coRegistrantId
     * @return GuzzleClient
     */
    public function deleteCoRegistrant($coRegistrantId)
    {
        $data = ['coRegistrantId' => $coRegistrantId];

        $request = craft()->tobiasAx_request->createRequest(
            'DeleteCoRegistrant',
            'tobiasax/templates/soap/registration/delete_coregistrant',
            $data
        );

        return $request;
    }
}
