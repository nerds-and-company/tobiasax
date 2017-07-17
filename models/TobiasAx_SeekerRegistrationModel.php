<?php
namespace Craft;

/**
 * TobiasAx - Seeker registration model
 */
class TobiasAx_SeekerRegistrationModel extends TobiasAx_EntityModel
{
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Id' => AttributeType::String,
            'BuyRent' => AttributeType::String,
            'TypeId' => AttributeType::String,
            'Status' => AttributeType::String,
        ));
    }
}
