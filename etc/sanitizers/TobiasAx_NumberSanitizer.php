<?php
namespace Craft;

use NumberFormatter;

/**
 * TobiasAx number sanitizer
 */
class TobiasAx_NumberSanitizer extends TobiasAx_Sanitizer implements ITobiasAx_Sanitizer
{
    /**
     * Sanitizes numbers converting locale formatting
     * @param string $name
     * @param array $value
     * @return array
     */
    public function clean($name, $value)
    {
        $config = $this->attributeConfig;

        if (isset($config[$name]) && $config[$name]['type'] == AttributeType::Number) {
            if (is_string($value)) {
                $formatter = new NumberFormatter(craft()->locale->id, NumberFormatter::DECIMAL);
                $value = $formatter->parse($value, NumberFormatter::TYPE_DOUBLE);
            }
            $value = round($value, $config[$name]['decimals']);
        }

        return $value;
    }
}
