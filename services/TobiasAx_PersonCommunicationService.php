<?php

namespace Craft;

use Exception;

/**
 * Tobias AX Person Communication service
 */
class TobiasAx_PersonCommunicationService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const EXCEPTION_ALREADY_EXISTS = "Overlappende";

    /**
     * @var string
     */
    const ERROR_ALREADY_EXISTS = "Communication already exists";

    /**
     * @var string
     */
    const ERROR_CREATE_UNKNOWN = "Unknown error creating communication: ";

    /**
     * @var string
     */
    const ERROR_UPDATE_UNKNOWN = "Unknown error updating communication: ";

    /**
     * @param TobiasAx_CommunicationModel $communication The communication to add to the person
     * @param string $personId The id of the person to add the address to
     * @return TobiasAx_CommunicationModel
     * @throws TobiasAx_SoapException $e Thrown when a person communication can't be created
     */
    public function createPersonCommunication($communication, $personId)
    {
        $service = craft()->tobiasAx_personConnector;

        try {
            $envelope = $service->sendRequest($service->createPersonCommunication($communication, $personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:CreatePersonCommunicationResponse/xmlns:CreatePersonCommunicationResult');
        } catch (TobiasAx_SoapException $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_ALREADY_EXISTS)) {
                throw new TobiasAx_SoapException(static::ERROR_ALREADY_EXISTS, null, $e);
            }
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . $e->getMessage(), null, $e);
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_CommunicationModel($result);
    }

    /**
     * @param TobiasAx_CommunicationModel[] $communications
     * @param string $personId
     * @return TobiasAx_CommunicationModel[]
     * @throws TobiasAx_SoapException $e
     */
    public function upsertPersonCommunications($communications, $personId)
    {
        $responseCommunications = array();
        foreach ($communications as $communication) {
            if (!empty($communication->Id)) {
                $responseCommunication = $this->updatePersonCommunication($communication);
                $responseCommunications[] = $responseCommunication;
            } else {
                $responseCommunication = $this->createPersonCommunication($communication, $personId);
                $responseCommunications[] = $responseCommunication;
            }
        }

        return $responseCommunications;
    }

    /**
     * @param TobiasAx_CommunicationModel $communication
     * @return TobiasAx_CommunicationModel
     * @throws TobiasAx_SoapException
     */
    public function updatePersonCommunication($communication)
    {
        $service = craft()->tobiasAx_communicationConnector;

        try {
            $envelope = $service->sendRequest($service->updateCommunication($communication));
            $result = $service->extractSingle($envelope, 'Body/xmlns:UpdateCommunicationResponse/xmlns:UpdateCommunicationResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException(static::ERROR_UPDATE_UNKNOWN . $e->getMessage(), null, $e);
        }

        return new TobiasAx_CommunicationModel($result);
    }
}
