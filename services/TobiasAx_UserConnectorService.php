<?php

namespace Craft;

/**
 * Tobias AX user connector service
 */
class TobiasAx_UserConnectorService extends TobiasAx_ConnectorService
{
    /**
     * Creates a user
     * @param TobiasAx_UserModel $user The user to create
     * @return GuzzleClient
     */
    public function createUser($user)
    {
        $data = [
            'user' => $user->getCreateAttributes()
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'CreateUser',
            'tobiasax/templates/soap/user/create',
            $data
        );

        return $request;
    }

    /**
     * Get temporary password by username (username might also be an emailaddress)
     * @param string $username
     * @return GuzzleClient
     */
    public function getTemporaryPassword($username)
    {
        $request = craft()->tobiasAx_request->createRequest(
            'GetTemporaryPassword',
            'tobiasax/templates/soap/user/get_temporary_password',
            ['username' => $username]
        );

        return $request;
    }

    /**
     * Changes password by temporary password
     * @param string $username
     * @param string $tempPassword
     * @param string $password
     * @param string $tempUsername
     * @return GuzzleClient
     */
    public function changePasswordByTemporaryPassword($username, $tempPassword, $password, $tempUsername = null)
    {
        $request = craft()->tobiasAx_request->createRequest(
            'ChangePasswordByTemporaryPassword',
            'tobiasax/templates/soap/user/change_password_by_temp_password',
            [
                'tempUsername' => $tempUsername,
                'tempPassword' => $tempPassword,
                'username' => $username,
                'password' => $password,
            ]
        );

        return $request;
    }

    /**
     * Updates user login credentials
     * @param string $currentUsername
     * @param string $currentPassword
     * @param string $newUsername
     * @param string $newPassword
     * @return GuzzleClient
     */
    public function updateLoginCredentials($currentUsername, $currentPassword, $newUsername, $newPassword)
    {
        $request = craft()->tobiasAx_request->createRequest(
            'UpdateLoginCredentials',
            'tobiasax/templates/soap/user/update_credentials',
            [
                'currentUsername' => $currentUsername,
                'currentPassword' => $currentPassword,
                'newUsername' => $newUsername,
                'newPassword' => $newPassword,
            ]
        );

        return $request;
    }

    /**
     * Retrieves the username by it's email
     * @param string $email
     * @return GuzzleClient
     */
    public function getUsernameByEmail($email)
    {
        $data = [
            'email' => $email
        ];

        $request = craft()->tobiasAx_request->createRequest(
            'GetUsernameByEmail',
            'tobiasax/templates/soap/user/get_username_by_email',
            $data
        );

        return $request;
    }
}
