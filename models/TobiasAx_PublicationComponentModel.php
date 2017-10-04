<?php
namespace Craft;

/**
 * TobiasAx - Publication component model
 */
class TobiasAx_PublicationComponentModel extends TobiasAx_EntityModel
{
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return floatval($this->Value);
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Description' => AttributeType::String,
            'Id' => AttributeType::String,
            'Value' => array(AttributeType::Number, 'decimals' => 2),
        ));
    }
}
