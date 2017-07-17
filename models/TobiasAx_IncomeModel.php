<?php
namespace Craft;

/**
 * TobiasAx - Income
 * @package Craft
 */
class TobiasAx_IncomeModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Amount' => AttributeType::Number,
            'GrossNet' => AttributeType::String,
            'Id' => AttributeType::String,
            'Period' => AttributeType::String,
            'Type' => AttributeType::String
        ));
    }
}
