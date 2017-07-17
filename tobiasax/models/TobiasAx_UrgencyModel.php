<?php
namespace Craft;

/**
 * TobiasAx - Urgency Model
 */
class TobiasAx_UrgencyModel extends TobiasAx_EntityModel
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
            'GrantDate' => AttributeType::DateTime,
            'Id' => AttributeType::String,
            'ReasonId' => AttributeType::String,
            'RequestDate' => AttributeType::DateTime,
            'SeekerRegistrationId' => AttributeType::String,
            'UrgencyId' => AttributeType::String,
            'ValidUntilDate' => AttributeType::DateTime
        ));
    }
}
