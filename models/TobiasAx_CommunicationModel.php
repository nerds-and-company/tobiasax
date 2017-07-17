<?php
namespace Craft;

/**
 * TobiasAx - Communication
 * @package Craft
 */
class TobiasAx_CommunicationModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Id' => AttributeType::String,
            'MethodType' => AttributeType::String,
            'Secret' => AttributeType::Bool,
            'Type' => AttributeType::String,
            'MethodType' => AttributeType::String,
            'MunicipalityId' => AttributeType::String,
            'Name' => AttributeType::String,
            'Street' => AttributeType::String,
            'Type' => AttributeType::String,
            'Value' => AttributeType::String
        ));
    }
}
