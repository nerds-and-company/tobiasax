<?php
namespace Craft;

/**
 * Reggewoon - Geocodable interface
 */
interface ITobiasAx_Geocodable
{
    /**
     * Return address for geocoding
     * @return string
     */
    public function getGeocodingAddress();

    /**
     * Return geocoding target field handle
     * @return string
     */
    public function getGeocodingFieldHandle();
}
