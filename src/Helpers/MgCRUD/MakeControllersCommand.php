<?php namespace Moregold\Infrastructure\Helpers\MgCRUD;

use Moregold\Infrastructure\Helpers\MgCRUD\GenerationRepository;

class MakeControllersCommand extends GenerationRepository
{

	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'BUILD';
		$this->url = 'app/Http/Controllers/'.$this->generateName().'Controller.php';
		$this->text = "<?php

namespace App\Http\Controllers;

use App\Models\\__CAPITALNAME__s;
use App\Transformers\\__CAPITALNAME__Transformer;
use App\Services\\__CAPITALNAME__s\Contracts\RegisterInterface as __CAPITALNAME__Activities;
use Illuminate\Http\Request;
use Moregold\Infrastructure\Pagination\InputTrait as PaginationInputTrait;

class __CAPITALNAME__Controller extends ApiController
{
	use PaginationInputTrait;

	public function postCreate__CAPITALNAME__(__CAPITALNAME__Activities \$activity)
	{
		\$__ORIGINALNAME__ = \$activity->create__CAPITALNAME__(\$this->__ORIGINALNAME__Input());
		if(\$__ORIGINALNAME__->hasErrors()) {
			return \$this->buildErrorResponse(\$__ORIGINALNAME__->getErrorsAsString(), \$__ORIGINALNAME__->getStatusCode());
		}
		return \$this->respondWithItem(\$__ORIGINALNAME__, new __CAPITALNAME__Transformer(), '__ORIGINALNAME__');
	}

	public function postUpdate__CAPITALNAME__(__CAPITALNAME__Activities \$activity, \$id)
	{
		\$__ORIGINALNAME__ = \$activity->update__CAPITALNAME__ById(\$id, \$this->__ORIGINALNAME__Input());
		if(\$__ORIGINALNAME__->hasErrors()) {
			return \$this->buildErrorResponse(\$__ORIGINALNAME__->getErrorsAsString(), \$__ORIGINALNAME__->getStatusCode());
		}
		return \$this->respondWithItem(\$__ORIGINALNAME__, new __CAPITALNAME__Transformer(), '__ORIGINALNAME__');
	}

	public function get__CAPITALNAME__Info(__CAPITALNAME__Activities \$activity, \$id)
	{
		\$__ORIGINALNAME__ = \$activity->fetch__CAPITALNAME__(\$id);
		if(\$__ORIGINALNAME__->hasErrors()) {
			return \$this->buildErrorResponse(\$__ORIGINALNAME__->getErrorsAsString(), \$__ORIGINALNAME__->getStatusCode());
		}
		return \$this->respondWithItem(\$__ORIGINALNAME__, new __CAPITALNAME__Transformer(), '__ORIGINALNAME__');
	}

	public function get__CAPITALNAME__List(__CAPITALNAME__Activities \$activity)
	{
		\$load = \$this->parseFilters();
		\$filters = \$this->inputPageFilters();
		\$__ORIGINALNAME__s = \$activity->fetch__CAPITALNAME__s(\$filters, \$load);
		\$this->addMeta(\$filters, \$__ORIGINALNAME__s->per_page, \$__ORIGINALNAME__s->total_records);
		return \$this->respondWithCollection(\$__ORIGINALNAME__s->records, new __CAPITALNAME__Transformer(), '__ORIGINALNAME__s');
	}

	public function delete__CAPITALNAME__(__CAPITALNAME__Activities \$activity, \$id)
	{
		\$__ORIGINALNAME__ = \$activity->delete__CAPITALNAME__(\$id);
		if(\$__ORIGINALNAME__->hasErrors()) {
			return \$this->buildErrorResponse(\$__ORIGINALNAME__->getErrorsAsString(), \$__ORIGINALNAME__->getStatusCode());
		}
		return \$this->respondWithItem(\$__ORIGINALNAME__, new __CAPITALNAME__Transformer(), '__ORIGINALNAME__');
	}

}
		";
	}

	public function handle()
	{
		$this->generate();
	}

}