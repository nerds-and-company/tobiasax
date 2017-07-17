<?php
namespace Craft;

/**
 * Tobias - Advertisement elementtype
 */
class TobiasAx_AdvertisementElementType extends BaseElementType
{
    /**
     * Returns the element type name.
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Advertisements');
    }

    /**
     * Return true so we have a status select menu
     * @return boolean
     */
    public function hasStatuses()
    {
        return true;
    }

    /**
     * Returns whether this element type has content.
     *
     * @return bool
     */
    public function hasContent()
    {
        return true;
    }

    /**
     * Returns whether this element type has titles.
     *
     * @return bool
     */
    public function hasTitles()
    {
        return false;
    }

    /**
     * @inheritDoc IElementType::getAvailableActions()
     *
     * @param string|null $source
     *
     * @return array|null
     */
    public function getAvailableActions($source = null)
    {
        $actions = array();

        // Edit
        $editAction = craft()->elements->getAction('Edit');
        $editAction->setParams(array(
            'label' => Craft::t('Edit advertisement'),
        ));
        $actions[] = $editAction;

        // Delete
        $deleteAction = craft()->elements->getAction('TobiasAx_DeleteTobiasAdvertisement');
        $deleteAction->setParams(array(
            'label' => Craft::t('Delete advertisement'),
        ));
        $actions[] = $deleteAction;

        // Set status
        $statusAction = craft()->elements->getAction('SetStatus');
        $statusAction->setParams(array(
            'label' => Craft::t('Set status'),
        ));
        $actions[] = $statusAction;

        return $actions;
    }

    /**
     * @inheritDoc
     *
     * @param string|null $context
     * @return array|false
     */
    public function getSources($context = null)
    {
        $sources = array(
            '*' => array(
                'label'    => Craft::t('All advertisements'),
            )
        );

        foreach (craft()->tobiasAx_advertType->getAllTypes() as $type) {
            $key = 'advertTypeId:'.$type->id;

            $sources[$key] = array(
                'label'    => $type->name,
                'criteria' => array('advertTypeId' => $type->id)
            );
        }

        return $sources;
    }

    // Define statuses
    public function getStatuses()
    {
        return array(
            TobiasAx_AdvertisementModel::LIVE  => Craft::t('Online'),
            TobiasAx_AdvertisementModel::EXPIRED  => Craft::t('Offline'),
            TobiasAx_AdvertisementModel::DISABLED => Craft::t('Disabled')
        );
    }


    /**
     * @inheritDoc
     *
     * @param string|null $source
     * @return array
     */
    public function defineTableAttributes($source = null)
    {
        return array(
            'title'     => Craft::t('Title')
        );
    }

    /**
     * @inheritDoc IElementType::defineSortableAttributes()
     *
     * @retrun array
     */
    public function defineSortableAttributes($source = null)
    {
        return array(
            'status'     => Craft::t('Status')
        );
    }

    /**
     * @inheritDoc IElementType::defineSearchableAttributes()
     *
     * @return array
     */
    public function defineSearchableAttributes()
    {
        return array("dateCreated");
    }

    /**
     * Defines any custom element criteria attributes for this element type.
     *
     * @return array
     */
    public function defineCriteriaAttributes()
    {
        return array(
            'advertTypeId' => AttributeType::Number,
            'tobiasId' => AttributeType::Number,
            'status' => array(AttributeType::String, 'default' => EntryModel::LIVE),
            'type' => AttributeType::String,
            'dateCreated' => AttributeType::DateTime,
            'published' => array(AttributeType::Bool, 'default' => null),
            'order' => array(AttributeType::String, 'default' => 'tobiasax_advertisement.dateCreated desc'),
        );
    }

    /**
     * @inheritDoc IElementType::getElementQueryStatusCondition()
     *
     * @param DbCommand $query
     * @param string    $status
     *
     * @return array|false|string|void
     */
    public function getElementQueryStatusCondition(DbCommand $query, $status)
    {
        switch ($status) {
            case EntryModel::LIVE:
                return array('and',
                    'elements.enabled = 1',
                    'elements_i18n.enabled = 1'
                );
            case EntryModel::PENDING:
                return array('and',
                    'elements.enabled = 1',
                    'elements_i18n.enabled = 1'
                );
            case EntryModel::EXPIRED:
                return array('and',
                    'elements.enabled = 1',
                    'elements_i18n.enabled = 1'
                );
        }
    }

    /**
     * Modifies an element query targeting elements of this type.
     *
     * @param DbCommand $query
     * @param ElementCriteriaModel $criteria
     * @return mixed
     */
    public function modifyElementsQuery(DbCommand $query, ElementCriteriaModel $criteria)
    {
        $query
            ->addSelect('tobiasax_advertisement.*')
            ->join('tobiasax_advertisement tobiasax_advertisement', 'tobiasax_advertisement.id = elements.id')
            ->join('tobiasax_adverttype tobiasax_adverttype', 'tobiasax_adverttype.id = tobiasax_advertisement.advertTypeId');

        if ($criteria->advertTypeId) {
            $query->andWhere(DbHelper::parseParam('advertTypeId', $criteria->advertTypeId, $query->params));
        }

        if ($criteria->tobiasId) {
            $query->andWhere(DbHelper::parseParam('tobiasId', $criteria->tobiasId, $query->params));
        }

        if ($criteria->type) {
            $query->andWhere(DbHelper::parseParam('tobiasax_adverttype.handle', $criteria->type, $query->params));
        }

        if ($criteria->published === true) {
            $query->andWhere('content.field_lastResponseDate > NOW()');
        } else if ($criteria->published === false) {
            $query->andWhere('content.field_lastResponseDate < NOW()');
        }
    }

    /**
     * Populates an element model based on a query result.
     *
     * @param array $row
     * @return array
     */
    public function populateElementModel($row = [])
    {
        return TobiasAx_AdvertisementModel::populateModel($row);
    }
}
