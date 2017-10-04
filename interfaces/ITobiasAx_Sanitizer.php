<?php
namespace Craft;

/**
 * Reggewoon - Sanitizer interface
 */
interface ITobiasAx_Sanitizer
{
    /**
     * Normalizes numbers converting locale formatting
     * @param array $attributeConfig
     * @param array $values
     * @return array
     */
    public function clean($attributeConfig, $values);
}
