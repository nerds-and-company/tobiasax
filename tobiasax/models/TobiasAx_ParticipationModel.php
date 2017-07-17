<?php
namespace Craft;

/**
 * Tobias AX - participation model
 */
class TobiasAx_ParticipationModel extends TobiasAx_EntityModel
{
    /**
     * Defines model attributes
     * @access protected
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'AcceptationDate' => AttributeType::DateTime,
            'ContractSigningDateTime' => AttributeType::DateTime,
            'Id' => AttributeType::String,
            'MatchingStatus' => AttributeType::String,
            'OfferDate' => AttributeType::DateTime,
            'OfferValidTill' => AttributeType::DateTime,
            'PointsTotal' => AttributeType::Number,
            'Preference' => AttributeType::Number,
            'PreliminaryContractDate' => AttributeType::DateTime,
            'PreliminaryRentFromDate' => AttributeType::DateTime,
            'ReasonOfferSkip' => AttributeType::String,
            'ReasonRefusal' => AttributeType::String,
            'ReasonRejection' => AttributeType::String,
            'ReasonReverseAcceptation' => AttributeType::String,
            'RefusalDate' => AttributeType::DateTime,
            'RejectionDate' => AttributeType::DateTime,
            'Publication' => array(AttributeType::Mixed, 'model' => 'TobiasAx_PublicationModel'),
            'ResponseDateTime' => AttributeType::DateTime,
            'ReverseAcceptationDate' => AttributeType::DateTime,
            'SeekerRegistration' => array(AttributeType::Mixed, 'model' => 'TobiasAx_RegistrationModel'),
            'Sequence' => AttributeType::Number,
            'Status' => AttributeType::String
        ));
    }
}
