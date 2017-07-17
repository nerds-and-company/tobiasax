<?php
namespace Craft;

/**
 * Defines attributes for this model
 * @return array
 */
class TobiasAx_AddressModel extends TobiasAx_EntityModel
{
    /**
     * Defines the attributes for an address
     * @access protected
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'City' => AttributeType::String,
            'Country' => array(AttributeType::String, 'exclude'=>[TobiasAX_ModelScenario::GET]),
            'HouseNumber' => AttributeType::Number,
            'HouseNumberAddition' => AttributeType::String,
            'Id' => array(AttributeType::String,'exclude'=>[TobiasAX_ModelScenario::GET]),
            'IsPrimary' => array(AttributeType::Bool,'exclude'=>[TobiasAX_ModelScenario::GET]),
            'MunicipalityId' => array(AttributeType::String,'exclude'=>[TobiasAX_ModelScenario::GET]),
            'Name' => array(AttributeType::String,'exclude'=>[TobiasAX_ModelScenario::GET]),
            'Street' => AttributeType::String,
            'Type' => array(AttributeType::String,'exclude'=>[TobiasAX_ModelScenario::GET]),
            'Zipcode' => AttributeType::String
        ));
    }
}
