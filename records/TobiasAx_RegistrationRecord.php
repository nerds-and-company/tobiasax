<?php
namespace Craft;

/**
 * Tobias - Registration record
 */
class TobiasAx_RegistrationRecord extends BaseRecord
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'tobiasax_registration';
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'data' => AttributeType::Mixed,
            'paymentCode' => AttributeType::Number,
            'paymentStatus' => AttributeType::String,
        ));
    }
}
