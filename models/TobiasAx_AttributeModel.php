<?php
namespace Craft;

class TobiasAx_AttributeModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Category' => array(AttributeType::String),
            'Id' => array(AttributeType::String),
            'Name' => array(AttributeType::String),
            'ObjectType' => array(AttributeType::String),
            'Type' => array(AttributeType::String),
            'Unit' => array(AttributeType::String),
            'Value' => array(AttributeType::String),
        ));
    }
}
