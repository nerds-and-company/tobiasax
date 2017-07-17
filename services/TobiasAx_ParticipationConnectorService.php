<?php

namespace Craft;

/**
 * Tobias AX participation connector service
 */
class TobiasAx_ParticipationConnectorService extends TobiasAx_ConnectorService
{
    /**
     * @var string
     */
    const DEFAULT_ORDER = 'ResponseDateTime';

    /**
     * @var string
     */
    const DEFAULT_EXPAND = 'Publication,SeekerRegistration';

    /**
     * Create publication participation
     * @param string $publicationId
     * @param string $seekerRegistrationId
     * @param int $preference
     * @param array $data additional template data
     * @return GuzzleClient
     */
    public function createParticipation($publicationId, $seekerRegistrationId, $preference = 0, $data = [])
    {
        $data = array_merge([
            'publicationId' => $publicationId,
            'seekerRegistrationId' => $seekerRegistrationId,
            'preference' => $preference,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'CreateParticipation',
            'tobiasax/templates/soap/participation/create',
            $data
        );

        return $request;
    }

    /**
     * Gets publication participations
     * @param string $publicationId
     * @param string $seekerRegistrationId
     * @param string $sort column to sort on
     * @param TobiasAX_SortOrder $sortOrder
     * @param int $offset
     * @param int $limit
     * @return GuzzleClient
     */
    public function getParticipations($publicationId = null, $seekerRegistrationId = null, $sort = self::DEFAULT_ORDER, $sortOrder = TobiasAX_SortOrder::ASC, $offset = null, $limit = null)
    {
        $data = [
            'publicationId' => $publicationId,
            'seekerRegistrationId' => $seekerRegistrationId,
            'sort' => $sort,
            'sortOrder' => $sortOrder,
            'offset' => $offset,
            'limit' => $limit,
            'expand' => static::DEFAULT_EXPAND
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'GetParticipations',
            'tobiasax/templates/soap/participation/get_participations',
            $data
        );

        return $request;
    }

    /**
     * Delete participation
     * @param string $participationId
     * @return GuzzleClient
     */
    public function deleteParticipation($participationId)
    {
        $data = [
            'participationId' => $participationId,
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'DeleteParticipation',
            'tobiasax/templates/soap/participation/delete_participation',
            $data
        );

        return $request;
    }
}
