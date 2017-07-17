<?php
namespace Craft;

/**
 * Tobias AX - participations model
 */
class TobiasAx_ParticipationsModel extends TobiasAx_EntityModel
{
    /**
     * @return TobiasAx_ParticipationModel[]
     */
    public function getAll()
    {
        return $this->Participations;
    }

    /**
     * Get participation publication ids
     * @return string[]
     */
    public function getPublicationIds()
    {
        $ids = [];

        foreach ($this->Participations as $participation) {
            $ids[] = $participation->Publication->Id;
        }

        return $ids;
    }

    /**
     * @param string $participationId
     * @return boolean
     */
    public function hasParticipation($participationId)
    {
        return isset($this->Participations[$participationId]);
    }

    /**
     * Defines model attributes
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Participations' => array(AttributeType::ClassName, 'models' => 'Craft\TobiasAx_ParticipationModel', 'default' => []),
        ));
    }
}
