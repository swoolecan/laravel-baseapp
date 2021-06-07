<?php

namespace Framework\Baseapp\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Swoolecan\Foundation\Controllers\TraitController;
use Framework\Baseapp\Helpers\ResourceContainer;

abstract class AbstractController extends BaseController
{
    use TraitController;

    public $resource;
    public $request;

    public function __construct()
    {
        $this->resource = app(ResourceContainer::class);
        $this->request = request();
    }

    /*public function getRequest()
    {
        return request();
    }*/
}
