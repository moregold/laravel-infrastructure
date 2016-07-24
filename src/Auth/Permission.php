<?php namespace Moregold\Infrastructure\Auth;

use Moregold\Infrastructure\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = ['route_name','display_name'];

    /**
     * Use UUIDs instead of incrementic IDs
     *
     * @var bool
     */
    public $incrementing = false;

    public function roles()
    {
        return $this->hasMany('Moregold\Infrastructure\Auth\RolePermission');
    }
}