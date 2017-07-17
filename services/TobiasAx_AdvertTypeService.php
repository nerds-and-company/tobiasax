<?php
namespace Craft;

/**
 * Tobias - Advert type service
 */
class TobiasAx_AdvertTypeService extends BaseApplicationComponent
{
    private $_allTypeIds;
    private $_typesById;
    private $_fetchedAllTypes = false;

    /**
     * Returns all of the type IDs.
     *
     * @return array
     */
    public function getAllTypeIds()
    {
        if (!isset($this->_allTypeIds)) {
            if ($this->_fetchedAllTypes) {
                $this->_allTypeIds = array_keys($this->_typesById);
            } else {
                $this->_allTypeIds = craft()->db->createCommand()
                    ->select('id')
                    ->from('tobiasax_adverttype')
                    ->queryColumn();
            }
        }

        return $this->_allTypeIds;
    }

    /**
     * Returns all types.
     *
     * @param string|null $indexBy
     * @return array
     */
    public function getAllTypes($indexBy = null)
    {
        if (!$this->_fetchedAllTypes) {
            $typeRecords = TobiasAx_AdvertTypeRecord::model()->ordered()->findAll();
            $this->_typesById = TobiasAx_AdvertTypeModel::populateModels($typeRecords, 'id');
            $this->_fetchedAllTypes = true;
        }

        if ($indexBy == 'id') {
            return $this->_typesById;
        } elseif (!$indexBy) {
            return array_values($this->_typesById);
        } else {
            $types = array();

            foreach ($this->_typesById as $type) {
                $types[$type->$indexBy] = $type;
            }

            return $types;
        }
    }

    /**
     * Gets the total number of types.
     *
     * @return int
     */
    public function getTotalTypes()
    {
        return count($this->getAllTypeIds());
    }

    /**
     * Returns a type by its ID.
     *
     * @param $typeId
     * @return TobiasAx_AdvertTypeModel|null
     */
    public function getTypeById($typeId)
    {
        if (!isset($this->_typesById) || !array_key_exists($typeId, $this->_typesById)) {
            $typeRecord = TobiasAx_AdvertTypeRecord::model()->findById($typeId);

            if ($typeRecord) {
                $this->_typesById[$typeId] = TobiasAx_AdvertTypeModel::populateModel($typeRecord);
            } else {
                $this->_typesById[$typeId] = null;
            }
        }

        return $this->_typesById[$typeId];
    }

    /**
     * Gets a type by its handle.
     *
     * @param string $typeHandle
     * @return TobiasAx_AdvertTypeModel|null
     */
    public function getTypeByHandle($typeHandle)
    {
        $typeRecord = TobiasAx_AdvertTypeRecord::model()->findByAttributes(array(
            'handle' => $typeHandle
        ));

        if ($typeRecord) {
            return TobiasAx_AdvertTypeModel::populateModel($typeRecord);
        }
    }

    /**
     * Saves a type.
     *
     * @param TobiasAx_AdvertTypeModel $type
     * @throws \Exception
     * @return bool
     */
    public function saveType(TobiasAx_AdvertTypeModel $type)
    {
        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
        try {

            $typeRecord = $this->createTypeRecord($type);

            $typeRecord->validate();
            $type->addErrors($typeRecord->getErrors());

            if ($type->hasErrors()) {
                return false;
            }

            $this->dropOldFieldLayout($type, $typeRecord);

            // Save the new one
            $fieldLayout = $type->getFieldLayout();
            craft()->fields->saveLayout($fieldLayout);

            // Update the type record/model with the new layout ID
            $type->fieldLayoutId = $fieldLayout->id;
            $typeRecord->fieldLayoutId = $fieldLayout->id;

            // Save it!
            $typeRecord->save(false);

            // Now that we have a type ID, save it on the model
            if (!$type->id) {
                $type->id = $typeRecord->id;
            }

            // Might as well update our cache of the type while we have it.
            $this->_typesById[$type->id] = $type;

            if ($transaction !== null) {
                $transaction->commit();
            }
        } catch (\Exception $e) {
            if ($transaction !== null) {
                $transaction->rollback();
            }

            throw $e;
        }

        return true;
    }

    /**
     * Deletes a type by its ID.
     *
     * @param int $typeId
     * @throws \Exception
     * @return bool
     */
    public function deleteTypeById($typeId)
    {
        if (!$typeId) {
            return false;
        }

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
        try {
            // Delete the field layout
            $fieldLayoutId = craft()->db->createCommand()
                ->select('fieldLayoutId')
                ->from('tobiasax_adverttype')
                ->where(array('id' => $typeId))
                ->queryScalar();

            if ($fieldLayoutId) {
                craft()->fields->deleteLayoutById($fieldLayoutId);
            }

            // Grab the element ids so we can clean the elements table.
            $elementIds = craft()->db->createCommand()
                ->select('id')
                ->from('tobiasax_advertisement')
                ->where(array('advertTypeId' => $typeId))
                ->queryColumn();

            craft()->elements->deleteElementById($elementIds);

            $affectedRows = craft()->db->createCommand()->delete('tobiasax_adverttype', array('id' => $typeId));

            if ($transaction !== null) {
                $transaction->commit();
            }

            return (bool) $affectedRows;
        } catch (\Exception $e) {
            if ($transaction !== null) {
                $transaction->rollback();
            }

            throw $e;
        }
    }

    /**
     * Gets TobiasAX custom Craft permissions
     * @return array
     */
    public function getPermissions()
    {
        $permissions = array(
            'tobiasax_startImport' => array('label' => Craft::t('Start import')),
            'tobiasax_editPeerEntries' => array('label' => Craft::t('Edit other authors’ entries'))
        );

        foreach ($this->getAllTypes() as $type) {
            $permissions['tobiasax_edit_' . $type->handle] = array('label' => $type->name . ' ' . strtolower(Craft::t('Edit entries')));
        }

        return $permissions;
    }

    /** Finds an existing type record or creates a new one based on the given type.
     *
     * @param TobiasAx_AdvertTypeModel $type
     * @return TobiasAx_AdvertTypeRecord
     * @throws \Exception If the model can not be found
     */
    private function createTypeRecord($type)
    {
        if ($type->id) {
            $typeRecord = TobiasAx_AdvertTypeRecord::model()->findById($type->id);

            if (!$typeRecord) {
                throw new Exception(Craft::t('No type exists with the ID “{id}”', array('id' => $type->id)));
            }
        } else {
            $typeRecord = new TobiasAx_AdvertTypeRecord();
        }

        $typeRecord->name       = $type->name;
        $typeRecord->handle     = $type->handle;

        return $typeRecord;
    }

    /**
     * @param TobiasAx_ADvertTypeModel $type
     * @param TobiasAx_AdvertTypeRecord $typeRecord
     */
    private function dropOldFieldLayout($type, $typeRecord)
    {
        $isNew = $type->id ? false : true;
        if (!$isNew) {
            $oldType = TobiasAx_AdvertTypeModel::populateModel($typeRecord);

            if ($oldType->fieldLayoutId) {
                craft()->fields->deleteLayoutById($oldType->fieldLayoutId);
            }
        }
    }
}
