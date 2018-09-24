<?php
namespace Craft;

/**
 * TobiasAX - Advertisement model.
 */
class TobiasAx_AdvertisementModel extends BaseElementModel
{
    /**
     * @var string
     */
    const LIVE     = 'live';

    /**
     * @var string
     */
    const EXPIRED  = 'expired';

    /**
     * @var string
     */
    const DISABLED  = 'disabled';

    /**
     * Default element type
     * @var string
     */
    const DEFAULT_TYPE   = 'rent';

    /**
     * Element type classname
     * @var string
     */
    protected $elementType = 'TobiasAx_Advertisement';

    /**
     * @access protected
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'title' => AttributeType::String,
            'tobiasId' => AttributeType::String,
            'advertTypeId' => AttributeType::Number,
            'status' => array(AttributeType::String, "default" => "live"),
            'authorId'   => AttributeType::Number,
            'slug' => AttributeType::String,
        ));
    }

    /**
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * Returns the element's status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the element's CP edit URL.
     *
     * @return string|false
     */
    public function getCpEditUrl()
    {
        return UrlHelper::getCpUrl('tobiasax/'.$this->type->handle.'/'.$this->id);
    }

    /**
     * Get element title
     * @return string
     */
    public function getTitle()
    {
        return "{$this->city}, {$this->street} {$this->houseNumber}";
    }

    /**
     * Get element slug
     * @return string
     */
    public function getSlug()
    {
        if ($this->slug) {
            return $this->slug;
        } else {
            return TobiasAx_AdvertisementModel::createSlug($this->city, $this->street, $this->houseNumber);
        }
    }

    /**
     * Get type name
     * @return string
     */
    public function getTypeName()
    {
        $name = null;
        $typeHandle = $this->type->handle;
        $mapping = [
            'rent' => 'huur',
            'sell' => 'koop',
        ];

        if (isset($mapping[$typeHandle])) {
            $name = $mapping[$typeHandle];
        }

        return $name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return 'woningaanbod/'.$this->getTypeName().'/'.$this->getSlug();
    }

    private function getMarkers()
    {
        return ModelHelper::packageAttributeValue($this->location->markers);
    }

    public function getMarker()
    {
        $markers = $this->getMarkers();
        return array_shift($markers);
    }

    public function getPrice()
    {
        return $this->content->price;
    }

    public function getNettoRent()
    {
        return $this->content->nettoRent;
    }

    public function getEligibleRent()
    {
        return $this->content->eligibleRent;
    }

    public function getPriceConditionText()
    {
        if ($this->type->handle == 'rent') {
            return ' per maand';
        } else {
            return ' '.$this->content->priceTypeSell;
        }
    }

    public function getServiceFee()
    {
        return $this->content->serviceFee;
    }

    public function getHeatingCostsTotal()
    {
        return $this->content->serviceFee + $this->content->heatingCosts;
    }

    public function getEnergyLabelLetter()
    {
        return substr($this->energyLabel, 0, 1);
    }

    public function isDirect()
    {
        $rentalType = (array) $this->content->rentalType;

        return in_array('direct', $rentalType);
    }

    /**
     * Returns the field layout used by this element.
     *
     * @return FieldLayoutModel|null
     */
    public function getFieldLayout()
    {
        $type = $this->getType();

        if ($type) {
            return $type->getFieldLayout();
        }
    }

    /**
     * Returns the element's type.
     *
     * @return TobiasAx_AdvertTypeModel|null
     */
    public function getType()
    {
        if ($this->advertTypeId) {
            return craft()->tobiasAx_advertType->getTypeById($this->advertTypeId);
        }
    }

    /**
     * Returns the entry's author.
     *
     * @return UserModel|null
     */
    public function getAuthor()
    {
        if ($this->authorId) {
            return craft()->users->getUserById($this->authorId);
        }
    }

    /**
     * @return string
     */
    public static function createSlug($city, $street, $houseNo)
    {
        return ElementHelper::createSlug($city.' '.$street.' '.$houseNo);
    }
}
