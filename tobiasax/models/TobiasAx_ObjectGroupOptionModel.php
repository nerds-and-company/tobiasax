<?php
namespace Craft;

/**
 * TobiasAx - ObjectGroupOption Model
 */
class TobiasAx_ObjectGroupOptionModel extends TobiasAx_EntityModel
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
            'Alert' => AttributeType::Bool,
            'CreatedDateTime' => AttributeType::DateTime,
            'EndDateTime' => AttributeType::DateTime,
            'Id' => AttributeType::String,
            'NewlyBuilt' => AttributeType::Bool,
            'ObjectGroup' => array(AttributeType::ClassName, 'models' => 'TobiasAx_ObjectGroupModel'),
            'Option' => AttributeType::Bool,
            'ReasonDroppedId' => AttributeType::String,
            'RenewalDate' => AttributeType::DateTime,
            'SeekerRegistration' => array(AttributeType::ClassName, 'models' => 'TobiasAx_RegistrationModel'),
            'Sequence' => AttributeType::Number,
            'Status' => AttributeType::String
        ));
    }
}
