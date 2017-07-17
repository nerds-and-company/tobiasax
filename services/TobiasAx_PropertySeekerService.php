<?php

namespace Craft;

/**
 * TobiasAx - Property seeker service
 */
class TobiasAx_PropertySeekerService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const EXCEPTION_SEEKER_NOT_FOUND = 'Zoekende niet gevonden';

    /**
     * Gets a property seeker by person ID
     * @param string $personId
     * @return TobiasAx_PropertySeekerModel|null
     */
    public function getPropertySeekerByPersonId($personId)
    {
        $service = craft()->tobiasAx_propertySeekerConnector;

        try {
            $envelope = $service->sendRequest($service->getPropertySeekerByPersonId($personId));
            $result = $service->extractSingle($envelope, 'Body/xmlns:GetPropertySeekerByPersonIdResponse/xmlns:GetPropertySeekerByPersonIdResult');
        } catch (Exception $e) {
            if (stristr($e->getMessage(), static::EXCEPTION_SEEKER_NOT_FOUND)) {
                // seeker is not found by the given personId, return a null
                return null;
            }
            throw new TobiasAx_SoapException('Unknown error getting property seeker by person id '.$personId.': '.$e->getMessage(), null, $e);
        }

        return new TobiasAx_PropertySeekerModel($result);
    }
}
