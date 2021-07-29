<?php

namespace Framework\Baseapp\Services;

use Illuminate\Support\Facades\Redis;
use Swoolecan\Foundation\Services\TraitRedisService as TraitRedisServiceBase;

trait TraitRedisService
{
    use TraitRedisServiceBase;

    public function init()
    {
        $this->redis = $this->setRedis();
    }

    public function setRedis($redis = 'default')
    {
        $redis = Redis::connection($redis);
        //$redis = app("redis.common");
        return $redis;
    }
}
