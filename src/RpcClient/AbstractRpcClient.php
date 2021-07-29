<?php

namespace Framework\Baseapp\RpcClient;

use Framework\Baseapp\Helpers\ResourceContainer;

class AbstractRpcClient
{

    protected function getResource()
    {
        return app(ResourceContainer::class);
    }

    public function getRpcData($app, $resource, $key, $keyField = 'id')
    {
        return $this->getServer()->getRpcData($resource, $key, $keyField);
    }

    public function getRpcDatas($app, $resource)
    {
        return $this->getServer()->getRpcDatas($resource, $key, $keyField);
    }
}
