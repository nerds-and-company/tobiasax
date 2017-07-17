<?php

namespace Craft;

use Exception;

/**
 * Tobias AX import service
 *
 * Contains logics for running import
 */
class TobiasAx_ImportService extends BaseApplicationComponent
{
    /**
     * Craft element type handle
     * @var string
     */
    const TYPE_HANDLE_RENT = 'rent';

    /**
     * Default import model
     * @var string
     */
    const DEFAULT_IMPORT_MODEL = 'TobiasAx_ImportModel';

    /**
     * Import model classname
     * @var string
     */
    private $importModel = null;

    /**
     * Field handles for element type
     * @var array
     */
    private $fieldHandles = [];

    /**
     * @return void
     */
    public function init()
    {
        $this->setImportModel(static::DEFAULT_IMPORT_MODEL);
        $this->registerPluginImportModel();
        $this->fieldHandles = $this->getFieldHandles();
    }

    /**
     * Registers plugin import model
     * @return void
     */
    public function registerPluginImportModel()
    {
        $type = $this->getType();

        if ($type != null) {
            $pluginTypes = craft()->plugins->call('registerTobiasAxImportModel', array($type->handle));
            foreach ($pluginTypes as $classname) {
                $this->setImportModel($classname);
            }
        }
    }

    /**
     * Populates element model using webservice model
     * @param  TobiasAx_PublicationModel $model
     * @return TobiasAx_AdvertisementModel
     */
    public function populateElement($model)
    {
        /**
         * @var TobiasAx_AttributeModel[]
         */
        $attributes = $this->getBuildingAttributes($model->PropertyRegistration->RealEstateObject->BuildingId);

        /**
         * @var ElementCriteriaModel
         */
        $districts = craft()->elements->getCriteria(ElementType::Entry, [
            'section' => $this->getDistrictSection()
        ]);

        /**
         * @var TobiasAx_ImportModel
         */
        $importModel = $this->createImportModel($model, $attributes, $districts);

        /**
         * @var TobiasAx_AdvertisementElementType
         */
        $elementType = craft()->tobiasAx_advert->getElementTypeComponent();

        /**
         * @var TobiasAx_AdvertisementModel
         */
        $element = $this->createElementModel($elementType->populateElementModel(), $model->Id, $this->getType()->id);

        // map asset fields
        if ($this->isAssetImportEnabled()) {
            $assetsModel = craft()->tobiasAx_asset->getPublicationAssets($model);
            foreach ($importModel->getAssetMapping() as $fieldHandle => $types) {
                $assetIds = craft()->tobiasAx_asset->getPublicationAssetIds($assetsModel, $importModel, $types);
                $importModel->$fieldHandle = $assetIds;
            }
        }

        // populate fields
        foreach ($this->fieldHandles as $fieldHandle) {
            if (!empty($value = $importModel->getAttributeByHandle($fieldHandle))) {
                $element->getContent()->setAttribute($fieldHandle, $value);
            }
        }

        return $element;
    }

    /**
     * Creates an element model
     * @param  TobiasAx_AdvertisementModel $element
     * @param  string $tobiasId
     * @param  int $advertTypeId
     * @return TobiasAx_AdvertisementModel
     */
    public function createElementModel($element, $tobiasId, $advertTypeId)
    {
        $element->tobiasId = $tobiasId;
        $element->advertTypeId = $advertTypeId;

        // check for existing element
        if ($existing = craft()->tobiasAx_advert->getElementByExternalId($element->tobiasId)) {
            $element->id = $existing->id;
        }

        return $element;
    }

    /**
     * Create import model
     * @param  TobiasAx_PublicationModel $publication
     * @param  TobiasAx_AttributeModel[] $attributes
     * @param  ElementCriteriaModel $districts
     * @return object
     */
    public function createImportModel($publication, $attributes, $districts)
    {
        $importModelClass = $this->getImportModel();
        $importModel = new $importModelClass($publication, $attributes, $districts);

        return $importModel;
    }

    /**
     * Get building attributes
     * @param  int $buildingId
     * @return TobiasAx_AttributeModel[]
     */
    public function getBuildingAttributes($buildingId)
    {
        $attributes = craft()->tobiasAx_attribute->getAttributes($buildingId, 'Building');

        return $attributes;
    }

    /**
     * Gets rent element type model
     * @return TobiasAx_AdvertTypeModel
     */
    public function getType()
    {
        $elementType = craft()->tobiasAx_advertType->getTypeByHandle(static::TYPE_HANDLE_RENT);

        return $elementType;
    }

    /**
     * Returns field handles for element type
     * @return array
     */
    public function getFieldHandles()
    {
        $elementType = $this->getType();
        $fields = $elementType->getFieldLayout()->getFields();

        $fieldHandles = [];
        foreach ($fields as $field) {
            $fieldHandle = $field->getField()->handle;
            $fieldHandles[] = $fieldHandle;
        }

        return $fieldHandles;
    }

    /**
     * Set import model to use for import
     * @param object $model
     */
    public function setImportModel($model)
    {
        $this->importModel = $model;

        return $this;
    }

    /**
     * Get import model to use for import
     * @return object
     */
    public function getImportModel()
    {
        return $this->importModel;
    }

    /**
     * Creates import task
     * @param bool $checkPending    Checks for pending tasks
     * @throws \Exception
     * @return TaskModel
     */
    public function startTask($checkPending = true)
    {
        if ($checkPending && $this->hasPendingTask()) {
            // TODO: report to rollbar
            throw new TobiasAx_SystemException('Unable to create import task because of pending task');
        }

        return craft()->tasks->createTask('TobiasAx_Import', Craft::t('Import TobiasAX publications'));
    }

    /**
     * Returns true if there's a pending import task
     * @return boolean
     */
    public function hasPendingTask()
    {
        return count(craft()->tasks->getPendingTasks('TobiasAx_Import', 1)) > 0;
    }

    /**
     * Returns true if asset import should be enabled
     * @return bool
     */
    public function isAssetImportEnabled()
    {
        return craft()->config->get('tobiasAxAssetImport') ?? true;
    }

    /**
     * Returns Craft districts section handle
     * @return string
     */
    public function getDistrictSection()
    {
        return craft()->config->get('tobiasAxDistrictSection');
    }
}
