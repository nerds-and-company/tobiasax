<?php

namespace Craft;

/**
 * Tobias AX attribute connector service
 */
class TobiasAx_AttributeConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Gets attributes
     * @param  string $objectId   object id
     * @param  string $objectType usually 'Building'
     * @param  string $category   attribute category to filter on e.g. 'Woningwaardering', 'Verhuur'
     * @param  array $data        additional template data
     * @return GuzzleClient
     */
    public function getAttributes($objectId, $objectType, $category = null, $data = [])
    {
        $data = array_merge([
            'id' => $objectId,
            'objectType' => $objectType,
            'category' => $category,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetAttributes',
            'tobiasax/templates/soap/attribute/get_attributes',
            $data
        );

        return $request;
    }
}
