<?php

namespace Craft;

/**
 * Tobias AX asset connector service
 */
class TobiasAx_AssetConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Gets assets by entity type
     * @param TobiasAx_RealEstateModel $realEstateObject
     * @param TobiasAX_AssetsEntityName $entityName
     * @param  string $data additional template data
     * @return GuzzleClient
     */
    public function getAssets($realEstateObject, $entityName, $data = [])
    {
        if ($entityName == TobiasAX_AssetsEntityName::REAL_ESTATE) {
            $entityId = $realEstateObject->Id;
        } else if ($entityName == TobiasAX_AssetsEntityName::BUILDING) {
            $entityId = $realEstateObject->BuildingId;
        } else {
            throw new TobiasAx_SystemException(Craft::t('Unknown assets entityName “{entityName}”.', array('entityName' => $entityName)));
        }

        $data = array_merge([
            'entityName' => $entityName,
            'entityId' => $entityId,
        ], $data);

        $request = craft()->tobiasAx_request->createRequest(
            'GetDocuRefsByEntityName',
            'tobiasax/templates/soap/assets/get_docu_refs_by_entity_name',
            $data
        );

        return $request;
    }

    /**
     * Gets asset resource
     * @param string $documentId
     * @param string $checksum
     * @return GuzzleClient
     */
    public function getAssetResource($documentId, $checksum)
    {
        $request = craft()->tobiasAx_request->createRequest(
            'GetDocumentFile',
            'tobiasax/templates/soap/assets/get_document_file',
            [
                'id' => $documentId,
                'checksum' => $checksum,
            ]
        );

        return $request;
    }
}
