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

    public function getResourceDatas(int $a, int $b): array
    {
        return $this->__request(__FUNCTION__, compact('a', 'b'));
    }

    public function getRouteDatas(): array
    {
        $p = 'a';
        //return require('/data/htmlwww/docker/container/passport/config/autoload/routes.php');
        return $this->__request(__FUNCTION__, ['a' => 'b']);
    }

    public function checkPermission($token): array
    {
        $p = 'a';
        //return require('/data/htmlwww/docker/container/passport/config/autoload/routes.php');
        return $this->__request(__FUNCTION__, ['token' => $token]);
    }

    public function getUserById($id): array
    {
        return $this->__request(__FUNCTION__, ['id' => $id]);
    }

    public function getSingleAttachmentData($params): array
    {
        return $this->__request(__FUNCTION__, $params);
    }

    public function getAttachmentInfos($params): array
    {
        return $this->getServer()->getAttachmentInfos($params);
    }
}
