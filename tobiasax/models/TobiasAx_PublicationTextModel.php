<?php
namespace Craft;

/**
 * TobiasAx - Publication text model
 */
class TobiasAx_PublicationTextModel extends TobiasAx_EntityModel
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
            'Id' => AttributeType::String,
            'Section' => AttributeType::String,
            'SequenceNr' => AttributeType::Number,
            'Text' => AttributeType::String,
            'Type' => AttributeType::String,
        ));
    }
}
