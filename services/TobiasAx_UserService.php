<?php

namespace Craft;

use Exception;

/**
 * Tobias AX User service
 */
class TobiasAx_UserService extends BaseApplicationComponent
{
    /**
     * @var string
     */
    const UNKNOWN_ERROR = 'An unknown error occurred: {message}';

    /**
     * @var string
     */
    const ERROR_CREATE_UNKNOWN = 'An unknown error occurred creating user';

    /**
     * @var string
     */
    const ERROR_GET_UNKNOWN = 'An unknown error occurred retrieving user';

    /**
     * @var string
     */
    const ERROR_ALREADY_EXISTS = 'User already exists';

    /**
     * @var string
     */
    const ERROR_INVALID_PASSWORD = 'New password doesn\'t meet requirements';

    /**
     * @var string
     */
    const ERROR_INVALID_TEMP_PASSWORD = 'Invalid temporary password';

    /**
     * @var string
     */
    const ERROR_INVALID_CREDENTIALS = 'Invalid credentials';

    /**
     * @var string
     */
    const EXCEPTION_ALREADY_EXISTS = 'Dubbele gebruikersnaam';

    /**
     * @var string
     */
    const EXCEPTION_INVALID_PASSWORD = 'Ongeldig nieuw wachtwoord';

    /**
     * @var string
     */
    const EXCEPTION_INVALID_PASSWORD_CREDENTIAL = 'Wachtwoord is ongeldig';

    /**
     * @var string
     */
    const EXCEPTION_INVALID_TEMP_PASSWORD = 'Ongeldig tijdelijk wachtwoord';

    /**
     * @var string
     */
    const EXCEPTION_INVALID_LOGIN = 'Valideren persoon mislukt';

    /**
     * @param TobiasAx_UserModel $user the user to create
     * @param  string $personId The person Id to link the user to
     * @return TobiasAx_UserModel
     * @throws Exception $e Thrown when an error occurred while creating the person
     */
    public function createUser($user)
    {
        $service = craft()->tobiasAx_userConnector;

        try {
            try {
                $envelope = $service->sendRequest($service->createUser($user));
                $result = $service->extractSingle($envelope, 'Body/xmlns:CreateUserResponse/xmlns:CreateUserResult');
            } catch (Exception $e) {
                $exception = new TobiasAx_SoapException(static::ERROR_CREATE_UNKNOWN, $e->getCode(), $e);

                if (stristr($e->getMessage(), static::EXCEPTION_ALREADY_EXISTS)) {
                    $exception = new TobiasAx_SoapException(static::ERROR_ALREADY_EXISTS, $e->getCode(), $e);
                }

                throw $exception;
            }
        } catch (Exception $e) {
            throw new Exception(Craft::t('Error creating user: {message}', ['message' => $e->getMessage()]));
        }

        return new TobiasAx_UserModel($result);
    }

    /**
     * Get temporary password by username (username might also be an emailaddress)
     * @param string $username
     * @return string
     */
    public function getTemporaryPassword($username)
    {
        $service = craft()->tobiasAx_userConnector;
        $envelope = $service->sendRequest($service->getTemporaryPassword($username));
        $result = $service->extractSingle($envelope, 'Body/xmlns:GetTemporaryPasswordResponse/xmlns:GetTemporaryPasswordResult');

        if ($result == null) {
            throw new Exception(Craft::t('Error requesting temporary password for username “{username}”', ['username' => $username]));
        }

        return array_shift($result);
    }

    /**
     * Changes password by temporary password
     * @param string $username
     * @param string $tempPassword
     * @param string $password
     * @param string $tempUsername
     * @return mixed
     */
    public function changePasswordByTemporaryPassword($username, $tempPassword, $password, $tempUsername = null)
    {
        $service = craft()->tobiasAx_userConnector;
        $request = $service->changePasswordByTemporaryPassword($username, $tempPassword, $password, $tempUsername);
        $errorMessage = 'Error changing password using temporary password. ';

        try {
            $envelope = $service->sendRequest($request);
            $result = $service->extractSingle($envelope, 'Body/xmlns:ChangePasswordByTemporaryPasswordResponse');
        } catch (Exception $e) {
            $exception = new Exception($errorMessage.Craft::t(static::UNKNOWN_ERROR, ['message' => $e->getMessage()]), TobiasAX_UserError::UNKOWN, $e);

            if (stristr($e->getMessage(), static::EXCEPTION_INVALID_TEMP_PASSWORD)) {
                $exception = new TobiasAx_SoapException($errorMessage.static::ERROR_INVALID_TEMP_PASSWORD, TobiasAX_UserError::INVALID_TEMP_PASSWORD, $e);
            } elseif (stristr($e->getMessage(), static::EXCEPTION_INVALID_PASSWORD)) {
                $exception = new TobiasAx_SoapException($errorMessage.static::ERROR_INVALID_PASSWORD, TobiasAX_UserError::INVALID_PASSWORD, $e);
            }

            throw $exception;
        }

        return $result;
    }

    /**
     * Updates user login credentials
     * @param string $currentUsername
     * @param string $currentPassword
     * @param string $newUsername
     * @param string $newPassword
     */
    public function updateLoginCredentials($currentUsername, $currentPassword, $newUsername, $newPassword)
    {
        $service = craft()->tobiasAx_userConnector;
        $request = $service->updateLoginCredentials($currentUsername, $currentPassword, $newUsername, $newPassword);
        $errorMessage = 'Error updating login credentials. ';

        try {
            $service->sendRequest($request);
        } catch (Exception $e) {
            $exception = new Exception($errorMessage.Craft::t(static::UNKNOWN_ERROR, ['message' => $e->getMessage()]), TobiasAX_UserError::UNKOWN, $e);

            if (stristr($e->getMessage(), static::EXCEPTION_INVALID_LOGIN)) {
                $exception = new TobiasAx_SoapException($errorMessage.static::ERROR_INVALID_CREDENTIALS, TobiasAX_UserError::INVALID_CREDENTIALS, $e);
            } elseif (stristr($e->getMessage(), static::EXCEPTION_INVALID_PASSWORD_CREDENTIAL)) {
                $exception = new TobiasAx_SoapException($errorMessage.static::ERROR_INVALID_PASSWORD, TobiasAX_UserError::INVALID_PASSWORD, $e);
            }

            throw $exception;
        }
    }

    /**
     * Get username by email
     * @param string $email
     * @return string
     * @throws Exception
     */
    public function getUsernameByEmail($email)
    {
        $service = craft()->tobiasAx_userConnector;

        try {
            try {
                $envelope = $service->sendRequest($service->getUsernameByEmail($email));
                $result = $service->extractSingle($envelope, 'Body/xmlns:GetUsernameByEmailResponse/xmlns:GetUsernameByEmailResult');
            } catch (Exception $e) {
                $exception = new TobiasAx_SoapException(static::ERROR_GET_UNKNOWN . ". {$e->getMessage()}", 0, $e);
                throw $exception;
            }
        } catch (Exception $e) {
            throw new Exception(Craft::t('Error retrieving user by email. {message}', ['message' => $e->getMessage()]), 0, $e);
        }

        return $result;
    }
}
