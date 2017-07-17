<?php
namespace Craft;

/**
 * TobiasAx - Points Model
 */
class TobiasAx_PointsModel extends TobiasAx_EntityModel
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
            'Age' => AttributeType::Number,
            'Children' => AttributeType::Number,
            'DynamicPoints' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'TobiasAx_PointsModel'),
            'FamilySize' => AttributeType::Number,
            'Id' => AttributeType::String,
            'OccupancyPeriodBuyer' => AttributeType::Number,
            'OccupancyPeriodOwnBuyer' => AttributeType::Number,
            'OccupancyPeriodOwnTenant' => AttributeType::Number,
            'RefusalPenalty' => AttributeType::Number,
            'Refusals' => AttributeType::Number,
            'RegistrationPeriod' => AttributeType::Number,
            'Urgencies' => AttributeType::Number
        ));
    }
}
