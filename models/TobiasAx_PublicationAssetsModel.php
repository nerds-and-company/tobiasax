<?php
namespace Craft;

/**
 * TobiasAx - Publication assets Model
 */
class TobiasAx_PublicationAssetsModel extends TobiasAx_BaseModel
{
    /**
     * Format asset path using publication
     * @param TobiasAx_AssetModel $asset
     */
    public function getAssetPath($asset)
    {
        return $asset->getAssetPath($this->Publication);
    }

    /**
     * Get assets by given type
     * @param string[] $types
     * @return TobiasAx_AssetModel[]
     */
    public function getByType($types)
    {
        $assets = array_filter($this->Assets, function ($asset) use ($types) {
            return in_array($asset->Type, $types);
        }, ARRAY_FILTER_USE_BOTH);

        return $assets;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Assets' => array(AttributeType::ClassName, 'models' => 'Craft\TobiasAx_AssetModel', 'default' => []),
            'Publication' => array(AttributeType::Mixed, 'model' => 'TobiasAx_PublicationModel'),
        ));
    }
}
