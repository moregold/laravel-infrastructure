<?php namespace Moregold\Infrastructure\Helpers\Generation;

use Moregold\Infrastructure\Helpers\Generation\GenerationRepository;

class MakeTransformersCommand extends GenerationRepository
{
	public function __construct($class_name)
	{
		$this->class_name = $class_name;
		$this->method = 'BUILD';
		$this->text = "<?php namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\__CAPITALNAME__s;
/**
* 
*/
class __CAPITALNAME__Transformer extends TransformerAbstract
{
	public function transform(__CAPITALNAME__s \$__ORIGINALNAME__)
	{
		return [

		];
	}
}
		";
		$this->url = 'app/Transformers/'.$this->generateName().'Transformer.php';

	}

	public function handle(GenerationRepository $generationRepository)
	{
		$this->generate();
	}

}