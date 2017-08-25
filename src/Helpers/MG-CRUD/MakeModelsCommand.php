<?php namespace Moregold\Infrastructure\Helpers\Generation;

use Moregold\Infrastructure\Helpers\Generation\GenerationRepository;

class MakeModelsCommand extends GenerationRepository
{
	public $class_name;

	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'BUILD';
		$this->text = "<?php

namespace App\Models;

use Moregold\Infrastructure\Model;

class __CAPITALNAME__s extends Model
{
    protected \$table = '__ORIGINALNAME__s';

    public \$incrementing = false;

    protected \$fillable = [
    	
    ];

}
		";
		$this->url = 'app/Models/'.$this->generateName().'s.php';
	}

	public function handle(GenerationRepository $generationRepository)
	{
		$this->generate();
	}

}