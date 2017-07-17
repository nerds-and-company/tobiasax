<?php

namespace Craft;

use NerdsAndCompany\Schematic\Services\Base as Schematic_AbstractService;

/**
 * Class TobiasAx_MigrationsService.
 */
class TobiasAx_MigrationsService extends Schematic_AbstractService
{
    /**
     * @return TobiasAx_AdvertTypeService
     */
    private function getTypeService()
    {
        return craft()->tobiasAx_advertType;
    }

    /**
     * Export all advert types.
     *
     * @param array $data
     *
     * @return array
     */
    public function export(array $data = array())
    {
        Craft::log(Craft::t('Exporting Advertisement types'));
        $types = $this->getTypeService()->getAllTypes('handle');

        foreach ($types as $type) {
            $data[$type->handle] = $this->getTypeDefinition($type);
        }

        return $data;
    }

    /**
     * Get adverttype definition.
     *
     * @param TobiasAx_AdvertTypeModel $advertType
     *
     * @return array
     */
    private function getTypeDefinition(TobiasAx_AdvertTypeModel $advertType)
    {
        return [
            'name' => $advertType->name,
            'fieldLayout' => Craft::app()->schematic_fields->getFieldLayoutDefinition($advertType->getFieldLayout()),
        ];
    }

    /**
     * Attempt to import advert types.
     *
     * @param array $advertTypeDefinitions
     * @param bool  $force If set to true advertTypes not included in the import will be deleted
     *
     * @return Result
     */
    public function import(array $advertTypeDefs, $force = false)
    {
        Craft::log(Craft::t('Importing Advertisement types'));

        $advertTypes = $this->getTypeService()->getAllTypes('handle');

        foreach ($advertTypeDefs as $advertTypeHandle => $advertTypeDefinition) {
            $advertType = array_key_exists($advertTypeHandle, $advertTypes)
                ? $advertTypes[$advertTypeHandle]
                : new TobiasAx_AdvertTypeModel();

            unset($advertTypes[$advertTypeHandle]);

            $this->populateAdvertType($advertType, $advertTypeDefinition, $advertTypeHandle);

            if (!$this->getTypeService()->saveType($advertType)) { // Save adverttype
                $this->addErrors($advertType->getAllErrors());

                continue;
            }
        }

        if ($force) {
            foreach ($advertTypes as $advertType) {
                $this->getTypeService()->deleteTypeById($advertType->id);
            }
        }

        return $this->getResultModel();
    }

    /**
     * Populate adverttype.
     *
     * @param TobiasAx_AdvertTypeModel $advertType
     * @param array                  $advertTypeDefinition
     * @param string                 $advertTypeHandle
     */
    private function populateAdvertType(TobiasAx_AdvertTypeModel $advertType, array $advertTypeDefinition, $advertTypeHandle)
    {
        $advertType->setAttributes([
            'handle' => $advertTypeHandle,
            'name' => $advertTypeDefinition['name'],
        ]);

        $fieldLayout = Craft::app()->schematic_fields->getFieldLayout($advertTypeDefinition['fieldLayout']);
        $advertType->setFieldLayout($fieldLayout);
    }
}
