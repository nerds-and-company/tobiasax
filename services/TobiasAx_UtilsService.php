<?php

namespace Craft;

/**
 * Tobias AX utils service
 *
 * Contains util methods
 */
class TobiasAx_UtilsService extends BaseApplicationComponent
{
    /**
     * GoogleMaps_MapDataModel
     * @param string $address
     * @return GoogleMaps_MapDataModel
     */
    public function geocodeAddress($address)
    {
        $geocode = craft()->googleMaps_geocoder->geocode($address);
        $mapDataModel = new GoogleMaps_MapDataModel([]);

        if ($geocode->status == 'OK') {
            $geocodeResponse = array_shift($geocode->results);
            $mapDataModel->addMarker(GoogleMaps_MarkerModel::populateModel([
                'lat' => $geocodeResponse->geometry->location->lat,
                'lng' => $geocodeResponse->geometry->location->lng,
                'address' => $geocodeResponse->formatted_address,
                'addressComponents' => $geocodeResponse->address_components,
                'content' => GoogleMaps_MarkerModel::defaultContent($geocodeResponse->formatted_address),
            ]));
        }

        return $mapDataModel;
    }
}
