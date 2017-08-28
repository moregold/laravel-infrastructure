<?php namespace Moregold\Infrastructure\Helpers\MG-CRUD;

use Moregold\Infrastructure\Helpers\MG-CRUD\GenerationRepository;

class MakeValidatorsCommand extends GenerationRepository
{
	public $class_name;

	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->url = 'app/Services/Validation/'.$this->generateName().'Validator.php';
		$this->text = "<?php namespace App\Services\Validation;

class __CAPITALNAME__Validator extends Validator {
	protected \$create_rules = [

	];
	protected \$update_rules = [

	];

	

	public function validateCreate(\$input)
	{
		return parent::validate(\$input, \$this->create_rules);
	}

	public function validateUpdate(\$input)
	{
		return parent::validate(\$input, \$this->update_rules);
	}

}
		";
	}

	public function handle(GenerationRepository $generationRepository)
	{
		$this->generate();
	}

}