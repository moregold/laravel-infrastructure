<?php namespace Moregold\Infrastructure\Clients\Zoho;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Moregold\Infrastructure\Clients\HttpClientInterface as Client;
use Moregold\Infrastructure\Clients\Zoho\Contracts\ZohoClientInterface;
use Exception;

class ZohoClient implements ZohoClientInterface
{
	private $client;
	private $access_token;
	private $zoho_account;
	private $zoho_password;
	private $zoho_display_name;
	public  $post_data;

	const REQUEST_RECORD_URL = 'https://crm.zoho.com/crm/private/xml/';
	const GET_TOKEN_URL 	 = 'https://accounts.zoho.com/apiauthtoken/nb/create';
	const ZOHO_SCOPE		 = 'crmapi';
	const ZOHO_TOKEN_SCOPE   = 'ZohoCRM/crmapi';

	public function __construct(Client $client)
	{
		$this->client 			 = $client;
		$this->zoho_account 	 = Config::get('services.zoho.account');
		$this->zoho_password 	 = Config::get('services.zoho.password');
		$this->zoho_display_name = Config::get('services.zoho.display_name');
		$this->access_token		 = Config::get('services.zoho.access_token');
		$this->post_data 		 = [
			'authtoken' => $this->access_token,
			'scope'		=> self::ZOHO_SCOPE,
		];
	}

	/**
	 *
	 * send request to zoho
	 * @param Array $data
	 * @param string $method
	 * @return Moregold\Domains\Appointments\Appointment | Array
	 */
	public function postRequest($data = [], $method)
	{
		$response = $this->client->sendRequest(self::REQUEST_RECORD_URL.$method, $data, 'post')->getPayload();
		if($this->client->getStatus() == 200) {
			return $this->parseResponse($response);
			
		} else {
			throw new Exception($this->client->getMessage(), $this->client->getStatus());
		}
	}

	public function requestAccessToken()
	{
		$post_data = [
			'SCOPE'		   => self::ZOHO_TOKEN_SCOPE,
			'EMAIL_ID'	   => $this->zoho_account,
			'PASSWORD'	   => $this->zoho_password,
			'DISPLAY_NAME' => $this->zoho_display_name
		];
		$response = $this->client->sendRequest(self::GET_TOKEN_URL, $post_data, 'post')->getPayload();
		if($this->client->getStatus() == 200) {
			if(preg_match('/AUTHTOKEN=(.{32})/ius', $response, $match)) {
				return $this->access_token = $match[1];
			} else {
				preg_match('/CAUSE=([a-zA-Z_]+)/ius', $response, $match);
				Log::error('Zoho get access token has error:'.$match[1]);
			}
		} else {
			Log::error('HTTP error code '.$this->client->getStatus().' on Zoho get access token');
		}
		return false;
	}

	public function formatXmlData($data = [])
	{
		$xml_str = '<Leads><row no="1">';
		foreach ($data as $key => $value) {
			$xml_str .= '<FL val="'.$key.'">'.$value.'</FL>';
		}
		$xml_str .= '</row></Leads>';
		return $xml_str;
	}

	public function parseResponse($response = '')
	{
		return json_decode(json_encode((array)simplexml_load_string($response)), true);
	}

}
