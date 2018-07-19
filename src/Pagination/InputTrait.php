<?php namespace Moregold\Infrastructure\Pagination;

use Illuminate\Support\Facades\Request;

trait InputTrait
{
    public function inputPageFilters()
    {
        return Request::only(
            'filter',
            'filters',
            'last_received',
            'order_by',
            'skip',
            'take',
            'keyword'
        );
    }

    public function inputIncludes()
    {
        return explode(',', Request::input('include'));
    }
}
