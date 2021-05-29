<?php

namespace Framework\Baseapp\Helpers;

use Illuminate\Support\Str;
use Framework\Baseapp\Exceptions\BusinessException;

/**
 * Class ResourceManager
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Helpers
 * @license https://opensource.org/licenses/MIT MIT
 */
class ResourceManager
{
    public static function getModel($code, $module = '')
    {
        return self::getPointObject('model', $code, $module);
    }

    public static function getRepository($code, $module = '')
    {
        return self::getPointObject('repository', $code, $module);
    }

    public static function getPointObject($type, $code, $module, $returnObj = true)
    {
        $types = [
            'repository' => 'Repositories',
        ];
        $typeCode = $types[$type] ?? ucfirst($type) . 's';
        $class = "App\\{$typeCode}\\";
        $class .= !empty($module) ? ucfirst($module) . "\\" : '';
        $class .= ucfirst($code);
        $class .= in_array($type, ['model']) ? '' : ucfirst($type);
        return new $class();
    }

    /*public function getPointModel($code, $module = '')
    {
        $code = ucfirst($code);
        if (!empty($module)) {
            $module = ucfirst($module);
            $module = "\\{$module}";
        }
        $class = "\App\Models{$module}\\{$code}";
        $model = new $class();
        //$model->adminUser = $this->user;
        return $model;
    }*/

    public static function getRouteParam($param)
    {
        $param = \Request::route($param);
        return $param;
    }

    public static function setRoute($route, $domain, $domainRoutes)
    {
        $domainRoute = $domainRoutes[$route] ?? [];
        $methods = $domainRoute['methods'] ?? ['GET'];
        $controller = $domainRoute['controller'] ?? $domain;
        $action = self::formatRouteAction($route, $domainRoute);
        $name = $domainRoute['name'] ?? $controller . '.' . $action;
        //echo '/' . $route . '====' . serialize($methods) . '--' . $controller . '==' . $action . '===' . $name . "\n <br />";
        $controller = '\ModuleInfocms\Controllers\Web\\' . ucfirst($controller);
        if ($methods === 'any') {
            \Route::any('/' . $route, ucfirst($controller) . 'Controller@' . $action)->name($name);
        } else {
            \Route::match($methods, '/' . $route, ucfirst($controller) . 'Controller@' . $action)->name($name);
        }
    }

    public static function formatRouteAction($route, $domainRoute)
    {
        $action = $domainRoute['action'] ?? $route;
        $action = empty($action) ? 'home' : $action;
        $strpos = strpos($action, '{');
        $action = $strpos ? substr($action, 0, $strpos) : $action;
        $action = trim($action, '/');
        return Str::camel($action);
    }

    public static function initRouteDatas()
    {
        return [];
    }

    public static function throwException($code = 400, $message = '参数有误')
    {
        throw new BusinessException($code, $message);
    }
}
