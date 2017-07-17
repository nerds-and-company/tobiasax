<?php

namespace Craft;

/**
 * TobiasAx - CoRegistrant Model
 */
class TobiasAx_CoRegistrantModel extends TobiasAx_PersonModel
{
    /**
     * Sets multiple attribute values at once.
     *
     * @param mixed $values
     *
     * @return null
     */
    public function setAttributes($values)
    {
        parent::setAttributes($values);

        if (!$values instanceof TobiasAx_CoRegistrantModel && $values instanceof TobiasAx_PersonModel) {
            $this->setAttribute('PersonId', $values->Id);
            $this->setAttribute('Id', null);
        }
    }

    public function getId()
    {
        return $this->Id;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'CoContractor' => AttributeType::Bool,
            'CoRegistrantType' => AttributeType::String,
            'Income' => AttributeType::Number,
            'Name' => AttributeType::String,
            'PersonId' => AttributeType::String,
            'RegistrationDateTime' => AttributeType::DateTime
        ));
    }
}
