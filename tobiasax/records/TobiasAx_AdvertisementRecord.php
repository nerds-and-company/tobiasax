<?php
namespace Craft;

/**
 * Tobias - Advertisement record
 */
class TobiasAx_AdvertisementRecord extends BaseRecord
{
    /**
     * @return string
     */
    public function getTableName()
    {
        return 'tobiasax_advertisement';
    }

    /**
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array(
            'status' => array(AttributeType::String, 'required' => true, "default" => TobiasAx_AdvertisementModel::LIVE),
            'tobiasId' => AttributeType::String,
            'slug' => AttributeType::String
        );
    }

    /**
     * @return array
     */
    public function defineRelations()
    {
        return array(
            'element' => array(static::BELONGS_TO, 'ElementRecord', 'id', 'required' => true, 'onDelete' => static::CASCADE),
            'advertType' => array(static::BELONGS_TO, 'TobiasAx_AdvertTypeRecord', 'required' => true, 'onDelete' => static::CASCADE),
            'author' => array(static::BELONGS_TO, 'UserRecord', 'onDelete' => static::CASCADE)
        );
    }
}
