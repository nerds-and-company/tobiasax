<?php
namespace Craft;

/**
 * Class TobiasAx_ImportModel
 * @SuppressWarnings("complexity")
 */
class TobiasAx_ImportModel extends TobiasAx_ImportBaseModel
{
    /**
     * @return string
     */
    public function getCity()
    {
        return ucfirst(strtolower($this->Publication->PropertyRegistration->RealEstateObject->AddressCity));
    }

    /**
     * @return string
     */
    public function getHouseNumber()
    {
        return trim($this->Publication->PropertyRegistration->RealEstateObject->AddressHouseNumber) . $this->getHouseNumberAddition();
    }

    /**
     * @return string
     */
    protected function getHouseNumberAddition()
    {
        return trim(strval($this->Publication->PropertyRegistration->RealEstateObject->AddressHouseNumberAddition));
    }

    /**
     * @return string
     */
    public function getZipcode()
    {
        return strtoupper(trim($this->Publication->PropertyRegistration->RealEstateObject->AddressZipcode));
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return trim($this->Publication->PropertyRegistration->RealEstateObject->AddressStreet);
    }

    /**
     * @return int
     */
    public function getConstructionYear()
    {
        return intval($this->Publication->PropertyRegistration->RealEstateObject->BuiltYear);
    }

    /**
     * @return string
     */
    public function getBuildingType()
    {
        return $this->Publication->PropertyRegistration->RealEstateObject->Housing;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return floatval($this->Publication->GrossRent);
    }

    /**
     * @return float
     */
    public function getNettoRent()
    {
        return floatval($this->Publication->NetRent);
    }

    /**
     * @return float
     */
    public function getEligibleRent()
    {
        return floatval($this->Publication->EligibleRent)
    }

    /**
     * @return DateTime
     */
    public function getAvailableFrom()
    {
        return $this->Publication->PropertyRegistration->DateAvailable;
    }

    /**
     * @return DateTime
     */
    public function getAdvertisementDate()
    {
        return $this->Publication->Start;
    }

    /**
     * Returns last response date minus 1 second.
     *
     * This is necessary because we receive a DateTime with the time set to
     * 00:00, but only show the date to the user. So we allow responses until
     * the end of the previous day.
     *
     * @return DateTime
     */
    public function getLastResponseDate()
    {
        $endDate = $this->Publication->End;
        return $endDate->modify("-1 seconds");
    }

    /**
     * @return string
     */
    public function getEnergyLabel()
    {
        return $this->getBuildingAttributeValueById('Energielabel (definitief)');
    }

    /**
     * @return boolean
     */
    public function getElevator()
    {
        return $this->getBuildingAttributeValueById('Lift aanwezig') === 'Ja';
    }

    /**
     * @return boolean
     */
    public function getCentralHeating()
    {
        return $this->getBuildingAttributeValueById('CV aanwezig') === 'Ja';
    }

    /**
     * @return string
     */
    public function getThermopane()
    {
        $thermopane = $this->getBuildingAttributeValueById('Dubbel glas');

        if (empty($thermopane) || $thermopane === 'Nee') {
            return null;
        } else {
            return $thermopane;
        }
    }

    /**
     * @return int
     */
    public function getNumberBedroomsGroundFloor()
    {
        return (int) $this->getBuildingAttributeValueById('Slaapkamers begane grond');
    }

    /**
     * @return int
     */
    public function getBedrooms()
    {
        return (int) $this->getBuildingAttributeValueById('AANTSLKAM');
    }

    /**
     * @return int
     */
    public function getLivingRoomSize()
    {
        return (int)$this->getBuildingAttributeValueById('Woonkamer');
    }

    /**
     * @return boolean
     */
    public function getGarden()
    {
        $gardenLayout = $this->getGardenLayout();

        return isset($gardenLayout);
    }

    /**
     * @return string
     */
    public function getGardenLayout()
    {
        $gardenLayout = $this->getBuildingAttributeValueById('Tuin');

        if (empty($gardenLayout) || $gardenLayout == 'Geen tuin') {
            return null;
        } else {
            return $gardenLayout;
        }
    }

    /**
     * @return string
     */
    public function getBalconyLayout()
    {
        $balconyLayout = $this->getBuildingAttributeValueById('Balkon');

        if (empty($balconyLayout) || $balconyLayout == 'Geen balkon') {
            return null;
        } else {
            return $balconyLayout;
        }
    }

    /**
     * @return float
     */
    public function getServiceFee()
    {
        return $this->getComponentValueByName('Servicekosten');
    }

    /**
     * @return float
     */
    public function getHeatingCosts()
    {
        return $this->getComponentValueByName('Stookkosten');
    }
}
