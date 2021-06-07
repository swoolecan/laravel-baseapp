<?php

namespace Framework\Baseapp\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Swoolecan\Foundation\Controllers\TraitController;
use Framework\Baseapp\Helpers\ResourceContainer;

abstract class AbstractController extends BaseController
{
    use TraitController;

    public $resource;
    public $config;
    public $request;

    public function __construct()
    {
        $this->resource = app(ResourceContainer::class);
        $this->config = config();
        $this->request = request();
    }

    public function success($datas)
    {
        return \responseJson(200, 'success', $datas);
    }

    /*public function getRequest()
    {
        return request();
    }*/
}
