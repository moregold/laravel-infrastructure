<?php namespace Moregold\Infrastructure\Messages;

use Illuminate\Support\Facades\Facade;

class TypesFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @codeCoverageIgnoreStart
     */
    protected static function getFacadeAccessor()
    {
        return 'MessagesTypes';
    }
    // @codeCoverageIgnoreEnd
}
