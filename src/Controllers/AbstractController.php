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

    public function success($datas = [], $message = 'OK')
    {
        $message = $message ?: 'OK';
        return \responseJson(200, $message, $datas);
    }

    public function error($code, $message, $datas = [])
    {
        return \responseJson($code, $message, $datas);
    }

    public function successCustom($datas = [], $message = 'OK')
    {
        $message = $message ?: 'OK';
        return \responseJsonCustom(200, $message, $datas);
    }

}
