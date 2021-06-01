<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Helpers;

use Illuminate\Support\Str;
use Framework\Baseapp\Exceptions\BusinessException;
use Swoolecan\Foundation\Helpers\TraitResourceContainer;
use Framework\Baseapp\Services\RedisService;

//use Hyperf\Cache\Annotation\Cacheable;

/**
 * Class ResourceContainer
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Helpers
 * @license https://opensource.org/licenses/MIT MIT
 */
class ResourceContainer
{
    use TraitResourceContainer;

    public function __construct()
    {
        $config = app('config');
        $this->config = $config;
        $this->request = request();
        $this->appCode = $appCode = $this->config->get('app.app_code');
        $resources = $this->getResourceDatas('resources');
        if (empty($resources)) {
            $this->throwException(500, '应用资源不存在');
        }
        $this->resources = $resources;
    }

    protected function getResourceDatas($key = 'resources')
    {
        $datas = $this->config->get('resource');
        return $datas;
    }

    public function getModel($code, $module = '')
    {
        return self::getPointObject('model', $code, $module);
    }

    public function getRepository($code, $module = '')
    {
        return self::getPointObject('repository', $code, $module);
    }

    public function getPointObject($type, $code, $module, $returnObj = true)
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

    public function getRouteParam($param)
    {
        $param = \Request::route($param);
        return $param;
    }

    public function setRoute($route, $domain, $domainRoutes)
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

    public function formatRouteAction($route, $domainRoute)
    {
        $action = $domainRoute['action'] ?? $route;
        $action = empty($action) ? 'home' : $action;
        $strpos = strpos($action, '{');
        $action = $strpos ? substr($action, 0, $strpos) : $action;
        $action = trim($action, '/');
        return Str::camel($action);
    }

    public function initRouteDatas()
    {
        $routes = $this->config->get('routes');
        return $routes;
    }

    public function throwException($code = 400, $message = '参数有误')
    {
        throw new BusinessException($code, $message);
    }

    public function strOperation($string, $operation, $params = [])
    {
        switch ($operation) {
        case 'singular':
            return Str::singular($string);
        case 'studly':
            return Str::studly($string);
        }
    }

    public function getObjectByClass($class)
    {
        return app()->make($class);
    }
}
