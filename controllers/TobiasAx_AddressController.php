<?php

namespace Craft;

/**
 * TobiasAx - Registration Controller
 */
class TobiasAx_AddressController extends BaseController
{
    protected $allowAnonymous = true;

    /**
     * Resolves an address on zipcode and housenumber
     * @return string Address json string
     */
    public function actionResolveAddress()
    {
        $zipcode = craft()->request->getRequiredQuery('zipcode');
        $houseNumber = craft()->request->getRequiredQuery('houseno');

        try {
            $address = craft()->tobiasAx_address->getByZipcodeAndHouseNumber($zipcode, $houseNumber);

            if (!empty($address->Street)) {
                return $this->returnJson($address->getGetAttributes());
            } else {
                return $this->returnJson(['error' => 1]);
            }
        } catch (Exception $e) {
            return $this->returnJson(['error' => 2]);
        }
    }
}
