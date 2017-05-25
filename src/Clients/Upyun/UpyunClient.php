<?php namespace Moregold\Infrastructure\Clients\Upyun;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Moregold\Infrastructure\Clients\HttpClientInterface as Client;
use Moregold\Infrastructure\Clients\Upyun\Contracts\UpyunClientInterface;
use Exception;

class UpyunClient implements UpyunClientInterface
{
	private $client;
	private $file;
	private $upyun_account;
	private $upyun_password;
	private $upyun_space;
	private $local_storage;

	public function __construct(Client $client)
	{
		$this->client 		 	= $client;
		$this->client->setBaseUrl(Config::get('services.upyun.upload_url'));
		$this->upyun_account 	= Config::get('services.upyun.account');
		$this->upyun_password 	= Config::get('services.upyun.password');
		$this->upyun_space 		= Config::get('services.upyun.space');
		$this->local_storage    = Config::get('services.upyun.local_storage');
	}

	/**
	 *
	 * upload image to upyun
	 * @param string $path 
	 * @param File $file
	 * @return Doctor|Facility
	 */
	public function uploadImage($path = '', $file = '')
	{
		$result = false;
		#save image to local
		$location_file = rand(100000, 999999).'.'.$file->getClientOriginalExtension();
		$target_folder = '../';
		$file->move($target_folder, $location_file);

		$this->file = fopen($target_folder.$location_file, 'r');
		$result = $this->request('PUT', $path) ? true : false;
		@fclose($this->file);
		if(!$this->local_storage) {
			unlink($target_folder.$location_file); 	
		}
		return $result;
	}

	/**
	 *
	 * Delete image from upyun
	 * @param string $path (e.g 'facility/abcdefg/1234567.jpg')
	 * @return Doctor|Facility
	 */
	public function deleteImage($path = '')
	{
		return $this->request('DELETE', $path);
	}

	public function request($request_method = '', $path = '')
	{
		$post_data = [];
		# get GMT time
		$date = gmdate('D, d M Y H:i:s \G\M\T');
		# get content length
		$content_length = 0;
		if(is_resource($this->file)) {
			fseek($this->file, 0, SEEK_END);
			$content_length = ftell($this->file);
			fseek($this->file, 0);
			$post_data = array_merge($post_data, ['body' => $this->file]);
		}
		# generate signature
		$signature = $request_method.'&/'.$this->upyun_space.'/'.$path.'&'.$date.'&'.$content_length.'&'.md5($this->upyun_password);
		# set header info
		$this->client->setHeader('Authorization', 'Upyun '.$this->upyun_account.':'.md5($signature));
		$this->client->setHeader('Date', $date);
		$this->client->setHeader('Content-Length', $content_length);
		$this->client->setHeader('Mkdir', 'true');

		$this->client->sendRequest('/'.$this->upyun_space.'/'.$path, $post_data, $request_method)->getPayload();
		if($this->client->getStatus() == 200) {
			return true;
		} else {
			throw new Exception($this->client->getMessage(), $this->client->getStatus());
		}
	}

}