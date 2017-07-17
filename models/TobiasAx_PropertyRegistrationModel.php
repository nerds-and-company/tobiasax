<?php
namespace Craft;

class TobiasAx_PropertyRegistrationModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'DateAvailable' => array(AttributeType::DateTime),
            'DateContract' => array(AttributeType::DateTime),
            'DateEmpty' => array(AttributeType::DateTime),
            'Id' => array(AttributeType::String),
            'PublicationStatus' => array(AttributeType::String),
            'RealEstateObject' => array(AttributeType::Mixed, 'model' => 'TobiasAx_RealEstateModel'),
        ));
    }
}
