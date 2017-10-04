<?php
namespace Craft;

class TobiasAx_PublicationModel extends TobiasAx_EntityModel
{
    /**
     * @return TobiasAx_RealEstateModel
     */
    public function getRealEstateObject()
    {
        return $this->PropertyRegistration->RealEstateObject;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Components' => array(AttributeType::ClassName, 'models' => 'Craft\TobiasAx_PublicationComponentModel', 'default' => array()),
            'EligibleRent' => array(AttributeType::Number, 'decimals' => 2),
            'End' => array(AttributeType::DateTime),
            'FreeForInternet' => array(AttributeType::Bool),
            'GrossRent' => array(AttributeType::Number, 'decimals' => 2),
            'Id' => array(AttributeType::String),
            'MinimumSellingPrice' => array(AttributeType::Number, 'decimals' => 2),
            'NetRent' => array(AttributeType::Number),
            'NrOfParticipations' => array(AttributeType::Number),
            'Number' => array(AttributeType::Number),
            'PropertyRegistration' => array(AttributeType::Mixed, 'model' => 'TobiasAx_PropertyRegistrationModel'),
            'PublicationTexts' => array(AttributeType::ClassName, 'models' => 'Craft\TobiasAx_PublicationTextModel', 'default' => array()),
            'RegistrationType' => array(AttributeType::String),
            'ScenarioId' => array(AttributeType::String),
            'SellingPrice' => array(AttributeType::Number, 'decimals' => 2),
            'Sequence' => array(AttributeType::Number),
            'SortingType' => array(AttributeType::String),
            'Start' => array(AttributeType::DateTime),
            'Status' => array(AttributeType::String),
            'Type' => array(AttributeType::String),
        ));
    }
}
