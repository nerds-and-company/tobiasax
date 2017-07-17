<?php
namespace Craft;

/**
 * Tobias - Advert type model
 */
class TobiasAx_AdvertTypeModel extends BaseModel
{
    /**
     * Use the translated advert type name as the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return Craft::t($this->name);
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'id'            => AttributeType::Number,
            'name'          => AttributeType::String,
            'handle'        => AttributeType::String,
            'fieldLayoutId' => AttributeType::Number,
        );
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return array(
            'fieldLayout' => new FieldLayoutBehavior('TobiasAx_Advertisement'),
        );
    }
}
