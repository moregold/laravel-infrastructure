<?php namespace Moregold\Infrastructure\Helpers\MG-CRUD;

use Moregold\Infrastructure\Helpers\MG-CRUD\GenerationRepository;

class MakeRoutesCommand extends GenerationRepository
{
	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'BUILD';
		$this->text = "
			Route::post('__ROUTENAME__s', [
			'uses' => '__CAPITALNAME__Controller@postCreate__CAPITALNAME__',
			'as'   => 'create-__ROUTENAME__',
			]);
			Route::put('__ROUTENAME__s/{id}', [
				'uses' => '__CAPITALNAME__Controller@postUpdate__CAPITALNAME__',
				'as'   => 'update-__ROUTENAME__',
			]);
			Route::get('__ROUTENAME__s/{id}', [
				'uses' => '__CAPITALNAME__Controller@get__CAPITALNAME__Info',
				'as'   => 'show-__ROUTENAME__',
			]);
			Route::delete('__ROUTENAME__s/{id}', [
				'uses' => '__CAPITALNAME__Controller@delete__CAPITALNAME__',
				'as'   => 'delete-__ROUTENAME__',
			]);
			Route::get('__ROUTENAME__s', [
				'uses' => '__CAPITALNAME__Controller@get__CAPITALNAME__List',
				'as'   => 'show-__ROUTENAME__s',
			]);
		";
		$this->url = 'routes/web.php';

	}

	public function handle()
	{
		$this->generate();
	}

}