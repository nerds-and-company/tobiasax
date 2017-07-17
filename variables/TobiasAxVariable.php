<?php

namespace Craft;

/**
 * Class TobiasAx - Variables for easy access to services from templates
 * @package Craft
 */
class TobiasAxVariable
{
    public function advertisements()
    {
        return craft()->tobiasAx_advert->getCriteria();
    }

    public function elementType()
    {
        return craft()->tobiasAx_advert->getElementType();
    }

    /**
     * Encrypts the input string
     * @param string $input
     * @return string|null
     */
    public function encrypt($input)
    {
        if (empty($input)) {
            return null;
        }
        return craft()->tobiasAx_cryptography->encrypt($input);
    }
}
