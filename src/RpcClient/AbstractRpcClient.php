<?php

namespace Framework\Baseapp\RpcClient;

use Framework\Baseapp\Helpers\ResourceContainer;

class AbstractRpcClient
{

    protected function getResource()
    {
        return app(ResourceContainer::class);
    }
}
