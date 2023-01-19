<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Helpers;

use Illuminate\Support\Str;
use Framework\Baseapp\Exceptions\BusinessException;
use Swoolecan\Foundation\Helpers\TraitResourceContainer;

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
        $this->config = app('config');
        $this->request = request();
        $resources = $this->getBaseCache('resource');
        if (empty($resources)) {
            $this->throwException(500, '应用资源不存在');
        }
        $this->resources = $resources;
    }

    public function getRouteParam($param)
    {
        $param = \Request::route($param);
        return $param;
    }

    public function getCurrentUser()
    {
        return $this->request->get('current_user');
    }

    public function setRoute($route, $domain, $domainRoutes)
    {
        $domainRoute = $domainRoutes[$route] ?? [];
        $methods = $domainRoute['methods'] ?? ['GET'];
        $controller = $domainRoute['controller'] ?? $domain;
        $action = self::formatRouteAction($route, $domainRoute);
        $name = $domainRoute['name'] ?? $controller . '.' . $action;
        //echo '/' . $route . '====' . serialize($methods) . '--' . $controller . '==' . $action . '===' . $name . "\n <br />";
        $controller = '\ModuleWebsite\Controllers\\' . ucfirst($controller);
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
        $routes = $this->getBaseCache('permission');
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
        case 'camel':
            return Str::camel($string);
        case 'snake':
            $params = empty($params) ? '_' : $params;
            return Str::snake($string, $params);
        case 'pluralStudly':
            return Str::pluralStudly($string);
        case 'plural':
            return Str::plural($string);
        case 'length':
            return Str::length($string);
        case 'substr':
            return Str::substr($string, $params['start'], $params['length']);
        }
    }

    public function getObjectByClass($class, $params = [])
    {
        return app()->make($class, $params);
    }

    public function getBaseCache($type)
    {
        $keys = [
            'permission' => 'passport:permission:common',
            'resource' => 'passport:resource:common',
        ];
        $key = $keys[$type];
        $redis = app("redis.connection");
        $datas = $redis->get($key);
        if (empty($datas)) {
            \Log::debug('cache-empty-' . $key);
        }
        $datas = empty($datas) ? '' : json_decode($datas, true);
        $datas = $datas ?: $this->config->get($type);
        return $datas;
    }

    public function getPointDomain($code = '')
    {
        $domain = config('app.' . $code);
        return $domains[$code] ?? '';
    }
}
