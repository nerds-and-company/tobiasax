<?php

namespace Craft;

/**
 * Tobias AX - property seeker connector service
 */
class TobiasAx_PropertySeekerConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Gets a property seeker by person ID
     * @param string $personId
     * @param array $data additional template data
     * @return GuzzleClient
     */
    public function getPropertySeekerByPersonId($personId, $data = [])
    {
        $data = array_merge([
            'personId' => $personId,
            'expand' => 'Addresses,Communications,Incomes,SeekerRegistrations,SeekerRegistrations/CoRegistrants,' .
                'SeekerRegistrations/CoRegistrants/Communications,SeekerRegistrations/CoRegistrants/Addresses,' .
                'SeekerRegistrations/CoRegistrants/incomes',
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetPropertySeekerByPersonId',
            'tobiasax/templates/soap/property_seeker/get_by_person_id',
            $data
        );

        return $request;
    }
}
