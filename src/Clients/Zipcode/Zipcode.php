<?php namespace Moregold\Infrastructure\Clients\Zipcode;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class Zipcode
{
	
	public $client;
	public $distance;
	public $api_key;
	public $request_url;

	public function __construct(Client $client)
	{
		$this->client = $client;
		$this->api_key = Config::get('services.zipcode.key');
		$this->distance = Config::get('services.zipcode.distance');
		$this->request_url = 'https://www.zipcodeapi.com';
	}

	/**
	 *
	 * Search data near zipcode and distance 
	 * @param string $zipcode
	 */
	public function searchNearBy($zipcode = '', $distance = false)
	{
		$result = [];
		try {
			if( $distance )
				$this->distance = $distance;
			if( !$this->validateParam( $zipcode ) )
				throw new Exception("Invalid request param.", 400);
			$this->generateRquestData( $zipcode );
			$result = $this->sendRequest();
		} catch (Exception $e) {
			throw new Exception($e->getMessage(), $e->getCode());
		}
		return $result;
	}

	public function validateParam( $zipcode )
	{
		return strlen($zipcode) != 5 ? false : true;
	}

	public function generateRquestData( $zipcode)
	{
		$this->request_url .= '/rest/'.$this->api_key.'/radius.json/'.sprintf('%05d', $zipcode).'/'.$this->distance.'/km';
	}

	public function sendRequest()
	{
		$response = $this->client->request('get', $this->request_url, [
			'verify' 	  => false,
		]);
		$http_content = $response->getBody()->getContents();
		if($response->getStatusCode() == 200) {
			return $this->parseResponse($http_content);
		}
		throw new Exception($response->getReasonPhrase(), $response->getStatusCode());
	}

	public function parseResponse($data = '')
	{
		$result = [];
		$data = json_decode($data);
		foreach ($data->zip_codes as $_item) {
			$result[] = [
				'zip_code' => $_item->zip_code,
				'city' => $_item->city,
				'state' => $_item->state
			];
		}
		return $result;
	}

}