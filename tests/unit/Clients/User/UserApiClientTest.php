<?php namespace Clients\User;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use \Mockery as m;
use Moregold\Infrastructure\Clients\User\UserApiClient;

class UserApiClientTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->client = m::mock('Moregold\Infrastructure\Clients\HttpClientInterface');

        Config::shouldReceive('get')
            ->with('services.users.base_url')
            ->andReturn('foo');

        $this->client->shouldReceive('setBaseUrl')
            ->with(Config::get('services.users.base_url'))
            ->andReturn($this->client);

        $this->ids = ['abc', 'def'];

        $this->url = 'url';
        $this->key = 'key';

        $this->api = new UserApiClient($this->client);
    }

    protected function tearDown()
    {
        m::close();
    }

    // tests
    public function test_it_verfies_user_is_authenticated()
    {
        $access_token = 'token';

        $this->client->shouldReceive('setHeader')
            ->with('Authorization', 'Bearer '. $access_token)
            ->once();

        $this->client->shouldReceive('sendRequest')
            ->once()
            ->with('api/auth/validate-access', [], 'post');

        $this->api->verifyAuthenticated($access_token);
    }

    public function test_it_gets_current_user()
    {
        $access_token = 'token';

        $this->client->shouldReceive('setHeader')
            ->with('Authorization', 'Bearer '. $access_token)
            ->once();

        $this->client->shouldReceive('sendRequest')
            ->once()
            ->with('api/user/current-user', [], 'get')
            ->andReturn(m::self());

        $this->client->shouldReceive('getPayload')
            ->once();

        $this->api->getCurrentUser($access_token);
    }

    public function test_it_gets_users_info_with_valid_token()
    {
        $token = 'lol';

        Cache::shouldReceive('get')
            ->with('users_oauth_access_token', 'we-must-refresh')
            ->once()
            ->andReturn($token);

        $this->client->shouldReceive('setHeader')
            ->with('Authorization', 'Bearer ' . $token)
            ->once();

        $response = m::mock();

        $this->client->shouldReceive('sendRequest')
            ->with('api/users/info', ['ids' => $this->ids], 'post', 'JSON')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getStatus')
            ->once()
            ->andReturn(200);

        $response->shouldReceive('getPayload')
            ->once()
            ->andReturn((object) ['users' => []]);

        $this->api->getUsersInfo($this->ids);
    }

    public function test_it_gets_users_info_with_invalid_token()
    {
        $token = 'lol';

        Cache::shouldReceive('get')
            ->with('users_oauth_access_token', 'we-must-refresh')
            ->twice()
            ->andReturn('we-must-refresh', $token);

        $this->client->shouldReceive('setHeader')
            ->with('Authorization', 'Bearer ' . 'we-must-refresh')
            ->once();

        $response = m::mock();

        $response->shouldReceive('getStatus')
            ->twice()
            ->andReturn(401, 200);

        $response->shouldReceive('getPayload')
            ->once()
            ->andReturn((object) ['users' => []]);

        $this->client->shouldReceive('sendRequest')
            ->with('api/users/info', ['ids' => ['lolol']], 'post', 'JSON')
            ->twice()
            ->andReturn($response);

        Config::shouldReceive('get')
            ->with('services.users.key')
            ->once()
            ->andReturn('the_key');

        Config::shouldReceive('get')
            ->with('services.users.secret')
            ->once()
            ->andReturn('the_secret');

        $auth_response = m::mock();

        $this->client->shouldReceive('sendRequest')
            ->with('api/auth/request-access', [
                'grant_type' => 'client_credentials',
                'client_id' => 'the_key',
                'client_secret' => 'the_secret',
                'scope' => 'admin',
            ], 'post', 'JSON')
            ->once()
            ->andReturn($auth_response);

        $auth_response->shouldReceive('getStatus')
            ->once()
            ->andReturn(200);

        $auth_response->shouldReceive('getPayload')
            ->once()
            ->andReturn((object) ['auth' => (object) ['access_token' => $token, 'expires_in' => 3600]]);

        Cache::shouldReceive('put')
            ->with('users_oauth_access_token', $token, 60)
            ->once();

        $this->client->shouldReceive('setHeader')
            ->with('Authorization', 'Bearer ' . $token)
            ->once();

        $this->api->getUserInfo('lolol');
    }

    public function test_it_cannot_get_user_info_with_500()
    {
        $token = 'lol';

        Cache::shouldReceive('get')
            ->with('users_oauth_access_token', 'we-must-refresh')
            ->twice()
            ->andReturn('we-must-refresh', $token);

        $this->client->shouldReceive('setHeader')
            ->with('Authorization', 'Bearer ' . 'we-must-refresh')
            ->once();

        $response = m::mock();

        $response->shouldReceive('getStatus')
            ->once()
            ->andReturn(500);

        $this->client->shouldReceive('sendRequest')
            ->with('api/users/info', ['ids' => ['lolol']], 'post', 'JSON')
            ->once()
            ->andReturn($response);

        Log::shouldReceive('error')->once();

        $this->api->getUserInfo('lolol');
    }

    protected function setUpConfig()
    {
        Config::shouldReceive('get')
            ->with('services.users.base_url')
            ->andReturn($this->url);

        Config::shouldReceive('get')
            ->with('services.users.key')
            ->andReturn($this->key);

        Config::shouldReceive('get')
            ->with('services.users.secret')
            ->andReturn($this->key);
    }

    // tests
    public function test_it_requests_access()
    {
        $this->client->shouldReceive('sendRequest')
            ->once();

        $this->api->requestAccess();
    }
}

