<?php
namespace Craft;

/**
 * TobiasAx - Person Role
 * @package Craft
 */
class TobiasAx_PersonRoleModel extends TobiasAx_EntityModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Description' => AttributeType::String,
            'Id' => AttributeType::String,
            'RelateId' => AttributeType::String,
            'TypeId' => AttributeType::Number
        ));
    }
}
