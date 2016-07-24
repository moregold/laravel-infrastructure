<?php namespace Moregold\Infrastructure\Validators;

use Illuminate\Support\Facades\Facade;

class KeysFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @codeCoverageIgnoreStart
     */
    protected static function getFacadeAccessor()
    {
        return 'ValidatorsKeys';
    }
    // @codeCoverageIgnoreEnd
}
