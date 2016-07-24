<?php namespace Moregold\Infrastructure\Clients;

use stdClass;

interface HttpClientInterface
{
    /**
     * Add custom header to client
     *
     * @param string $header
     * @param string $value
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setHeader($header, $value);

    /**
     * Override base_url parameter
     *
     * @param $base_url
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setBaseUrl($base_url);

    /**
     * Get payload from client request
     *
     * @return stdClass
     */
    public function getPayload();

    /**
     * Get payload from client request
     *
     * @param string $payload
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setPayload($payload);

    /**
     * Get status from client request
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Get message from client request
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get status from client request
     *
     * @param  integer $status
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setStatus($status);

    /**
     * Execute send request
     *
     * @param  string $resource
     * @param  array $parameters
     * @param  string $method
     * @param  string|null $dataType
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function sendRequest($resource = null, $parameters = [], $method = 'get', $dataType = null);
}
