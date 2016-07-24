<?php namespace Moregold\Infrastructure\Pagination;

interface FilterInterface
{
    public function scopeWithFilters($query, $filters = []);
}
