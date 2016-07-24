<?php namespace Moregold\Infrastructure;

use Illuminate\Support\Facades\Config;
use Stevenmaguire\Laravel\Contracts\Cacheable;
use Stevenmaguire\Laravel\Services\EloquentCache;
use Moregold\Infrastructure\Pagination\FiltersTrait;

abstract class Repository extends EloquentCache implements Cacheable
{
    use FiltersTrait;

    protected $enableLogging = false;
    protected $cacheForMinutes;

    public function __construct()
    {
        $this->cacheForMinutes = Config::get('cache.cache_time.default');
    }

    /**
     * Cache (or fetch) the count of a collection
     *
     * @param $query
     * @return \Illuminate\Support\Collection
     */
    protected function cacheCount($query, $key = null, $pagination_filters = [])
    {
        if (isset($pagination_filters['filter'])) {
            $key = $key ? $key.'_' : '';
            foreach ($pagination_filters['filter'] as $filter => $val) {
                $key .= $filter . ':' . $val . ',';
            }
        }

        $key ? $key = 'count('.$key.')' : $key = 'count';

        return $this->cache($key, $query, 'count');
    }

    /**
     * Cache (or fetch) a single record by ID
     *
     * @param $query
     * @param $id
     */
    protected function cacheById($id, $query, $key = null)
    {
        return $this->cache('id('.$id.($key ? '_'.$key : '').')', $query, 'first');
    }

    /**
     * Cache (or fetch) a paginated dataset with skip, take, and order_by filters
     *
     * @param $query
     * @param $pagination_filters
     * @return \Illuminate\Support\Collection
     */
    protected function paginatedCache($query, $pagination_filters, $key = null)
    {
        $serialized_filters = $pagination_filters['skip'].','
            .$pagination_filters['take'].','
            .$pagination_filters['order_by']['field'].','
            .$pagination_filters['order_by']['order'];

        foreach ($pagination_filters['filter'] as $filter => $val) {
            $serialized_filters .= $filter.':'.$val.',';
        }

        if ($key) {
            $key = 'paginated('.$key.$serialized_filters.')';
        } else {
            $key = 'paginated('.$serialized_filters.')';
        }

        return $this->cache($key, $query);
    }
}
