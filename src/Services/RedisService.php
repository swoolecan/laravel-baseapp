<?php

namespace Framework\Baseapp\Services;

use Swoolecan\Foundation\Services\TraitRedisService;

class RedisService extends AbstractService
{
    use TraitRedisService;

    public function init()
    {
        $this->redis = $this->setRedis();
    }

    public function setRedis($redis = null)
    {
        if (is_null($redis)) {
            //$redis = \Redis::connection('default');
            $redis = app("redis.connection");
        }
        return $redis;
    }
}
