<?php namespace Moregold\Infrastructure\Pagination;

use Illuminate\Support\Facades\Log;

trait FiltersTrait
{
    protected static $defaults = [
        'skip' => 0,
        'take' => 200,
        'order_by' => [
            'field' => 'created_at',
            'order' => 'desc'
        ],
        'max_records' => 1000,
        'include' => null,
        'filter' => []
    ];

    protected static function getFilterDefaults()
    {
        return self::$defaults;
    }

    protected static function paginationFilters($filters = [])
    {
        $defaults = self::getFilterDefaults();

        if (!isset($filters['skip']) || intval($filters['skip'])==0) {
            $filters['skip'] = $defaults['skip'];
        }
        if (!isset($filters['take']) || intval($filters['take'])==0) {
            $filters['take'] = $defaults['take'];
        } elseif ($filters['take'] > $defaults['max_records']) {
            $filters['take'] = $defaults['max_records'];
        }
        if (!isset($filters['order_by']['field']) || strlen($filters['order_by']['field'])==0) {
            $filters['order_by']['field'] = $defaults['order_by']['field'];
        }
        if (!isset($filters['order_by']['order']) || strlen($filters['order_by']['order'])==0) {
            $filters['order_by']['order'] = $defaults['order_by']['order'];
        }
        if (!isset($filters['filter']) || gettype($filters['filter'])=='string') {
            $filters['filter'] = $defaults['filter'];
        }
        return $filters;
    }

    protected static function filterQuery($query = null, $filters = [])
    {
        if (count($filters) < 1) {
            return $query;
        }

        $value = reset($filters);
        $filter = key($filters);
        unset($filters[$filter]);

        return self::filterQuery($query->where($filter, $value), $filters);
    }

    protected static function includeQuery($query = null, $includes = [])
    {
        if (count($includes) < 1) {
            return $query;
        }

        $include = array_pop($includes);

        // if it's not a model, get the model from the query
        if (get_class($query) == 'Illuminate\Database\Eloquent\Builder') {
            $model = $query->getModel();
        } else {
            $model = $query;
        }

        // check if the relation exists, and if so, include it
        if (strlen($include) && !preg_match('/get/', $include)) {
            if( preg_match('/^(.+?)Count$/iu', $include, $m) ) {
                if (method_exists($model, $m[1])) {
                    $query = $query->withCount($m[1]);
                }
            } else {
                $query = $query->with($include);    
            }
        }

        return self::includeQuery($query, $includes);
    }
}
