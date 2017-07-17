<?php
namespace Craft;

/**
 * Tobias - Advert type record
 */
class TobiasAx_AdvertTypeRecord extends BaseRecord
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'tobiasax_adverttype';
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'name'          => array(AttributeType::Name, 'required' => true),
            'handle'        => array(AttributeType::Handle, 'required' => true),
            'fieldLayoutId' => AttributeType::Number,
        );
    }

    /**
     * @return array
     */
    public function defineRelations()
    {
        return array(
            'fieldLayout' => array(static::BELONGS_TO, 'FieldLayoutRecord', 'onDelete' => static::SET_NULL),
            'advertisements'      => array(static::HAS_MANY, 'TobiasAx_AdvertisementRecord', 'elementId'),
        );
    }

    /**
     * @return array
     */
    public function defineIndexes()
    {
        return array(
            array('columns' => array('name'), 'unique' => true),
            array('columns' => array('handle'), 'unique' => true),
        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        return array(
            'ordered' => array('order' => 'name'),
        );
    }
}
