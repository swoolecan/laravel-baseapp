<?php

declare(strict_types=1);

namespace Framework\Baseapp\RpcClient;

class PassportRpcClient extends AbstractRpcClient
{
    public function getServer()
    {
        $class = "\Framework\Baseapp\RpcServer\PassportRpcServer";
        return $this->getResource()->getObjectByClass($class);
    }

    public function getUserById($id)
    {
        return $this->getServer()->getUserById($id);
    }

    public function getAttachmentInfos($params): array
    {
        return $this->getServer()->getAttachmentInfos($params);
    }

    public function getAttachmentInfo($params, $onlyUrl)
    {
        return $this->getServer()->getAttachmentInfo($params, $onlyUrl);
    }
}
