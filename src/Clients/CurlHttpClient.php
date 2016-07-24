<?php namespace Moregold\Infrastructure\Clients;

use \Exception;

class CurlHttpClient implements HttpClientInterface
{
    private $base_url;

    /**
     * The resulting status code
     *
     * @var integer
     */
    private $status = null;

    /**
     * The resulting payload
     *
     * @var \stdClass
     */
    private $payload = null;

    /**
     * Additional headers to include in requests
     *
     * @var array
     */
    private $headers = [];

    /**
     * Message from request
     *
     * @var string
     */
    private $message = null;

    public function __construct($base_url = null, $defaults = [])
    {
        $this->base_url = $base_url;
        // Include setters
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
        $this->base_url = $base_url;
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
     * @param string $payload
     *
     * @return \Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setPayload($payload)
    {
        try {
            $payload = json_decode($payload);
            if (is_object($payload)) {
                $this->payload = $payload;
                return $this;
            } else {
                throw new Exception("invalid payload after json conversion");
            }
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            $this->payload = null;
        }
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
     * @return Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Execute send request
     *
     * @param  string $method
     * @param  array $parameters
     *
     * @return Moregold\Infrastructure\Clients\HttpClientInterface
     */
    public function sendRequest($resource = null, $parameters = [], $method = 'get', $dataType = null)
    {
        try {
            $url = $this->buildUrl($resource);

            if ($method == 'post') {
                $is_post = true;
            } else {
                $is_post = false;
            }

            $fields_string = '';
            switch($dataType) {
                case 'JSON':
                    $fields_string = json_encode($parameters);
                    $this->headers['Content-Type'] = 'application/json';
                    break;
                default:
                    $fields_string = http_build_query($parameters);
                    break;
            }


            $ch = curl_init();
            if ($is_post) {
                curl_setopt($ch, CURLOPT_POST, $dataType=='JSON' ? 1 : count($parameters));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            } else {
                $url .= (strrpos($url, '?') != (strlen($url) - 1) ? '?' : '').$fields_string;
            }

            $headers = $this->buildHeadersForCurl();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            $http_status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error_message = curl_error($ch);

            curl_close($ch);

            return $this->setMessage($error_message)
                ->setStatus($http_status)
                ->setPayload($result);
        } catch (Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
            return $this->setMessage($e->getMessage())
                ->setStatus(999);
        }
    }

    private function buildUrl($path = '')
    {
        $url = $this->base_url;
        if (!$this->endsWith($url, '/') && !$this->startsWith($url, '/')) {
            $url .= '/';
        }
        $url .= $path;
        return $url;
    }

    private function buildHeadersForCurl()
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key.': '.$value;
        }
        return $headers;
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    private function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}
