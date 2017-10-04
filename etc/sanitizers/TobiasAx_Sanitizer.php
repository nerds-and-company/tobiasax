<?php
namespace Craft;

/**
 * TobiasAx sanitizer base class
 */
abstract class TobiasAx_Sanitizer
{
    /**
     * @var array
     */
    protected $attributeConfig = [];

    /**
     * Creates sanitizer for given attribute config
     * @param array $attributeConfig
     */
    public function __construct($attributeConfig)
    {
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * Cleans an array of attributes
     * @param array $values
     * @return array
     */
    public function cleanAll($values)
    {
        $data = [];

        foreach ($values as $name => $value) {
            $data[$name] = $this->clean($name, $value);
        }

        return $data;
    }
}
