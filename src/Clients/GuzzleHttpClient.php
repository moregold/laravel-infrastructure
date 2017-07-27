<?php namespace Moregold\Infrastructure\Clients;

use \Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;

class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * The http client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client = null;

    protected $base_uri = null;

    protected $defaults = [];

    protected $headers = [];

    /**
     * The resulting status code
     *
     * @var integer
     */
    protected $status = null;

    /**
     * The resulting payload
     *
     * @var \stdClass
     */
    protected $payload = null;

    /**
     * Message from request
     *
     * @var string
     */
    protected $message = null;

    /**
     * Guzzle http client constructor
     *
     * @param string $base_url base url/domain for client
     */
    public function __construct($base_url = null, $defaults = [])
    {
        $this->setBaseUrl($base_url);
        $this->defaults = $defaults;
        $this->client = new Client();
    }

    /**
     * Add custom header to client
     *
     * @param string $header
     * @param string $value
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;
        return $this;
    }

    /**
     * Override base_url parameter
     *
     * @param $base_url
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setBaseUrl($base_url)
    {
        $this->base_uri = new Uri($base_url);
        return $this;
    }

    /**
     * Get payload from client request
     *
     * @return \stdClass
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Get payload from client request
     *
     * @param \GuzzleHttp\Psr7\Response $payload
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setPayload($payload = '')
    {
        try {
            $_contents = $payload->getBody()->getContents();
            $this->payload = !is_null(json_decode($_contents)) ? json_decode($_contents) : $_contents;
        } catch (Exception $e) {
            $this->setStatus($payload->getStatusCode());
            $this->setMessage($e->getMessage());
            Log::error($e->getMessage());
            Log::debug($payload->getBody()->getContents());
        }
        return $this;
    }

    /**
     * Get status from client request
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status from client request
     *
     * @param  integer $status
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set message from client request
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setMessage($message = null)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message from client request
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Initiate request with given path, parameters and optional method (defaults to get)
     *
     * @param  string $resource
     * @param  string $method
     * @param  array $parameters
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function sendRequest($resource = null, $parameters = [], $method = 'get', $dataType = null)
    {
        $options = [];

        if (!empty($parameters)) {
            if ($method == 'get') {
                $options['query'] = $parameters;
            } else {
                // Guzzle will set header content type according to the option key
                // see http://docs.guzzlephp.org/en/latest/quickstart.html?highlight=post%20field
                switch ($dataType) {
                    case 'JSON':
                        // Content-Type: application/json
                        $options['json'] = $parameters;
                        break;
                    default:
                        $options['form_params'] = $parameters;
                        break;
                }
            }
        }

        $options['headers'] = $this->headers;
        $options['verify'] = false;

        try {
            $uri = Uri::resolve($this->base_uri, $resource);
            $response = $this->client->request($method, $uri, $options);
            return $this->setStatus((int)$response->getStatusCode())
                ->setPayload($response);
        } catch (RequestException $e) {
            Log::error($e);
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $this->setMessage($e->getMessage());
                return $this->setStatus((int)$response->getStatusCode())
                    ->setPayload($response);
            }
            
            return $this->setStatus(999);
        }
    }
}
