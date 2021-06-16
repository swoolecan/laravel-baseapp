<?php

namespace Framework\Baseapp\Services;

use Swoolecan\Foundation\Services\TraitService;
use Framework\Baseapp\Helpers\ResourceContainer;

class AbstractService
{
    use TraitService;

    public $resource;
    public $config;
    public $request;

    public function __construct()
    {
        $this->resource = app(ResourceContainer::class);
        $this->config = config();
        $this->request = request();
    }
}
