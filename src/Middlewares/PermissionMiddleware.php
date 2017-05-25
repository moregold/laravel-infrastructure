<?php namespace Moregold\Infrastructure\Middlewares;

use Closure;
use Moregold\Infrastructure\Middlewares\Permissions;
use Moregold\Infrastructure\Middlewares\Users;
use Moregold\Infrastructure\Middlewares\AuthorizationMiddleware;

class PermissionMiddleware {

    public $authorizer;
    public $user;

    public function __construct(AuthorizationMiddleware $authorizer)
    {
        $this->authorizer = $authorizer;
        $this->user = User::findOrFail( $this->authorizer->getCurrentUserId() );
    }



    public function hasPermission($permission_name)
    {
        if(!$permission_name)
            return false;
        $permission = Permissions::where('permission_name', $permission_name)->first();
        if(!$permission)
            return false;
        // Can optimize speed by cache
        return $this->user->rolePermission->role_permission & $permission->permission;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeAs = $request->route()->getAction()['as'];

        $route_name = isset($routeAs) ? $routeAs : null;

        if(!$this->hasPermission($route_name)) {
            return $this->makeErrorResponse('User does not have permission to perform this action', 403);
        }

        return $next($request);
    }

    private function makeErrorResponse($message, $code)
    {
        return response()->json(['status' => 'error', 'code' => $code, 'message' => $message], $code );
    }
}
