<?php
namespace Craft;

/**
 * Class TobiasAx_EntityModel
 * @package Craft
 */
abstract class TobiasAx_EntityModel extends TobiasAx_BaseModel
{
    /**
     * Defines the attributes for an address
     * @access protected
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'CompanyId' => array(AttributeType::String, 'default' => craft()->config->get('tobiasAxCompanyId')),
        ));
    }
}
