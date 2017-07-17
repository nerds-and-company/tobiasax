<?php

namespace Craft;

use Exception;

/**
 * Tobias AX attribute service
 */
class TobiasAx_AttributeService extends BaseApplicationComponent
{
    /**
     * Gets object attributes
     * @param  string $objectId   object id
     * @param  string $objectType e.g. 'Building'
     * @param  string $category   attribute category to filter on e.g. 'Woningwaardering', 'Verhuur'
     * @return GuzzleClient
     */
    public function getAttributes($objectId, $objectType, $category = null)
    {
        $service = craft()->tobiasAx_attributeConnector;

        try {
            $envelope = $service->sendRequest($service->getAttributes($objectId, $objectType, $category));
            $result = $service->extract($envelope, 'Body/xmlns:GetAttributesResponse/xmlns:GetAttributesResult/xmlns:Attribute');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unkown error fetching attributes', null, $e);
        }

        return TobiasAx_AttributeModel::populateModels($result);
    }
}
