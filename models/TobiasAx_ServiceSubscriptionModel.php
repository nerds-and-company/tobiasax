<?php
namespace Craft;

/**
 * TobiasAx - ServiceSubscription Model
 */
class TobiasAx_ServiceSubscriptionModel extends TobiasAx_EntityModel
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
            'Active' => AttributeType::Bool,
            'End' => AttributeType::DateTime,
            'Id' => AttributeType::String,
            'Service' => array(AttributeType::ClassName, 'model' => 'TobiasAx_ServiceModel'),
            'Start' => AttributeType::DateTime
        ));
    }
}
