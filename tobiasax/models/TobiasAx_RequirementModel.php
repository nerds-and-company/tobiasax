<?php
namespace Craft;

/**
 * TobiasAx - Requirement Model
 */
class TobiasAx_RequirementModel extends TobiasAx_EntityModel
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
            'AttributeId' => AttributeType::String,
            'Id' => AttributeType::String,
            'Operator' => AttributeType::String,
            'Type' => AttributeType::String,
            'Unit' => AttributeType::String,
            'ValueEnd' => AttributeType::String,
            'ValueStart' => AttributeType::String,
            'ValueType' => AttributeType::String
        ));
    }
}
