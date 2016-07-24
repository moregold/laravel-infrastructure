<?php namespace Moregold\Infrastructure\Clients\User;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Moregold\Infrastructure\Clients\HttpClientInterface as Client;
use Moregold\Infrastructure\Clients\User\Contracts\UserClientInterface;

class UserApiClient implements UserClientInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->setBaseUrl(Config::get('services.users.base_url'));
    }

    /**
     * Check against the User API to see if current user is authenticated
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function verifyAuthenticated($access_token = null)
    {
        $this->setAuthorizationHeader($access_token);
        return $this->client->sendRequest('api/auth/validate-access', [], 'post');
    }

    /**
     * Get Current user account from User API
     *
     * @return object
     */
    public function getCurrentUser($access_token = null)
    {
        $this->setAuthorizationHeader($access_token);
        return $this->client->sendRequest('api/user/current-user', [], 'get')->getPayload();
    }

    /**
     * Get user info for a list of users
     *
     * @return array|null
     */
    public function getUsersInfo($ids = [])
    {
        $this->setAuthorizationHeader(Cache::get('users_oauth_access_token', 'we-must-refresh'));
        $response = $this->client->sendRequest('api/users/info', ['ids' => $ids], 'post', 'JSON');
        $status_code = $response->getStatus();
        if ($status_code == 200) {
            return $response->getPayload()->users;
        } else if ($status_code == 401) {
            if (!is_null($this->authenticateWithClientCredentials())) {
                return $this->getUsersInfo($ids);
            }
        }
        Log::error('HTTP error code '.$status_code.' on /api/users/info');
        return null;
    }

    /**
     * Get user info for a single user ID
     *
     * @return object|null
     */
    public function getUserInfo($id = null)
    {
        $result = $this->getUsersInfo([$id]);
        if (count($result)) {
            $users = collect($result);
            return $users->first(function ($key, $value) use ($id) {
                return $value->id == $id;
            });
        }
        return null;
    }

    /**
     * Login user via OAuth password grant
     *
     * @param array $attributes
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function requestAccess()
    {
        $payload = [
            'grant_type' => 'client_credentials',
            'client_id' => Config::get('services.users.key'),
            'client_secret' => Config::get('services.users.secret'),
            'scope' => 'admin'
        ];

        return $this->client->sendRequest('api/auth/request-access', $payload, 'post');
    }

    /**
     * Login with our client credentials and generate an admin-scoped access token
     *
     * @return object
     */
    private function authenticateWithClientCredentials()
    {
        $response = $this->client->sendRequest('api/auth/request-access', [
            'grant_type' => 'client_credentials',
            'client_id' => Config::get('services.users.key'),
            'client_secret' => Config::get('services.users.secret'),
            'scope' => 'admin',
        ], 'post', 'JSON');
        if ($response->getStatus() == 200) {
            $auth = $response->getPayload()->auth;
            Cache::put('users_oauth_access_token', $auth->access_token, $auth->expires_in/60);
            return $auth;
        }
        return null;
    }

    /**
     * Set Authorization: Bearer <token> header needed for any OAuth-secured API request to users API
     */
    private function setAuthorizationHeader($access_token)
    {
        return $this->client->setHeader('Authorization', 'Bearer ' . $access_token);
    }
}
