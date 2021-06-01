<?php

namespace Framework\Baseapp\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Swoolecan\Foundation\Controllers\TraitController;
use Framework\Baseapp\Helpers\ResourceContainer;

abstract class AbstractController extends BaseController
{
    use TraitController;

    public $resource;

    public function __construct()
    {
        $this->resource = app(ResourceContainer::class);
        $this->request = request();
    }

    public function getRequestObj($scene = '', $repository = null, $code = '')
    {
        //$type = empty($action) ? 'request' : 'request-' . $action;
        $code = !empty($code) ? $code : get_called_class();
        //$request = $this->resource->getObject($type, $code, false);
        $request = $this->resource->getObject('request', $code, false);
        if (empty($request)) {
            return $this->request;
        }
        if ($repository) {
            $request->setRepository($repository);
        }
        $request->setScene($scene);

        if (method_exists($request, 'validateResolved')) {
            $request->validateResolved();
        }
        return $request;
    }

    public function dealCriteria($scene, $repository, $params)
    {
        return $repository->getDealSearchFields($scene, $params);
    }

    public function getVersion()
    {
        return \Request::header('version');
    }

    public function getRouteParam($param)
    {
        return \ResourceManager::getRouteParam($param);
    }

    public function getRequest()
    {
        return request();
    }
}
