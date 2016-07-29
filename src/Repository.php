<?php namespace Packback\Infrastructure;

use Illuminate\Support\Facades\Config;
use Stevenmaguire\Laravel\Contracts\Cacheable;
use Stevenmaguire\Laravel\Services\EloquentCache;
use Packback\Infrastructure\Pagination\FiltersTrait;

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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function cacheCount($query, $key = null)
    {
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function paginatedCache($query, $pagination_filters, $key = null)
    {
        $pagination_filters = $pagination_filters['skip'].','
            .$pagination_filters['take'].','
            .$pagination_filters['order_by']['field'].','
            .$pagination_filters['order_by']['order'];

        if ($key) {
            $key = 'paginated('.$key.$pagination_filters.')';
        } else {
            $key = 'paginated('.$pagination_filters.')';
        }

        return $this->cache($key, $query);
    }
}
