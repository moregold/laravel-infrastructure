<?php namespace Moregold\Infrastructure\Helpers\MgCRUD;

use Moregold\Infrastructure\Helpers\MgCRUD\GenerationRepository;

class MakeCommandsCommand extends GenerationRepository
{
	public $text_array, $url_array;

	public function __construct($class_name)
	{
		$this->method = 'BUILD';
		$this->class_name = $class_name;
		$this->url_array = [
			'app/Commands/'.$this->generateName().'s/Create'.$this->generateName().'Command.php',
			'app/Commands/'.$this->generateName().'s/Update'.$this->generateName().'ByIdCommand.php',
			'app/Commands/'.$this->generateName().'s/Fetch'.$this->generateName().'Command.php',
			'app/Commands/'.$this->generateName().'s/Fetch'.$this->generateName().'sCommand.php',
			'app/Commands/'.$this->generateName().'s/Delete'.$this->generateName().'Command.php'
		];
		$this->text_array = [
			"<?php namespace App\Commands\__CAPITALNAME__s;

use App\Commands\Command;
use App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface;

class Create__CAPITALNAME__Command extends Command
{
	public \$attributes;

	public function __construct(\$attributes)
	{
		\$this->attributes = \$attributes;
	}


	public function handle(__CAPITALNAME__RepositoryInterface \$__ORIGINALNAME__Repository)
	{   
		return \$__ORIGINALNAME__Repository->create(\$this->attributes);
	}
}
		",

		"<?php namespace App\Commands\__CAPITALNAME__s;
use App\Commands\Command;
use App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface;

class Update__CAPITALNAME__ByIdCommand extends Command
{
	public \$id, \$attributes;

	public function __construct(\$id, \$attributes)
	{	
		\$this->id = \$id;
		\$this->attributes = \$attributes;
	}


	public function handle(__CAPITALNAME__RepositoryInterface \$__ORIGINALNAME__Repository)
	{
		return \$__ORIGINALNAME__Repository->update(\$this->id, \$this->attributes);
	}
}
		",

		"<?php namespace App\Commands\__CAPITALNAME__s;

use App\Commands\Command;
use App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface;

class Fetch__CAPITALNAME__Command extends Command
{
	public \$id;

	public function __construct(\$id)
	{
		\$this->id = \$id;
	}


	public function handle(__CAPITALNAME__RepositoryInterface \$__ORIGINALNAME__repository)
	{
		return \$__ORIGINALNAME__repository->get__CAPITALNAME__ById( \$this->id );
	}
}
		",

		"<?php namespace App\Commands\__CAPITALNAME__s;
use App\Commands\Command;
use App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface;
use Illuminate\Http\Request;
use App\Models\UserRoles;

class Fetch__CAPITALNAME__sCommand extends Command
{
	public \$filters, \$load;

	public function __construct(\$filters, \$load)
	{
		\$this->filters = \$filters;
		\$this->load = \$load;
	}


	public function handle(__CAPITALNAME__RepositoryInterface \$__ORIGINALNAME__Repository, Request \$request)
	{
		return \$__ORIGINALNAME__Repository->get__CAPITALNAME__s(\$this->filters, \$this->load);
	}
}
		",
		"<?php namespace App\Commands\__CAPITALNAME__s;
use App\Commands\Command;
use App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface;

class Delete__CAPITALNAME__Command extends Command
{
	private \$id;

	public function __construct(\$id)
	{
		\$this->id = \$id;
	}


	public function handle(__CAPITALNAME__RepositoryInterface \$__ORIGINALNAME__Repository)
	{
		return \$__ORIGINALNAME__Repository->delete__CAPITALNAME__ById(\$this->id);
	}
}
		",
	];
	}

	public function handle()
	{
		foreach ($this->text_array as $key => $text) {
			$this->text = $text;
			$this->url = $this->url_array[$key];
			$this->generate();
		}

	}


}