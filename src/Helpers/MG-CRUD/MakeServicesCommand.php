<?php namespace Moregold\Infrastructure\Helpers\Generation;

use Moregold\Infrastructure\Helpers\Generation\GenerationRepository;

class MakeServicesCommand extends GenerationRepository
{
	public $text_array, $url_array;

	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'BUILD';
		$this->url_array = [
			'app/Services/'.$this->generateName().'s/Contracts/RegisterInterface.php',
			'app/Services/'.$this->generateName().'s/Registrar.php',
		];
		$this->text_array =[
			"<?php namespace App\Services\__CAPITALNAME__s\Contracts;

interface RegisterInterface
{
	public function create__CAPITALNAME__(\$attributes = []);
	public function update__CAPITALNAME__ById(\$id = null, \$attributes = []);
	public function fetch__CAPITALNAME__(\$id = null);
	public function delete__CAPITALNAME__(\$id = null);
	public function fetch__CAPITALNAME__s(\$filters = [], \$load = []);
}
		",

		"<?php namespace App\Services\__CAPITALNAME__s;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Commands\__CAPITALNAME__s\Create__CAPITALNAME__Command;
use App\Commands\__CAPITALNAME__s\Update__CAPITALNAME__ByIdCommand;
use App\Commands\__CAPITALNAME__s\Fetch__CAPITALNAME__Command;
use App\Commands\__CAPITALNAME__s\Fetch__CAPITALNAME__sCommand;
use App\Commands\__CAPITALNAME__s\Delete__CAPITALNAME__Command;
use App\Commands\__CAPITALNAME__s\SetPreferred__CAPITALNAME__Command;
use App\Services\__CAPITALNAME__s\Contracts\RegisterInterface;

class Registrar implements RegisterInterface
{
	use DispatchesJobs;

	public function create__CAPITALNAME__(\$attributes = [])
	{
		return \$this->dispatch(new Create__CAPITALNAME__Command(\$attributes));
	}

	public function update__CAPITALNAME__ById(\$id = null, \$attributes = [])
	{
		return \$this->dispatch(new Update__CAPITALNAME__ByIdCommand(\$id, \$attributes));
	}

	public function fetch__CAPITALNAME__(\$id = null)
	{
		return \$this->dispatch(new Fetch__CAPITALNAME__Command(\$id));
	}

	public function fetch__CAPITALNAME__s(\$filters = [], \$load = [])
	{
		return \$this->dispatch(new Fetch__CAPITALNAME__sCommand(\$filters, \$load));
	}

	public function delete__CAPITALNAME__(\$id = null)
	{
		return \$this->dispatch(new Delete__CAPITALNAME__Command(\$id));
	}

}
		",

		];
	}

	public function handle(GenerationRepository $generationRepository)
	{
		
		foreach ($this->text_array as $key => $text) {
			$this->text = $text;
			$this->url = $this->url_array[$key];
			$this->generate();
		}

	}

}