<?php
namespace Craft;

/**
 * TobiasAX sanitation helper
 */
class TobiasAx_SanitationHelper
{
    /**
     * Mass-populates models based on an array of attribute arrays containing user input.
     * @param string $className
     * @param array $data
     * @param string|null $indexBy
     * @return TobiasAx_BaseModel[]
     */
    public static function populateModels($className, $data, $indexBy = null)
    {
        $data = array_map(function ($values) use ($className) {
            return self::sanitizeAttributes($className, $values);
        }, $data);

        return $className::populateModels($data, $indexBy);
    }

    /**
     * Populates a new model instance with a given set of attributes containing user input.
     * @param string $className
     * @param mixed $values
     * @return TobiasAx_BaseModel
     */
    public static function populateModel($className, $values)
    {
        $attributes = self::sanitizeAttributes($className, $values);

        return $className::populateModel($attributes);
    }

    /**
     * Sanitizes model attribute values
     * @param string $className
     * @param array $values
     * @return array
     */
    public static function sanitizeAttributes($className, $values)
    {
        $model = new $className;
        $attributeConfig = $model->getAttributeConfigs();
        $sanitizers = [TobiasAx_NumberSanitizer::class];

        foreach ($sanitizers as $sanitizerClass) {
            $sanitizer = new $sanitizerClass($attributeConfig);
            $values = $sanitizer->cleanAll($values);
        }

        return $values;
    }
}
