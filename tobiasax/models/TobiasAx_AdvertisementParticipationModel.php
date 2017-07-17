<?php
namespace Craft;

/**
 * Tobias AX - advertisement participation model
 */
class TobiasAx_AdvertisementParticipationModel extends TobiasAx_BaseModel
{
    /**
     * Defines model attributes
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Participation' => array(AttributeType::Mixed, 'models' => 'TobiasAx_ParticipationModel'),
            'Advertisement' => array(AttributeType::Mixed, 'models' => 'TobiasAx_AdvertisementModel'),
        ));
    }
}
