<?php

namespace Craft;

class TobiasAx_PropertySeekerModel extends TobiasAx_PersonModel
{
    /**
     * Get registrations by status
     * @param TobiasAX_RegistrationStatus $status
     * @return TobiasAx_SeekerRegistrationModel[]
     */
    public function getRegistrationsByStatus($status)
    {
        $registrations = array_filter($this->getOrderedRegistrations(), function ($registration) use ($status) {
            return $registration->Status == $status;
        }, ARRAY_FILTER_USE_BOTH);

        return $registrations;
    }

    /**
     * Gets registrations ordered by RegistrationDateTime DESC
     * @return TobiasAx_SeekerRegistrationModel[]
     */
    public function getOrderedRegistrations()
    {
        $registrations = $this->SeekerRegistrations;

        usort($registrations, function ($registrationA, $registrationB) {
            return $registrationA->RegistrationDateTime < $registrationB->RegistrationDateTime;
        });

        return $registrations;
    }

    /**
     * Gets the most recent active registration from the currently logged in user, filled so that twig can handle it
     * @return TobiasAx_RegistrationModel|null
     */
    public function getActiveRegistration()
    {
        $registration = $this->getRegistrationsByStatus(TobiasAX_RegistrationStatus::ACTIVE);
        if (count($registration) > 0) {
            $registration = array_shift($registration);

            $registration->PropertySeeker = $this; // So we can access everything from registration instead of propertyseeker

            return $registration;
        } else {
            return null;
        }
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    public function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Id' => AttributeType::String,
            'Income' => AttributeType::Number,
            'InvoiceMethod' => AttributeType::String,
            'Nationality' => AttributeType::String,
            'PersonId' => AttributeType::String,
            'RegistrationDateTime' => AttributeType::DateTime,
            'SeekerRegistrations' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_RegistrationModel'),
            'ServiceSubscriptions' => array(AttributeType::ClassName, 'default' => array(), 'models' => 'Craft\TobiasAx_ServiceSubscriptionModel'),
            'Status' => AttributeType::String
        ));
    }

    public function getId()
    {
        return $this->Id;
    }
}
