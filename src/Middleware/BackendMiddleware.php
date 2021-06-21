<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Middleware;
 
use Closure;
 
class BackendMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $user = $request->get('current_user');
        $service = app()->make(\ModulePassport\Services\UserPermissionService::class);
        $manager = $service->getManager($user, false);
        if (empty($manager)) {
            $service->resource->throwException(403, '您没有操作权限nom');
        }
        $rolePermissions = $service->getRolePermissions($manager);
        if (empty($rolePermissions)) {
            $service->resource->throwException(403, '您没有操作权限norp');
        }
        $request->request->set('manager', $manager);
        $request->request->set('rolePermissions', $rolePermissions);

        return $next($request);
    }
}
