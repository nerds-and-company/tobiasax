<?php
namespace Craft;

/**
 * TobiasAx - Bank account
 * @package Craft
 */
class TobiasAx_BankAccountModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'BIC' => AttributeType::String,
            'Default' => AttributeType::Bool,
            'G_account' => AttributeType::Bool,
            'IBAN' => AttributeType::String,
            'Id' => AttributeType::String,
            'Name' => AttributeType::String,
            'Status' => AttributeType::String,
            'ValidationMethod' => AttributeType::String
        ));
    }
}
