<?php
namespace Craft;

class TobiasAx_RealEstateModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'AddressCity' => array(AttributeType::String),
            'AddressHouseNumber' => array(AttributeType::Number),
            'AddressHouseNumberAddition' => array(AttributeType::String),
            'AddressMunicipalityId' => array(AttributeType::String),
            'AddressStreet' => array(AttributeType::String),
            'AddressZipcode' => array(AttributeType::String),
            'BuildingCategory' => array(AttributeType::String),
            'BuildingId' => array(AttributeType::String),
            'BuildingType' => array(AttributeType::String),
            'BuiltYear' => array(AttributeType::Number),
            'ContractObjectCategoryName' => array(AttributeType::String),
            'ContractObjectTypeName' => array(AttributeType::String),
            'Destination' => array(AttributeType::String),
            'District' => array(AttributeType::String),
            'EligibleRent' => array(AttributeType::Number, 'decimals' => 2),
            'FinancialComplex' => array(AttributeType::String),
            'GrossRent' => array(AttributeType::Number),
            'GroundId' => array(AttributeType::String),
            'Housing' => array(AttributeType::String),
            'Id' => array(AttributeType::String),
            'MaximumRentAmount' => array(AttributeType::Number, 'decimals' => 2),
            'MaximumRentAmountFuture' => array(AttributeType::Number, 'decimals' => 2),
            'Name' => array(AttributeType::String),
            'Neighborhood' => array(AttributeType::String),
            'NetRent' => array(AttributeType::Number, 'decimals' => 2),
            'OwnerShipType' => array(AttributeType::String),
            'PlotType' => array(AttributeType::String),
            'Prices' => array(AttributeType::ClassName, 'models' => 'Craft\TobiasAx_RealEstatePriceModel', 'default' => []),
            'RenovationYear' => array(AttributeType::Number),
            'TechnicalComplex' => array(AttributeType::String),
            'Type' => array(AttributeType::String),
            'addressCountryRegionId' => array(AttributeType::String),
        ));
    }
}
