<?php namespace Moregold\Infrastructure\Helpers\Generation;

use Illuminate\Support\Facades\Storage;


class GenerationRepository
{
	public $capital_name, $file;
	public $route_name;
	public $class_name;
	public $method;
	public $text;
	public $url;
	public $replace_str;

	// public function generateCapitalName()
	// {
	// 	$this->capital_name = ucfirst($this->class_name);
	// }

	public function readFile()
	{
		if(Storage::disk('template')->exists($this->url)){
			$this->file = Storage::disk('template')->get($this->url);
		}else{
			Storage::disk('template')->put($this->url, '');
		}
		
	}

	public function createContent()
	{
		$this->text = preg_replace('/\_\_CAPITALNAME\_\_/', $this->capital_name, $this->text);
		$this->text = preg_replace('/\_\_ORIGINALNAME\_\_/', $this->class_name, $this->text);
		$this->text = preg_replace('/\_\_ROUTENAME\_\_/', $this->route_name, $this->text);
	}

	public function replaceFileContent()
	{
		if($this->method == 'INSERTTO'){
			$this->text = preg_replace($this->replace_str, $this->text, $this->file);
		}
	}

	public function saveContentToFile()
	{
		if($this->method != 'INSERTTO') {
			Storage::disk('template')->append($this->url, $this->text);
		} else {
			Storage::disk('template')->put($this->url, $this->text);
		}
	}




	public function generate()
	{
		$this->generateName();
		$this->generateRouteName();
		$this->readFile();
		$this->createContent();
		$this->replaceFileContent();
		$this->saveContentToFile();
	}


	public function generateName()
	{
		$this->capital_name = '';
		$arr = explode('_', $this->class_name);
		foreach ($arr as $value) {
			$this->capital_name .= ucfirst($value);
		}
		return $this->capital_name;
	}

	public function generateRouteName()
	{
		$arr = explode('_', $this->class_name);
		$this->route_name = implode('-', $arr);
		return $this->route_name;
	}

}

