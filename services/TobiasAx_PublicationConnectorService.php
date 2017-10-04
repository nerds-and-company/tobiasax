<?php

namespace Craft;

/**
 * Tobias AX publication connector service
 */
class TobiasAx_PublicationConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Default entity expand
     * @var string
     */
    const DEFAULT_EXPAND = 'components,PropertyRegistration/RealEstateObject,PropertyRegistration/RealEstateObject/Prices,PublicationTexts';

    /**
     * Gets active publications
     * @param  string $data         additional template data
     * @return GuzzleClient
     */
    public function getActivePublications($data = [])
    {
        $data = array_merge([
            'useFreeForInternet' => 'true',
            'expand' => static::DEFAULT_EXPAND,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetActivePublications',
            'tobiasax/templates/soap/publication/get_active_publications',
            $data
        );

        return $request;
    }

    /**
     * Gets all publications
     * @param  DateTime $start publication start date
     * @param  DateTime $end   publication end date
     * @param  array    $data  additional template data
     * @return TobiasAx_PublicationModel[]
     */
    public function getAllPublications($start = null, $end = null, $data = [])
    {
        $data = array_merge([
            'start' => $start,
            'end' => $end,
            'expand' => static::DEFAULT_EXPAND,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetAllPublications',
            'tobiasax/templates/soap/publication/get_all_publications',
            $data
        );

        return $request;
    }
}
