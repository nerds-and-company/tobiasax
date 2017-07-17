<?php
namespace Craft;

/**
 * TobiasAx - Service Model
 */
class TobiasAx_ServiceModel extends TobiasAx_EntityModel
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
            'DefaultActive' => AttributeType::Bool,
            'Description' => AttributeType::String,
            'Fee' => AttributeType::Number,
            'Id' => AttributeType::String,
            'Periods' => AttributeType::Number,
            'PeriodType' => AttributeType::String
        ));
    }
}
