<?php

namespace Craft;

use Exception;

/**
 * Tobias AX participation service
 */
class TobiasAx_ParticipationService extends BaseApplicationComponent
{

    /**
     * @var string
     */
    const EXCEPTION_REACTION_FAIL = "ReactionFail";

    /**
     * @var string
     */
    const EXCEPTION_NOT_FOUND = "De deelname is niet gevonden";

    /**
     * @var TobiasAx_ParticipationConnectorService
     */
    protected $service;

    /**
     * Service init
     */
    public function init()
    {
        $this->service = craft()->tobiasAx_participationConnector;
    }

    /**
     * Create publication participation
     * @param string $publicationId
     * @param string $seekerRegistrationId
     * @param int $preference
     * @return TobiasAx_ParticipationModel
     */
    public function createParticipation($publicationId, $seekerRegistrationId, $preference = 0)
    {
        if ($publicationId == null) {
            throw new TobiasAx_SystemException('Error creating partipation, missing publication identifier', TobiasAX_ParticipationError::MISSING_PUBLICATION);
        }

        if ($seekerRegistrationId == null) {
            throw new TobiasAx_SystemException('Error creating partipation, missing seeker registration identifier', TobiasAX_ParticipationError::MISSING_REGISTRATION);
        }

        try {
            $envelope = $this->service->sendRequest($this->service->createParticipation($publicationId, $seekerRegistrationId, $preference));
            $result = $this->service->extractSingle($envelope, 'Body/xmlns:CreateParticipationResponse/xmlns:CreateParticipationResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unknown error creating participation: '.$e->getMessage(), TobiasAX_ParticipationError::UNKOWN, $e);
        }

        $participationResult = new TobiasAx_ParticipationModel($result);

        if (stristr($participationResult->MatchingStatus, static::EXCEPTION_REACTION_FAIL)) {
            throw new TobiasAx_BusinessLogicException(Craft::t('Error creating participation, one or more business logic conditions were not met with publication “{publication}” and registration "{registration}"!', array('publication' => $publicationId, 'registration' => $seekerRegistrationId)), TobiasAx_BusinessLogicException::STATUS_REACTION_FAIL);
        }

        return $participationResult;
    }

    /**
     * Gets registration participations
     * @param string $seekerRegistrationId
     * @param string $sort column to sort on
     * @param TobiasAX_SortOrder $sortOrder
     * @param int $offset
     * @param int $limit
     * @return TobiasAx_ParticipationsModel
     */
    public function getRegistationParticipations($seekerRegistrationId, $sort = TobiasAx_ParticipationConnectorService::DEFAULT_ORDER, $sortOrder = TobiasAX_SortOrder::DESC, $offset = null, $limit = null)
    {
        if ($seekerRegistrationId == null) {
            throw new TobiasAx_SystemException('Error fetching registration partipations, missing registration identifier', TobiasAX_ParticipationError::MISSING_REGISTRATION);
        }

        $models = $this->getParticipations(null, $seekerRegistrationId, $sort, $sortOrder, $offset, $limit);

        return new TobiasAx_ParticipationsModel(['Participations' => $models]);
    }

    /**
     * Gets registration participations
     * @param string $publicationId
     * @param string $seekerRegistrationId
     * @param string $sort column to sort on
     * @param TobiasAX_SortOrder $sortOrder
     * @param int $offset
     * @param int $limit
     * @return TobiasAx_ParticipationModel[]
     */
    public function getPublicationParticipations($publicationId, $seekerRegistrationId = null, $sort = TobiasAx_ParticipationConnectorService::DEFAULT_ORDER, $sortOrder = TobiasAX_SortOrder::DESC, $offset = null, $limit = null)
    {
        if ($publicationId == null) {
            throw new TobiasAx_SystemException('Error fetching registration partipations, missing publication identifier', TobiasAX_ParticipationError::MISSING_PUBLICATION);
        }

        return $this->getParticipations($publicationId, $seekerRegistrationId, $sort, $sortOrder, $offset, $limit);
    }

    /**
     * Gets publication participations
     * @param string $publicationId
     * @param string $seekerRegistrationId
     * @param string $sort column to sort on
     * @param TobiasAX_SortOrder $sortOrder
     * @param int $offset
     * @param int $limit
     * @return TobiasAx_ParticipationModel[]
     */
    public function getParticipations($publicationId = null, $seekerRegistrationId = null, $sort = TobiasAx_ParticipationConnectorService::DEFAULT_ORDER, $sortOrder = TobiasAX_SortOrder::DESC, $offset = null, $limit = null)
    {
        if ($publicationId == null && $seekerRegistrationId == null) {
            throw new TobiasAx_SystemException('Error fetching partipations, missing publication or seekerRegistration identifier', TobiasAX_ParticipationError::MISSING_ID);
        }

        try {
            $envelope = $this->service->sendRequest($this->service->getParticipations($publicationId, $seekerRegistrationId, $sort, $sortOrder, $offset, $limit));
            $result = $this->service->extract($envelope, 'Body/xmlns:GetParticipationsResponse/xmlns:GetParticipationsResult/xmlns:Participation');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unknown error fetching publication participations: '.$e->getMessage(), TobiasAX_ParticipationError::UNKOWN, $e);
        }

        return TobiasAx_ParticipationModel::populateModels($result);
    }

    /**
     * Deletes a registration's participation
     * @param string $registrationId
     * @param string $participationId
     * @return bool
     */
    public function deleteRegistrationParticipation($registrationId, $participationId)
    {
        $errorMessage = 'An error occured deleting participation. ';

        if ($registrationId == null || $participationId == null) {
            throw new TobiasAx_SystemException($errorMessage.'Missing registration or participation identifier', TobiasAX_ParticipationError::MISSING_ID);
        }

        $participations = $this->getRegistationParticipations($registrationId);

        if (!$participations->hasParticipation($participationId)) {
            throw new TobiasAx_SystemException($errorMessage.'Registration participation not found.', TobiasAX_ParticipationError::NOT_FOUND);
        }

        try {
            $request = $this->service->deleteParticipation($participationId);
            $this->service->sendRequest($request);
        } catch (Exception $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_NOT_FOUND)) {
                throw new TobiasAx_SystemException($errorMessage.'Participation not found.', TobiasAX_ParticipationError::NOT_FOUND);
            }

            throw new TobiasAx_SystemException($errorMessage.Craft::t('Unknown error: “{message}”', ['message' => $e->getMessage()], TobiasAX_ParticipationError::UNKOWN, $e));
        }

        return true;
    }

    /**
     * Filters published participations
     * @param  TobiasAx_ParticipationsModel $participations
     * @return TobiasAx_AdvertisementParticipationModel[]
     */
    public function getPublishedParticipations($participations)
    {
        $criteria = craft()->tobiasAx_advert->getCriteria();
        $criteria->published = true;
        $criteria->indexBy = 'tobiasId';
        $criteria->tobiasId = $participations->getPublicationIds();
        $advertisements = $criteria->find();

        $models = [];
        foreach ($participations->getAll() as $participation) {
            $publicationId = $participation->Publication->Id;
            if (isset($advertisements[$publicationId])) {
                $models[] = new TobiasAx_AdvertisementParticipationModel([
                    'Participation' => $participation,
                    'Advertisement' => $advertisements[$publicationId],
                ]);
            }
        }

        return $models;
    }
}
