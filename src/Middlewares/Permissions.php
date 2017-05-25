<?php namespace Moregold\Infrastructure\Middlewares;

use Moregold\Infrastructure\Model;

class Permissions extends Model
{
    protected $table = 'permissions';

    protected $fillable = ['permission_name', 'permission'];

}