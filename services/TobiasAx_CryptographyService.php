<?php

namespace Craft;

/**
 * Tobias AX - Cryptography service
 */
class TobiasAx_CryptographyService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    private $encryptKey;

    /**
     * Service init
     */
    public function init()
    {
        $this->encryptKey = craft()->config->get('encrypt_key');

        if ($this->encryptKey == null) {
            throw new TobiasAx_SystemException(Craft::t('Required encryption key is not configured'));
        }
    }

    /**
     * Encrypts text using the given cipher method and
     * initialisation vector. If no intitialisation vector is provided
     * a random value is used for each encryption operation.
     */
    public function encrypt($plaintext, $method = 'AES-256-CBC')
    {
        // Generate an initialisation vector of the required length
        $random = random_int(0, PHP_INT_MAX);
        $initialisationVector = mb_substr(md5($random), 0, openssl_cipher_iv_length($method));

        // Ecrypt the text
        $cipher = openssl_encrypt($plaintext, $method, $this->encryptKey, 0, $initialisationVector);

        // Append the initilisation vector before the cipher text
        $data = $initialisationVector.$cipher;

        return $data;
    }

    /**
     * Decrypts data by the given cipher method.
     */
    public function decrypt($data, $method = 'AES-256-CBC')
    {
        // Get the length of initialisation vector for give cipher method
        $ivLength = openssl_cipher_iv_length($method);

        // Cipher shorter than expected minimum length
        if (strlen($data) <= $ivLength) {
            return false;
        }

        // Split the data into initialisation vector and cipher text
        $initialisationVector = mb_substr($data, 0, $ivLength);
        $cipher = mb_substr($data, $ivLength);

        // Decrypt the cipher
        $plaintext = openssl_decrypt($cipher, $method, $this->encryptKey, 0, $initialisationVector);

        return $plaintext;
    }
}
