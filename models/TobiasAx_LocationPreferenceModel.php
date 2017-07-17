<?php
namespace Craft;

/**
 * TobiasAx - LocationPreference Model
 */
class TobiasAx_LocationPreferenceModel extends TobiasAx_EntityModel
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
            'Description' => AttributeType::String,
            'Id' => AttributeType::String,
            'Parent' => AttributeType::String,
            'Sort' => AttributeType::String,
            'Type' => AttributeType::String,
            'Value' => AttributeType::String
        ));
    }
}
