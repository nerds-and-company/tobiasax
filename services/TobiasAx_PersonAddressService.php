<?php

namespace Craft;

use Exception;

/**
 * Tobias AX Person Address service
 */
class TobiasAx_PersonAddressService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const EXCEPTION_ALREADY_EXISTS = "Er bestaat al een adres van het adrestype";

    /**
     * @var string
     */
    const ERROR_ALREADY_EXISTS = "Address already exists";

    /**
     * @var string
     */
    const ERROR_CREATE_UNKNOWN = "Error creating address";

    /**
     * @param TobiasAx_AddressModel[] $addresses The addresses to add to the person
     * @param string $personId The id of the person to add the address to
     * @return TobiasAx_AddressModel[]
     * @throws TobiasAx_SoapException $e Thrown when a person address can't be created
     */
    public function createPersonAddresses($addresses, $personId)
    {
        $responseAddresses = array();
        foreach ($addresses as $address) {
            $responseAddress = $this->createPersonAddress($address, $personId);
            $responseAddresses[] = $responseAddress;
        }

        return $responseAddresses;
    }

    /**
     * @param TobiasAx_AddressModel $address The address to add to the person
     * @param string $personId The id of the person to add the address to
     * @return TobiasAx_AddressModel
     * @throws TobiasAx_SoapException $e Thrown when a person address can't be created
     */
    public function createPersonAddress($address, $personId)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->createPersonAddress($address, $personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:CreatePersonAddressResponse/xmlns:CreatePersonAddressResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_ALREADY_EXISTS)) {
                throw new TobiasAx_SoapException(static::ERROR_ALREADY_EXISTS, null, $e);
            }
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN, null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN, null, $e);
        }

        return new TobiasAx_AddressModel($result);
    }

    /**
     * Creates or updates multiple person addresses
     * @param TobiasAx_AddressModel[] $addresses The addresses to update
     * @param string $personId
     * @return TobiasAx_AddressModel[]
     * @throws TobiasAx_SoapException $e Thrown when a person address can't be created
     */
    public function upsertPersonAddresses($addresses, $personId)
    {
        $responseAddresses = array();
        foreach ($addresses as $address) {
            if (empty($address->Id)) {
                $responseAddress = $this->createPersonAddress($address, $personId);
            } else {
                $responseAddress = craft()->tobiasAx_address->updateAddress($address);
            }
            $responseAddresses[] = $responseAddress;
        }

        return $responseAddresses;
    }

    /**
     * Checks if the address of this person matches the address of the given person
     * @param TobiasAx_PersonModel $person
     * @param TobiasAx_PersonModel $otherPerson
     * @return bool
     */
    public function personAddressMatches($person, $otherPerson)
    {
        if (!isset($person) || !isset($otherPerson)) {
            return true;
        }

        $addresses = $person->Addresses;
        $otherAddresses = $otherPerson->Addresses;

        if (!empty($addresses) && !empty($otherAddresses)) {
            $address = array_shift($addresses);
            $otherAddress = array_shift($otherAddresses);

            return $address->Zipcode == $otherAddress->Zipcode && $address->HouseNumber == $otherAddress->HouseNumber;
        } else if (empty($addresses) && empty($otherAddresses)) {
            return true;
        }

        return false;
    }
}
