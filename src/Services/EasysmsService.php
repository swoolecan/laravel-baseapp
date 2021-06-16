<?php

namespace Framework\Baseapp\Services;

use Swoolecan\Foundation\Services\TraitEasysmsService;

class EasysmsService extends AbstractService
{
    use TraitEasysmsService;
    protected $redis;

    public function __construct()
    {
        parent::__construct();
    }

    protected function pointRepository()
    {
        return false;
    }

    protected function cacheCode($key)
    {
        $redis = $this->getServiceObj('redis');
        $redis->set($key, $this->createInfo);
        return $this->createInfo;
    }

    protected function getCodeInfo($key)
    {
        $redis = $this->getServiceObj('redis');
        $info = $redis->get($key, true);
        return $info;
        echo $key;exit();
        return null;
    }
}
