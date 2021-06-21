<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Middleware;
 
use Closure;

class PermissionMiddleware
{
    public function handle($request, Closure $next)
    {
        $service = app()->make(\ModulePassport\Services\UserPermissionService::class);
        $route = \Request::route();
        $routeCode = $route->getName();
        $permission = $service->getPointPermission($routeCode);
        if (empty($permission)) {
            $service->throwException(400, '操作不存在');
        }
        $rolePermissions = $request->get('rolePermissions');

        $check = $service->checkPermissionTo($permission, $rolePermissions);
        if (empty($check)) {
            $service->throwException(403, '无权进行该操作');
        }
        return $next($request);
    }
}
