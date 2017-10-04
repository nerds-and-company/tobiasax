<?php
namespace Craft;

class TobiasAx_BaseModel extends BaseModel
{
    /**
     * @var bool Whether this model should be strict about only allowing values to be set on defined attributes
     */
    protected $strictAttributes = false;

    /**
     * Mass-populates models based on an array of attribute arrays.
     *
     * @param array $data
     * @param string|null $indexBy
     *
     * @return array
     */
    public static function populateModels($data, $indexBy = 'Id')
    {
        return parent::populateModels($data, $indexBy);
    }

    /**
     * Normalizes and sets an attribute's value
     *
     * @param string $name
     * @param mixed $value
     *
     * @return bool
     */
    public function setAttribute($name, $value)
    {
        if (!empty($value)) {
            $value = $this->normalizeDateTimes($name, $value);
            $value = $this->normalizeModels($name, $value);
        }

        return parent::setAttribute($name, $value);
    }

    /**
     * Normalizes nested models
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function normalizeModels($name, $value)
    {
        $attributes = $this->getAttributeConfigs();

        if (isset($attributes[$name]) && isset($attributes[$name]['models']) && is_array($value) && !$this->isPopulatedModel($value)) {
            $data = $this->isEnumeration($value) ? array_shift($value) : $value;
            $className = $attributes[$name]['models'];
            $value = $className::populateModels($data);
        }

        return $value;
    }

    /**
     * Whether given input is a populated model
     * @param mixed $value
     * @return boolean
     */
    private function isPopulatedModel($value)
    {
        if (count($value) == 0) {
            return false;
        }

        $arrayValues = array_values($value);

        return is_object($arrayValues[0]);
    }

    /**
     * Normalizes datetimes adding timezone offset
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function normalizeDateTimes($name, $value)
    {
        $attributes = $this->getAttributeConfigs();

        if (isset($attributes[$name]) && $attributes[$name]['type'] == AttributeType::DateTime) {
            $datetime = DateTime::createFromFormat("Y-m-d\TH:i:s", $value, $this->getTimezone());
            if ($datetime) {
                $value = $datetime->w3c();
            }
        }

        return $value;
    }

    /**
     * Get timezone identifier
     * @return string
     */
    public function getTimezone()
    {
        return craft()->config->get('tobiasAxTimezone') ?? 'Europe/Amsterdam';
    }

    /**
     * Whether given input is an enumeration or single element
     * @param array $value
     * @return boolean
     */
    protected function isEnumeration($value)
    {
        $first = array_shift($value);
        if (is_array($first)) {
            $keys = array_keys($first);
            $isEnum = count($keys) > 0 && $keys[0] === 0;
        } else {
            $isEnum = false;
        }

        return $isEnum;
    }

    /**
     * Gets the model's 'create' attributes
     * @param array $names filters attributes by name
     * @param bool $flattenValues Will change a DateTime object to a timestamp, Mixed to array, etc. Useful for saving
     * @return array
     */
    public function getCreateAttributes($names = null, $flattenValues = null)
    {
        return $this->getScenarioAttributes(TobiasAX_ModelScenario::CREATE, $names, $flattenValues);
    }

    /**
     * Gets the model's 'update' attributes
     * @param array $names filters attributes by name
     * @param bool $flattenValues Will change a DateTime object to a timestamp, Mixed to array, etc. Useful for saving
     * @return array
     */
    public function getUpdateAttributes($names = null, $flattenValues = null)
    {
        return $this->getScenarioAttributes(TobiasAX_ModelScenario::UPDATE, $names, $flattenValues);
    }

    /**
     * Gets the model's 'update' attributes
     * @param array $names filters attributes by name
     * @param bool $flattenValues Will change a DateTime object to a timestamp, Mixed to array, etc. Useful for saving
     * @return array
     */
    public function getDeleteAttributes($names = null, $flattenValues = null)
    {
        return $this->getScenarioAttributes(TobiasAX_ModelScenario::DELETE, $names, $flattenValues);
    }

    /**
     * Gets the model's 'get' attributes
     * @param array $names filters attributes by name
     * @param bool $flattenValues Will change a DateTime object to a timestamp, Mixed to array, etc. Useful for saving
     * @return array
     */
    public function getGetAttributes($names = null, $flattenValues = null)
    {
        return $this->getScenarioAttributes(TobiasAX_ModelScenario::GET, $names, $flattenValues);
    }

    /**
     * Returns attributes for given scenario
     * @param TobiasAX_ModelScenario $scenario
     * @param array $names filters attributes
     * @param bool $flattenValues Will change a DateTime object to a timestamp, Mixed to array, etc. Useful for saving
     * @return array
     */
    private function getScenarioAttributes($scenario, $names, $flattenValues)
    {
        if (!isset($scenario)) {
            return parent::getAttributes($names, $flattenValues);
        }

        $values = array();

        $attributesNames = $this->attributeNames($scenario);

        foreach ($attributesNames as $name) {
            if ($names === null || in_array($name, $names)) {
                $values[$name] = $this->getAttribute($name, $flattenValues);
            }
        }

        return $values;
    }

    /**
     * Returns the list of this model's attribute names.
     * @param TobiasAX_ModelScenario $scenario
     * @return array
     */
    public function attributeNames($scenario = null)
    {
        if ($scenario == null) {
            $attributeNames = parent::attributeNames();
            sort($attributeNames);
            return $attributeNames;
        }

        $attributeConfigs = $this->getAttributeConfigs();
        $attributeConfigs = array_filter($attributeConfigs, array(new TobiasAx_ScenarioFilter($scenario), "filter"));

        ksort($attributeConfigs);

        $attributeNames = array_keys($attributeConfigs);

        if (!$this->strictAttributes) {
            $attributeNames = array_merge($attributeNames, parent::getExtraAttributeNames());
        }

        return $attributeNames;
    }
}
