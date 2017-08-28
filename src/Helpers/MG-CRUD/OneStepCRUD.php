<?php namespace Moregold\Infrastructure\Helpers\MG-CRUD;

use Illuminate\Foundation\Bus\DispatchesJobs;

class OneStepCRUD
{
	use DispatchesJobs;

	public $class_name;

	public function __construct($class_name)
	{
		$this->class_name = $class_name;
	}

	public function handle()
	{
		$this->dispatch(new MakeRoutesCommand( $this->class_name));
		$this->dispatch(new MakeControllersCommand( $this->class_name));
		$this->dispatch(new MakeServicesCommand( $this->class_name));
		$this->dispatch(new MakeCommandsCommand( $this->class_name));
		$this->dispatch(new MakeRepositoriesCommand( $this->class_name));
		$this->dispatch(new MakeValidatorsCommand( $this->class_name));
		$this->dispatch(new MakeTransformersCommand( $this->class_name));
		$this->dispatch(new MakeModelsCommand( $this->class_name));
		$this->dispatch(new MakeProvidersCommand( $this->class_name));
	}

}
