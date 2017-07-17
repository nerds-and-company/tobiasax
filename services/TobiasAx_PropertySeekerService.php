<?php

namespace Craft;

/**
 * TobiasAx - Property seeker service
 */
class TobiasAx_PropertySeekerService extends BaseApplicationComponent
{
    /**
     * Gets a property seeker by person ID
     * @param string $personId
     * @return TobiasAx_PropertySeekerModel
     */
    public function getPropertySeekerByPersonId($personId)
    {
        $service = craft()->tobiasAx_propertySeekerConnector;

        try {
            $envelope = $service->sendRequest($service->getPropertySeekerByPersonId($personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:GetPropertySeekerByPersonIdResponse/xmlns:GetPropertySeekerByPersonIdResult');
        } catch (Exception $e) {
            throw new TobiasAx_SoapException('Unknown error getting property seeker by person id {'.$personId.'}: '.$e->getMessage(), null, $e);
        }

        return new TobiasAx_PropertySeekerModel($result);
    }
}
