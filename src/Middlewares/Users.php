<?php namespace Moregold\Infrastructure\Middlewares;

use Moregold\Infrastructure\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Models\Permissions;

class Users extends Model implements AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable, SoftDeletes;

    protected $table = 'users';

    public $incrementing = false;

    protected $dates = ['deleted_at'];

    protected $fillable = [
    	'user_role', 'username', 'password'
    ];

    public function rolePermission()
    {
        return $this->belongsTo('App\Models\RolePermissions', 'user_type');
    }

}
