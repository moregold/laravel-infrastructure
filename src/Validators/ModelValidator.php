<?php namespace Moregold\Infrastructure\Validators;

use Moregold\Infrastructure\Model;

class ModelValidator implements ValidatorInterface
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getInput()
    {
        return $this->model->getAttributes();
    }

    public function getRules()
    {
        return $this->model->getValidationRules();
    }
}
