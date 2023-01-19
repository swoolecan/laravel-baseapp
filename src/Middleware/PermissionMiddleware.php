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
        if (in_array($routeCode, [''])) {
            return $next($request);
        }
        $rolePermissions = $request->get('rolePermissions');
        $basePermission = $rolePermissions['basePermission'];
        $check = false;
        foreach ((array) $basePermission as $role => $pInfos) {
            if (in_array($routeCode, array_keys($pInfos))) {
                $request->request->set('currentPermission', $pInfos[$routeCode]);
                $request->request->set('currentRole', $rolePermissions['roleDetails'][$role]);
                $check = true;
                break;
            }
        }
        if (empty($check)) {
            $service->resource->throwException(403, '操作不存在或您没有操作权限-' . $routeCode);
        }

        //$permission = $service->getPointPermission($routeCode);
        /*$check = $service->checkPermissionTo($permission, $rolePermissions);
        if (empty($check)) {
            $service->throwException(403, '无权进行该操作');
        }*/
        return $next($request);
    }
}
