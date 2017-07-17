<?php

namespace Craft;

use Exception;

/**
 * Tobias AX Address service
 */
class TobiasAx_AddressService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const UNKNOWN_ERROR = 'An unknown error occurred: ';

    /**
     * @var string
     */
    const ERROR_UPDATE_UNKNOWN = 'Unknown error updating address: ';

    /**
     * @param  string $zipcode zipcode
     * @param string $houseNumber house number
     * @return TobiasAx_AddressModel
     * @throws Exception $e Thrown when an error occured while retrieving the person
     */
    public function getByZipcodeAndHouseNumber($zipcode, $houseNumber)
    {
        $service = craft()->tobiasAx_addressConnector;

        try {
            $envelope = $service->sendRequest($service->getByZipcodeAndHouseNumber($zipcode, $houseNumber));
            $result = $service->extractSingle($envelope, 'Body/xmlns:GetAddressByZipcodeAndHouseNumberResponse/xmlns:GetAddressByZipcodeAndHouseNumberResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::UNKNOWN_ERROR . $e->getMessage(), 0, $e);
        }

        return new TobiasAx_AddressModel($result);
    }

    /**
     * Updates multiple addresses
     * @param TobiasAx_AddressModel[] $addresses The addresses to update
     * @return TobiasAx_AddressModel[]
     * @throws TobiasAx_SoapException $e Thrown when a person address can't be created
     */
    public function updateAddresses($addresses)
    {
        $responseAddresses = array();
        foreach ($addresses as $address) {
            $responseAddress = $this->updateAddress($address);
            $responseAddresses[] = $responseAddress;
        }

        return $responseAddresses;
    }

    /**
     * Updates a single address
     * @param TobiasAx_AddressModel $address The address to update
     * @return TobiasAx_AddressModel
     * @throws TobiasAx_SoapException $e Thrown when a person address can't be updated
     */
    public function updateAddress($address)
    {
        $service = craft()->tobiasAx_addressConnector;

        try {
            $envelope = $service->sendRequest($service->updateAddress($address));
            $result = $service->extractSingle($envelope, 'Body/xmlns:UpdateAddressResponse/xmlns:UpdateAddressResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_UPDATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_AddressModel($result);
    }
}
