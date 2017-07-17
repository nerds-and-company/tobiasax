<?php

namespace Craft;

/**
 * Tobias AX address connector service
 */
class TobiasAx_AddressConnectorService extends TobiasAx_ConnectorService
{

    /**
     * @param  string $zipcode zipcode
     * @param string $housenumber house number
     * @param  string $data additional template data
     * @return GuzzleClient
     */
    public function getByZipcodeAndHouseNumber($zipcode, $houseNumber, $data = [])
    {
        $data = array_merge([
            'zipcode' => $zipcode,
            'houseNumber' => $houseNumber,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetAddressByZipcodeAndHouseNumber',
            'tobiasax/templates/soap/address/get_by_zipcode_and_housenumber',
            $data
        );

        return $request;
    }

    /**
     * Updates an address
     * @param TobiasAx_AddressModel $address The person address to update
     * @return GuzzleClient
     */
    public function updateAddress($address)
    {
        $data = [
            'address' => $address->getUpdateAttributes()
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'UpdateAddress',
            'tobiasax/templates/soap/address/update',
            $data
        );

        return $request;
    }
}
