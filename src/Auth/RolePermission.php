<?php namespace Moregold\Infrastructure\Auth;


class RolePermission extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'roles_permissions';

    public function check($permission_id, $role_id)
    {
        return $this->where('permission_id', $permission_id)
            ->where('role_id', $role_id)
            ->count() > 0;
    }
}