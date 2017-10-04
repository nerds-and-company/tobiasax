<?php
namespace Craft;

/**
 * TobiasAX - Register model
 */
class TobiasAx_RegisterModel extends BaseModel
{
    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'id' => AttributeType::Number,
            'paymentCode' => AttributeType::Number,
            'registrationId' => AttributeType::String,
        ));
    }
}
