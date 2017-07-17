<?php
namespace Craft;

abstract class TobiasAx_ImportBaseModel extends BaseModel
{
    /**
     * @var bool Whether this model should be strict about only allowing values to be set on defined attributes
     */
    protected $strictAttributes = false;

    /**
     * @param TobiasAx_PublicationModel $publication
     * @param TobiasAx_AttributeModel[] $buildingAttributes
     * @param ElementCriteriaModel $districts
     * @return TobiasAx_ImportBaseModel
     */
    public function __construct($publication = null, $buildingAttributes = [], $districts = null)
    {
        parent::__construct(null);
        $this->setAttributes([
            'Publication' => $publication,
            'BuildingAttributes' => $buildingAttributes,
            'Districts' => $districts,
        ]);
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge($this->getAssetAttributes(), parent::defineAttributes(), array(
            'Publication' => array(AttributeType::Mixed, 'model' => 'TobiasAx_PublicationModel'),
            'BuildingAttributes' => array(AttributeType::ClassName, 'models' => 'Craft\TobiasAx_AttributeModel', 'default' => array()),
            'Districts' => array(AttributeType::Mixed, 'model' => 'ElementCriteriaModel'),
        ));
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return TobiasAx_AdvertisementModel::createSlug($this->getCity(), $this->getStreet(), $this->getHouseNumber());
    }

    /**
     * @return string
     */
    public function getFoldername()
    {
        return $this->getSlug();
    }

    /**
     * Gets attribute by field handle
     * @param  string $handle
     * @return mixed
     */
    public function getAttributeByHandle($handle)
    {
        $getter = 'get' . ucfirst($handle);
        $value = null;

        if (method_exists($this, $getter)) {
            $value = $this->$getter();
        } else if (isset($this->$handle)) {
            $value = $this->$handle;
        }

        return $value;
    }

    /**
     * Gets asset field mapping
     * Maps field handle to one or more TobiasAX types
     * @return array
     */
    public function getAssetMapping()
    {
        return [];
    }

    /**
     * Gets building attribute value by id
     * @param  int $attributeId
     * @return mixed
     */
    protected function getBuildingAttributeValueById($attributeId)
    {
        $value = null;

        if ($attribute = $this->getBuildingAttributeById($attributeId)) {
            $value = $attribute->Value;
        }

        return $value;
    }

    /**
     * Gets building attribute by id
     * @param  int $attributeId
     * @return TobiasAx_AttributeModel|null
     */
    protected function getBuildingAttributeById($attributeId)
    {
        $attribute = null;

        if (isset($this->BuildingAttributes[$attributeId])) {
            $attribute = $this->BuildingAttributes[$attributeId];
        }

        return $attribute;
    }

    /**
     * Gets component by its name
     * @param  string $name name to filter on
     * @return TobiasAx_PublicationComponentModel
     */
    protected function getComponentByName($name)
    {
        $components = array_filter($this->Publication->Components, function ($component) use ($name) {
            return $component->Description == $name;
        }, ARRAY_FILTER_USE_BOTH);

        return array_shift($components);
    }

    /**
     * Gets component value by its name
     * @param  string $name name to filter on
     * @return float
     */
    protected function getComponentValueByName($name)
    {
        $value = null;
        if ($component = $this->getComponentByName($name)) {
            $value = $component->getValue();
        }

        return $value;
    }

    /**
     * Orders publication text
     * @param  string $section section to filter on
     * @param  string $type type to filter on
     * @return array
     */
    protected function getOrderedAdvertisementText($section, $type = 'Advertising')
    {
        $texts = [];

        // filter array by section and or type
        foreach ($this->Publication->PublicationTexts as $text) {
            if (($type == null || $type == $text->Type) && ($section == null || $section == $text->Section)) {
                $text->Text = html_entity_decode($text->Text);
                $texts[] = $text;
            }
        }

        // sort texts by sequence number
        usort($texts, function ($current, $prev) {
            return intval($current->SequenceNr) > intval($prev->SequenceNr);
        });

        return $texts;
    }

    /**
     * Creates asset attributes definitons using asset mapping
     * @return array
     */
    protected function getAssetAttributes()
    {
        $attributes = [];
        foreach (array_keys($this->getAssetMapping()) as $name) {
            $attributes[$name] = [AttributeType::Mixed, 'default' => []];
        }

        return $attributes;
    }
}
