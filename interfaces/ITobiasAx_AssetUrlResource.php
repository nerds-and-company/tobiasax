<?php
namespace Craft;

/**
 * Reggewoon - Asset URL resource interface
 */
interface ITobiasAx_AssetUrlResource
{
    /**
     * Return URL segments
     * @param TobiasAx_RealEstateModel $realEstateObject
     * @return array
     */
    public function getUrlSegments($realEstateObject);
}
