<?php namespace Moregold\Infrastructure\Clients\Sparrow;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Moregold\Infrastructure\Clients\HttpClientInterface as Client;
use Moregold\Infrastructure\Clients\Sparrow\Contracts\SparrowClientInterface;

class SparrowClient implements SparrowClientInterface
{
    const TRANS_TYPE_SALE = 'sale';
    const TRANS_TYPE_TOKEN = 'token';
    const PAY_TYPE_CREDITCARD = 'creditcard';
    const SPARROW_PAY_SUCCESS_CODE = '200';

    private $client;
    private $private_key;
    private $public_key;
    private $request_param;
    private $request_uri;

    public function __construct(Client $client)
    {
        $this->public_key = Config::get('services.sparrow.key.public');
        $this->private_key = Config::get('services.sparrow.key.private');
        $this->client = $client;
        $this->client->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->client->setBaseUrl('https://secure.5thdl.com');
        $this->request_uri = '/payments/services_api.aspx';
    }

    /**
     * set up the request data
     * @param array
     * @return Transaction
     */
    private function initRequestParam($request_param = [])
    {
        $this->request_param = $request_param;
    }

    /**
     * get auth token for transaction
     *
     * @return string|false
     */
    public function requestAccessToken()
    {
        $post_data = array_merge(
            $this->request_param,
            [
                'transtype' => self::TRANS_TYPE_TOKEN,
                'pkey' 		=> $this->public_key,
                'paytype'   => self::PAY_TYPE_CREDITCARD
            ]
        );
        $response = $this->client->sendRequest($this->request_uri, $post_data, 'post')->getPayload();
        if($this->client->getStatus() == 200) {
            $response_decoded = $this->decodeResponse($response);
            if($response_decoded['response'] == 1) {
                return $response_decoded['tt'];
            } else {
                throw new Exception($response_decoded['textresponse'], 500);
            }
        }else{
            throw new Exception($this->client->getMessage(), $this->client->getStatus());
        }
        return false;
    }

    /**
     * send a payment request to sparrow
     * @return object
     */
    public function postSaleTransaction($request_data = [])
    {
        $this->initRequestParam($request_data);

        $token = $this->requestAccessToken();

        if($token) {
            $post_data = array_merge(
                $this->request_param,
                [
                    'transtype' => self::TRANS_TYPE_SALE,
                    'mkey' 		=> $this->private_key,
                    'paytype'   => self::PAY_TYPE_CREDITCARD,
                    'tt'        => $token
                ]
            );
            $response = $this->client->sendRequest($this->request_uri, $post_data, 'post')->getPayload();

            $transType = isset($response_decoded['type']) ? $response_decoded['type'] : null;

            $response_decoded = $this->decodeResponse($response);
            if($this->client->getStatus() == 200) {
                $transId = isset($response_decoded['transid']) ? $response_decoded['transid'] : null;
                $status = isset($response_decoded['status']) ? $response_decoded['status'] : 400;
                $codeDescription = isset($response_decoded['codedescription']) ? $response_decoded['codedescription'] : null;

                if($status != self::SPARROW_PAY_SUCCESS_CODE) {
                    # change response status code to '400' if sparrow get '401', because '401' means parameter error
                    $status = $status == '401' ? '400' : $status;
                    throw new Exception($codeDescription, $status);
                }
            } else {
                throw new Exception($this->client->getMessage(), $this->client->getStatus());
            }
        }
        return true;
    }

    /**
     * decode the sparrow response
     * @param $response string
     * @return array
     */
    private function decodeResponse($response = '')
    {
        $result = [];
        $_tmp = explode('&', $response);
        foreach ($_tmp as $string) {
            $result[ explode('=', $string)[0] ] = str_replace('+', ' ', explode('=', $string)[1]);
        }
        return $result;
    }

}
