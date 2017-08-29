<?php namespace Moregold\Infrastructure\Helpers\MgCRUD;

use Moregold\Infrastructure\Helpers\MgCRUD\GenerationRepository;

class MakeRepositoriesCommand extends GenerationRepository
{
	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'BUILD';
		$this->url_array = [
			'app/Repositories/Contracts/'.$this->generateName().'RepositoryInterface.php',
			'app/Repositories/'.$this->generateName().'Repository.php'
		];
		$this->text_array = [
			"<?php namespace App\Repositories\Contracts;

interface __CAPITALNAME__RepositoryInterface
{
	public function create(\$attributes = []);
	public function update(\$id = null, \$attributes = []);
	public function get__CAPITALNAME__ById(\$id = null);
	public function get__CAPITALNAME__s(\$filters = [], \$load = []);
	public function delete__CAPITALNAME__ById(\$id = null);
}
		",
			"<?php namespace App\Repositories;

use App\Models\__CAPITALNAME__s;
use App\Repositories\Contracts\__CAPITALNAME__RepositoryInterface;
use App\Services\Validation\__CAPITALNAME__Validator;
use Moregold\Infrastructure\Pagination\FiltersTrait;
use Moregold\Infrastructure\Repository,
	Moregold\Infrastructure\Contract;

class __CAPITALNAME__Repository extends Repository implements __CAPITALNAME__RepositoryInterface
{
	private \$__ORIGINALNAME__;
	private \$validator;
	private \$contract;

	public function __construct(__CAPITALNAME__s \$__ORIGINALNAME__, __CAPITALNAME__Validator \$validator, Contract \$contract)
	{
		\$this->__ORIGINALNAME__ = \$__ORIGINALNAME__;
		\$this->validator = \$validator;
		\$this->contract = \$contract;
	}


	public function create(\$attributes = [])
	{
		if(\$this->validator->validateCreate(\$attributes)) {
			return \$this->__ORIGINALNAME__->create(\$attributes);
		}
		return \$this->__ORIGINALNAME__->addError(\$this->validator->getErrorMessages(), null, 400);
	}

	public function update(\$id = null, \$attributes = [])
	{
		\$__ORIGINALNAME__ = \$this->__ORIGINALNAME__->where('id', \$id)->first();
		if(\$this->validator->validateUpdate(\$attributes)){
			if(\$__ORIGINALNAME__ && \$__ORIGINALNAME__->update(\$attributes)) {
				return \$__ORIGINALNAME__;
			}
			return \$this->__ORIGINALNAME__->addError('Unable to find __ORIGINALNAME__ with this id', null, 404);
		}
		return \$this->__ORIGINALNAME__->addError(\$this->validator->getErrorMessages(), null, 400);
	}

	public function get__CAPITALNAME__ById(\$id = null)
	{
		\$__ORIGINALNAME__ = \$this->__ORIGINALNAME__->where('id', \$id)->first();
		if(\$__ORIGINALNAME__){
			return \$__ORIGINALNAME__;
		}
		return \$this->__ORIGINALNAME__->addError('Unable to find __ORIGINALNAME__ with this id', null, 404);
	}

	public function get__CAPITALNAME__s(\$filters = [], \$load = [])
	{
		\$pagination_filters = static::paginationFilters(\$filters);
        \$filters = \$pagination_filters['filter'];
        \$query = \$this->__ORIGINALNAME__->query();
        \$query = static::filterQuery(\$query, \$filters);

        \$__ORIGINALNAME__s_count = \$query->count();

        \$query = static::includeQuery(\$query, \$load);
        \$__ORIGINALNAME__s = \$query->withPaginationFilters(\$pagination_filters)->get();

        \$this->contract->total_records(\$__ORIGINALNAME__s_count)
            ->per_page(count(\$__ORIGINALNAME__s))
            ->skip(\$pagination_filters['skip'])
            ->take(\$pagination_filters['take'])
            ->records(\$__ORIGINALNAME__s);

        return \$this->contract;

	}

	public function delete__CAPITALNAME__ById(\$id = null)
	{
		\$query = \$this->__ORIGINALNAME__->where('id', \$id);
		\$origin___ORIGINALNAME__ = \$query->first();
		if(\$query->delete()){
			return \$origin___ORIGINALNAME__;
		}
		return \$this->__ORIGINALNAME__->addError('Unable to find __ORIGINALNAME__ with this id', null, 404);
	}


	protected function getCacheKey()
    {
        return '__ORIGINALNAME__';
    }

    /**
     * Get the model associated with the repository
     *
     * @return Appointment
     * @codeCoverageIgnore
     */
    protected function getModel()
    {
        return \$this->__ORIGINALNAME__;
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