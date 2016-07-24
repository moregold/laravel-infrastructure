<?php namespace Moregold\Infrastructure\Validators;

use Illuminate\Support\Facades\Validator;
use Moregold\Infrastructure\Model as Base;

class Factory
{
    /**
     * @param array $input
     * @param array $rules
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected static function validate($input = [], $rules = [])
    {
        $v = Validator::make($input, $rules);
        return $v;
    }

    /**
     * @param array $input
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function view($input = [])
    {
        if (isset($input[KeysFacade::htmlName()])) {
            $view = $input[KeysFacade::htmlName()];
            unset($input[KeysFacade::htmlName()]);
        } else {
            $view = null;
        }
        $validator = new ViewValidator($view, $input);
        return self::validate($validator->getInput(), $validator->getRules());
    }

    /**
     * @param \Moregold\Infrastructure\Model $model
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function model(Base $model)
    {
        $validator = new ModelValidator($model);
        return self::validate($validator->getInput(), $validator->getRules());
    }
}
