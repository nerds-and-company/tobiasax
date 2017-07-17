<?php
namespace Craft;

/**
 * TobiasAx - Asset Model
 */
class TobiasAx_AssetModel extends TobiasAx_EntityModel
{
    /**
     * Format asset path using publication
     * @param TobiasAx_PublicationModel $publication
     */
    public function getAssetPath($publication)
    {
        $realEstateObject = $publication->getRealEstateObject();

        return implode('/', $this->getUrlSegments($realEstateObject));
    }

    /**
     * Return URL segments
     * @param TobiasAx_RealEstateModel $realEstateObject
     * @return array
     */
    public function getUrlSegments($realEstateObject)
    {
        $segments = [
            'photo',
            $realEstateObject->AddressCity,
            $realEstateObject->AddressStreet,
            $realEstateObject->AddressHouseNumber.$realEstateObject->AddressHouseNumberAddition,
            $this->Filename,
        ];

        return $segments;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Checksum' => AttributeType::String,
            'Filename' => AttributeType::String,
            'Id' => AttributeType::String,
            'Name' => AttributeType::String,
            'Notes' => AttributeType::String,
            'Type' => AttributeType::String,
            'Publication' => array(AttributeType::Mixed, 'model' => 'TobiasAx_PublicationModel'),
        ));
    }
}
