<?php
namespace Craft;

/**
 * TobiasAx - Person Model
 */
class TobiasAx_PersonModel extends TobiasAx_EntityModel
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @return string
     */
    public function getCommunicationEmail()
    {
        return $this->getCommunicationValue('Email');
    }

    /**
     * Get gender label
     * @return string
     */
    public function getGenderLabel()
    {
        $mapping = [
            'Male' => 'heer',
            'Female' => 'mevrouw',
        ];

        return isset($mapping[$this->Gender]) ? $mapping[$this->Gender] : null;
    }

    /**
     * Get mail salutation combined of genderlabel, optional infix and lastname
     * @return string
     */
    public function getMailSalutation()
    {
        $salutation = '';
        if (strlen($genderLabel = $this->getGenderLabel()) > 0) {
            $salutation .= $genderLabel . ' ';
        }
        if (strlen($this->Infix) > 0) {
            $salutation .= ucfirst(strtolower($this->Infix)) . ' ';
        }
        $salutation .= ucfirst(strtolower($this->Lastname));

        return $salutation;
    }

    /**
     * Get communication value by type
     * @param  string $type
     * @return string
     */
    public function getCommunicationValue($type)
    {
        $value = null;

        if (count($communicaton = $this->getCommunicationsByType($type)) > 0) {
            $value = array_shift($communicaton)->Value;
        }

        return $value;
    }

    /**
     * Get communication Id by type
     * @param  string $type
     * @return string
     */
    public function getCommunicationId($type)
    {
        $value = null;

        if (count($communicaton = $this->getCommunicationsByType($type)) > 0) {
            $value = array_shift($communicaton)->Id;
        }

        return $value;
    }

    /**
     * Filters communications by type
     * @param  string $type
     * @return TobiasAx_CommunicationModel[]
     */
    public function getCommunicationsByType($type)
    {
        $communications = array_filter($this->Communications, function ($communication) use ($type) {
            return $communication->Type == $type;
        }, ARRAY_FILTER_USE_BOTH);

        return $communications;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Addresses' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_AddressModel', 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'BankAccounts' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_BankAccountModel', 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Birthdate' => AttributeType::DateTime,
            'CareOf' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'ChamberOfCommerceCity' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'ChamberOfCommerceNumber' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Communications' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_CommunicationModel', 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Firstname' => AttributeType::String,
            'Gender' => AttributeType::String,
            'Id' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE]),
            'Incomes' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_IncomeModel', 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Infix' => AttributeType::String,
            'Initials' => AttributeType::String,
            'IntroductoryName' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Lastname' => AttributeType::String,
            'Maidenname' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'MaritalStatus' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Name' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'NationalIdentificationNumber' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'PersonNumber' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'PlaceOfBirth' => AttributeType::String,
            'Roles' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_PersonRoleModel', 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE]),
            'Type' => array(AttributeType::String, 'default' => 'Person'),
            'VATNumber' => array(AttributeType::String, 'exclude' => [TobiasAX_ModelScenario::CREATE, TobiasAX_ModelScenario::UPDATE])
        ));
    }
}
