<?php
namespace Craft;

/**
 * TobiasAx - Registration Model
 */
class TobiasAx_RegistrationModel extends TobiasAx_EntityModel
{
    public function getId()
    {
        return $this->Id;
    }

    /**
     * Gets the partner from the registration's co-registrants
     * @return TobiasAx_CoRegistrantModel|null
     */
    public function getPartner()
    {
        $partner = null;

        foreach ($this->CoRegistrants as $coRegistrant) {
            if ($coRegistrant->CoRegistrantType == 'Partner') {
                $partner = $coRegistrant;
                break;
            }
        }

        return $partner;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'BankAccount' => AttributeType::String,
            'BuyRent' => AttributeType::String,
            'CoRegistrants' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_CoRegistrantModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'CurrentHousing' => AttributeType::String,
            'CurrentRentSellingPrice' => AttributeType::Number,
            'FamilySize' => AttributeType::Number,
            'FinalDate' => AttributeType::DateTime,
            'FocusGroupId' => AttributeType::String,
            'HouseHoldId' => AttributeType::String,
            'Id' => AttributeType::String,
            'Income' => AttributeType::Number,
            'InvoiceMethod' => AttributeType::String,
            'LocationPreferences' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_LocationPreferenceModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'NumberOfChildren' => AttributeType::Number,
            'OccupancyDate' => AttributeType::DateTime,
            'Options' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_ObjectGroupOptionModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Participations' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_ParticipationModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Points' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_PointsModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'PointsTotal' => AttributeType::Number,
            'PropertySeeker' => array(AttributeType::Mixed, 'model' => 'TobiasAx_PropertySeekerModel', 'exclude' => [TobiasAX_ModelScenario::UPDATE]),
            'PurposeGroupId' => AttributeType::String,
            'ReasonMovingId' => AttributeType::String,
            'ReDate' => AttributeType::DateTime,
            'RegistrationDateTime' => AttributeType::DateTime,
            'RegistrationStartDate' => AttributeType::DateTime,
            'RenewalDate' => AttributeType::DateTime,
            'Requirements' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_RequirementModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'ReRegistration' => AttributeType::Bool,
            'SortField1' => AttributeType::String,
            'SortField2' => AttributeType::String,
            'SortField3' => AttributeType::String,
            'Status' => AttributeType::String,
            'TipMessage' => AttributeType::Bool,
            'TypeHousingId' => AttributeType::String,
            'TypeId' => AttributeType::String,
            'Urgencies' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_UrgencyModel', 'exclude'=>[TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE])
        ));
    }
}
