<?php
namespace Craft;

/**
 * Tobias AX service.
 */
class TobiasAx_AdvertService extends BaseApplicationComponent
{
    /**
     * Default elementtype class
     * @var string
     */
    const DEFAULT_ELEMENTTYPE = 'TobiasAx_Advertisement';

    /**
     * @var string
     */
    private $elementType;

    /**
     * Returns an element by its ID.
     *
     * @param int $elementId
     *
     * @return TobiasAx_AdvertisementModel|null
     */
    public function getElementById($elementId)
    {
        return craft()->elements->getElementById($elementId, $this->getElementType());
    }

    /**
     * Returns an element criteria model with giving attributes
     * @param mixed  $attributes Any criteria attribute values that should be pre-populated on the criteria model.
     *
     * @throws Exception
     * @return ElementCriteriaModel An element criteria model, wired to fetch elements of the given $type.
     */
    public function getCriteria($attributes = null)
    {
        $criteria = craft()->elements->getCriteria($this->getElementType(), $attributes);

        return $criteria;
    }

    /**
     * Returns an element by its external ID.
     *
     * @param string $tobiasId
     *
     * @return TobiasAx_AdvertisementModel|null
     */
    public function getElementByExternalId($tobiasId)
    {
        $criteria = $this->getCriteria();
        $criteria->tobiasId = $tobiasId;

        return $criteria->first();
    }

    /**
     * Removes element(s) by filtered ids
     * @param  int[] $elementIds    element ids to filter, others will be deleted
     * @param  string $type         advertisement type elements to delete
     * @return bool Whether the element(s) were deleted successfully.
     */
    public function deleteElementsByFilter($elementIds, $type)
    {
        $success = true;

        // Get the IDs and let deleteElementById() take care of the actual deletion
        $query = craft()->db->createCommand()
            ->select('a.id')
            ->from('tobiasax_advertisement a')
            ->join('tobiasax_adverttype t', 't.id = a.advertTypeId')
            ->where('t.handle = :type', [':type' => $type])
            ->andWhere(['not in', 'a.id', $elementIds]);

        if ($filteredIds = $query->queryColumn()) {
            $success = craft()->elements->deleteElementById($filteredIds);
        }

        return $success;
    }

    /**
     * Saves an element.
     *
     * @param TobiasAx_AdvertisementModel $element
     *
     * @throws Exception
     *
     * @return bool
     */
    public function saveElement(TobiasAx_AdvertisementModel $element)
    {
        $isNewElement = !$element->id;

        // create slug based on element title
        $element->slug = ElementHelper::createSlug($element->getSlug());

        // creates record for this element
        $elementRecord = $this->createElementRecord($element);

        // set attributes
        $elementRecord->status = $element->status;
        $elementRecord->tobiasId = $element->tobiasId;
        $elementRecord->advertTypeId = $element->advertTypeId;

        if ($element->validate()) {
            $elementRecord->validate();
        }

        $element->addErrors($elementRecord->getErrors());

        if (!$element->hasErrors()) {
            $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
            try {
                // Fire an 'onBeforeSaveEvent' event
                $this->onBeforeSaveEvent(new Event($this, array(
                    'element'      => $element,
                    'isNewEvent' => $isNewElement,
                )));

                if (craft()->elements->saveElement($element)) {
                    // Now that we have an element ID, save it on the other stuff
                    if ($isNewElement) {
                        $elementRecord->id = $element->id;
                    }

                    // save record
                    $elementRecord->save(false);

                    // Now that id is available save it in model
                    $element->id = $elementRecord->id;

                    // Fire an 'onSaveEvent' event
                    $this->onSaveEvent(new Event($this, array(
                        'element'      => $element,
                        'isNewEvent' => $isNewElement,
                    )));

                    if ($transaction !== null) {
                        $transaction->commit();
                    }

                    return true;
                } else {
                    // error saving element
                    throw new Exception(Craft::t('Cannot save advertisement: “{errors}”.', array('errors' => json_encode($element->errors))));
                }
            } catch (\Exception $e) {
                if ($transaction !== null) {
                    $transaction->rollback();
                }

                throw $e;
            }
        } else {
            throw new Exception(Craft::t('Cannot save advertisement: “{errors}”.', array('errors' => json_encode($element->errors))));
        }

        return false;
    }

    /**
     * Registers plugin ElementType
     * @return void
     */
    public function registerPluginElementType()
    {
        // Makes sure base elementtype is initialized
        $defaultElementType = $this->getElementTypeComponentByName(static::DEFAULT_ELEMENTTYPE);
        $elementType = $defaultElementType->getClassHandle();
        $pluginTypes = craft()->plugins->call('registerTobiasAxElementType');

        foreach ($pluginTypes as $classname) {
            $elementType = $classname;
        }

        $this->setElementType($elementType);
    }

    /**
     * Initializes and returns base element type by name
     * @param string $className
     * @return BaseComponentType|null
     */
    protected function getElementTypeComponentByName($className)
    {
        return craft()->components->getComponentByTypeAndClass('element', $className);
    }

    /**
     * Initializes and returns used element type
     * @return BaseComponentType|null
     */
    public function getElementTypeComponent()
    {
        return craft()->components->getComponentByTypeAndClass('element', $this->getElementType());
    }

    /**
     * Set elementtype to use
     * @param object $elementType
     */
    public function setElementType($elementType)
    {
        $this->elementType = $elementType;

        return $this;
    }

    /**
     * Get elementtype to use
     * @return object
     */
    public function getElementType()
    {
        return $this->elementType;
    }

    // Events

    /**
     * Fires an 'onBeforeSaveEvent' event.
     *
     * @param Event $event
     */
    public function onBeforeSaveEvent(Event $event)
    {
        $this->raiseEvent('onBeforeSaveEvent', $event);
    }

    /**
     * Fires an 'onSaveEvent' event.
     *
     * @param Event $event
     */
    public function onSaveEvent(Event $event)
    {
        $this->raiseEvent('onSaveEvent', $event);
    }

    /**
     * @param TobiasAx_AdvertisementModel $element
     * @return TobiasAx_AdvertisementRecord
     */
    private function createElementRecord($element)
    {
        $isNewElement = !$element->id;

        // element data
        if (!$isNewElement) {
            $elementRecord = TobiasAx_AdvertisementRecord::model()->findById($element->id);

            if (!$elementRecord) {
                throw new Exception(Craft::t('No element exists with the ID “{id}”', array('id' => $element->id)));
            }
        } else {
            $elementRecord = new TobiasAx_AdvertisementRecord();
            $elementRecord->slug = ElementHelper::createSlug($element->getSlug());

            if ($sessionUser = craft()->userSession->getUser()) {
                $elementRecord->authorId = $sessionUser->id;
            }
        }

        return $elementRecord;
    }
}
