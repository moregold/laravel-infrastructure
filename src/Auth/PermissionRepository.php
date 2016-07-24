<?php namespace Moregold\Infrastructure\Auth;

use Illuminate\Support\Facades\Cache;
use Stevenmaguire\Laravel\Contracts\Cacheable;
use Stevenmaguire\Laravel\Services\EloquentCache;

class PermissionRepository extends EloquentCache implements Cacheable
{
    protected $enableLogging = false;

    protected $permission, $rolePermission;

    public function __construct(Permission $permission, RolePermission $rolePermission)
    {
        $this->permission = $permission;
        $this->rolePermission = $rolePermission;
    }

    public function checkRoute($route_name = null, $role_id = null)
    {
        if (! $route_name) return true;

        $query = $this->permission->where('route_name', $route_name);

        $permission = $this->cache('route_name('.$route_name.')', $query, 'first');

        if (! $permission) return true;

        return Cache::rememberForever(
            $this->getCacheKey().'.check('.$route_name.','.$role_id.')',
            function() use($permission, $role_id){
                return $this->rolePermission->check($permission->id, $role_id);
            }
        );
    }

    /**
     * Get the cache key grouping
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return 'permission';
    }

    /**
     * Get the model associated with the repository
     *
     * @return Permission
     * @codeCoverageIgnore
     */
    protected function getModel()
    {
        return $this->permission;
    }

}
