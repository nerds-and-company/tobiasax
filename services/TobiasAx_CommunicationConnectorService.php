<?php

namespace Craft;

/**
 * Tobias AX communication connector service
 */
class TobiasAx_CommunicationConnectorService extends TobiasAx_ConnectorService
{

    /**
     * Updates a communication
     * @param TobiasAx_CommunicationModel $communication
     * @return GuzzleClient
     */
    public function updateCommunication($communication)
    {
        $data = [
            'communication' => $communication->getUpdateAttributes()
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'UpdateCommunication',
            'tobiasax/templates/soap/communication/update',
            $data
        );

        return $request;
    }
}
