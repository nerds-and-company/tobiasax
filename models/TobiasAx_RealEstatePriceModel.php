<?php
namespace Craft;

class TobiasAx_RealEstatePriceModel extends TobiasAx_EntityModel
{
    /**
     * @return string
     */
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
            'Adaptable' => AttributeType::Bool,
            'Amount' => array(AttributeType::Number, 'decimals' => 2),
            'CostGroup' => AttributeType::String,
            'CostTypeId' => AttributeType::String,
            'CostTypeName' => AttributeType::String,
            'Id' => AttributeType::String,
            'TaxGroup' => AttributeType::String,
            'TypeCosts' => AttributeType::String,
            'ValidFrom' => AttributeType::DateTime,
            'ValidTo' => AttributeType::DateTime,
        ));
    }
}
