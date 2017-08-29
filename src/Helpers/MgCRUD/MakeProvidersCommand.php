<?php namespace Moregold\Infrastructure\Helpers\MgCRUD;

use Moregold\Infrastructure\Helpers\MgCRUD\GenerationRepository;

class MakeProvidersCommand extends GenerationRepository
{
	public $replace_array, $text_array;

	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'INSERTTO';
		$this->url= 'app/Providers/AppServiceProvider.php';
		$this->text_array = [
			"\$this->register__CAPITALNAME__();
			# add new register
			",
		"public function register__CAPITALNAME__()
	    {
	        \$this->app->bind('App\Services\__CAPITALNAME__s\Contracts\RegisterInterface', 'App\Services\__CAPITALNAME__s\Registrar');
	        \$this->app->bind('App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface', 'App\Repositories\__CAPITALNAME__Repository');
	    }

	    # add new function
			",
		];
		$this->replace_array = [
			'/\#\sadd\snew\sregister/',
			'/\#\sadd\snew\sfunction/'
		];
	}

	public function handle(GenerationRepository $generationRepository)
	{
		foreach ($this->text_array as $key => $text) {
			$this->text = $text;
			$this->replace_str = $this->replace_array[$key];
			$this->generate();
		}
	}

}