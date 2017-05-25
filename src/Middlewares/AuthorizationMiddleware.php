<?php namespace Moregold\Infrastructure\Middlewares;

use Closure;
use LucaDegasperi\OAuth2Server\Authorizer;
use Moregold\Infrastructure\Auth\PermissionRepository;

class AuthorizationMiddleware {

	public $authorizer;

    public function __construct(Authorizer $authorizer, PermissionRepository $permission)
    {
        $this->authorizer = $authorizer;
        $this->permission = $permission;
    }

    public function getCurrentUserId()
    {
    	return $this->authorizer->getResourceOwnerId();
    }

    public function validateAuthentication()
    {
        try {
            $this->authorizer->validateAccessToken();
            return true;    
        } catch (Exception $e) {
            return false;
        }
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
        $access_token = str_replace('Bearer ', '', $request->headers->get('authorization'));

        if (!$access_token) {
            return $this->makeErrorResponse('Authorization Token Required', 400);
        }

        if ($this->validateAuthentication()) {
            return $this->makeErrorResponse('Authorization token invalid or expired', 401);
        }

        return $next($request);
    }

    private function makeErrorResponse($message, $code)
    {
        return response()->json(['status' => 'error', 'code' => $code, 'message' => $message], $code );
    }
}
