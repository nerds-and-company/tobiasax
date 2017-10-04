<?php

namespace Craft;

use Exception;

/**
 * Tobias AX registration store service
 */
class TobiasAx_RegistrationStoreService extends BaseApplicationComponent
{
    /**
     * Encrypts data and saves registration
     * @param TobiasAx_RegisterModel $registration
     * @return TobiasAx_RegisterModel
     */
    public function saveRegistration(TobiasAx_RegisterModel $registration)
    {
        $record = new TobiasAx_RegistrationRecord();
        $record->data = $this->encryptData($registration);

        if ($record->save()) {
            $registration->id = $record->id;
        } else {
            throw new Exception(Craft::t('Unable to save encrypted registration'));
        }

        return $registration;
    }

    /**
     * Finishes the registration and clears sensitive data
     * @param int $registrationId
     * @param string $paymentCode
     * @param callable $callback
     * @throws Exception
     */
    public function finishRegistration($registrationId, $paymentCode, callable $callback)
    {
        $record = TobiasAx_RegistrationRecord::model()->findById($registrationId);

        try {
            if ($record == null) {
                throw new Exception(Craft::t('No record found'));
            } else if ($paymentCode == null) {
                throw new Exception(Craft::t('No payment code given'));
            } else if ($record->data == null) {
                throw new Exception(Craft::t('Data already processed'));
            } elseif (($data = $this->decryptData($record->data)) === false) {
                throw new Exception(Craft::t('Data can not be deserialized'));
            }

            // update payment code
            $record->paymentCode = $paymentCode;

            // save payment status
            if ($record->save() == false) {
                throw new Exception(Craft::t('Error occured while trying to save registration record'));
            }

            // populate data
            $data = array_merge($data, [
                'id' => $record->id,
                'paymentCode' => $record->paymentCode,
            ]);

            // create the actual registration
            $callback($data);

            // clear encrypted registration data
            $this->clearData($record->id);

        } catch (Exception $e) {
            throw new Exception(Craft::t('Unable to finish registration “{registrationId}”. {message}', ['registrationId' => $registrationId, 'message' => $e->getMessage()]));
        }
    }

    /**
     * Stores TobiasAX registration id
     * @param int $registerId internal registation id
     * @param  string $registrationId TobiasAX registration id
     * @return bool
     */
    public function saveRegistrationId($registerId, $registrationId)
    {
        $record = TobiasAx_RegistrationRecord::model()->findById($registerId);
        $record->registrationId = $registrationId;

        return $record->save();
    }

    /**
     * Clears encrypted data for privacy reasons
     * @param int $registerId
     * @return TobiasAx_RegistrationRecord
     * @throws Exception
     */
    public function clearData($registerId)
    {
        $record = TobiasAx_RegistrationRecord::model()->findById($registerId);
        $record->data = null;

        if (!$record->save()) {
            throw new Exception(Craft::t('Unable to save cleared registration data'));
        }

        return $record;
    }

    /**
     * Clears records data by age
     * @return int number of affected rows
     */
    public function clearRecordsData()
    {
        $defaultMaxAge = 60*60*24; // 24 hours
        $maxAge = craft()->config->get('registrationStorageDuration') ?? $defaultMaxAge; // in seconds

        $affectedRows = TobiasAx_RegistrationRecord::model()
            ->updateAll(['data' => null], '(UNIX_TIMESTAMP() - UNIX_TIMESTAMP(dateCreated)) > :age', [':age' => $maxAge]);

        return $affectedRows;
    }

    /**
     * Eecrypts and unserializes given data
     * @param TobiasAx_RegisterModel $model
     * @return mixed
     */
    protected function encryptData(TobiasAx_RegisterModel $model)
    {
        $data = serialize(ModelHelper::packageAttributeValue($model));
        return craft()->tobiasAx_cryptography->encrypt($data);
    }

    /**
     * Decrypts and unserializes given data
     * @param string $data
     * @return mixed
     */
    protected function decryptData($data)
    {
        $decryptedValues = craft()->tobiasAx_cryptography->decrypt($data);
        return unserialize($decryptedValues);
    }
}
