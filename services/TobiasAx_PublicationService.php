<?php

namespace Craft;

use Exception;

/**
 * Tobias AX publication service
 */
class TobiasAx_PublicationService extends BaseApplicationComponent
{
    /**
     * Gets active publications
     * @return TobiasAx_PublicationModel[]
     */
    public function getActivePublications()
    {
        $service = craft()->tobiasAx_publicationConnector;

        try {
            $envelope = $service->sendRequest($service->getActivePublications());
            $result = $service->extract($envelope, 'Body/xmlns:GetActivePublicationsResponse/xmlns:GetActivePublicationsResult/xmlns:Publication');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unkown error fetching active publications', null, $e);
        }

        return TobiasAx_PublicationModel::populateModels($result);
    }

    /**
     * Gets all publications
     * @param  DateTime $start publication start date
     * @param  DateTime $end   publication end date
     * @return TobiasAx_PublicationModel[]
     */
    public function getAllPublications($start = null, $end = null)
    {
        $service = craft()->tobiasAx_publicationConnector;

        try {
            $envelope = $service->sendRequest($service->getAllPublications($start, $end));
            $result = $service->extract($envelope, 'Body/xmlns:GetAllPublicationsResponse/xmlns:GetAllPublicationsResult/xmlns:Publication');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unkown error fetching publications', null, $e);
        }

        return TobiasAx_PublicationModel::populateModels($result);
    }
}
