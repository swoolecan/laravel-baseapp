<?php

declare(strict_types=1);

namespace Framework\Baseapp\RpcServer;

use App\Services\UserPermissionService;

class PassportRpcServer extends AbstractRpcServer
{
    public function getResourceDatas(): array
    {
        return ['a' => 'b'];
    }

    public function getRouteDatas(): array
    {
        return ['a' => 'b'];
    }

    public function getUserById($id): array
    {
        $userPermission = make(UserPermissionService::class);
        $user = $userPermission->getUserById($id);
        if (empty($user)) {
            return ['code' => 400, 'message' => 'Token获取用户失败'];
        }

        return ['code' => '200', 'message' => 'OK', 'data' => $user];
    }

    public function loginUserById($id): array
    {
        $user = $this->getUserById($id);
        if (!isset($user['data'])) {
            return $user;
        }

        $token = (string) $this->jwt->getToken(['user_id' => $id]);
        $result = [
            'user' => $user['data'],
            'access_token' => $token,
            'expires_in' => $this->jwt->getTTL(),
        ];
        return ['code' => '200', 'message' => 'OK', 'data' => $result];
    }

    public function checkPermission($token, $routeCode): array
    {
        return ['code' => 200, 'message' => 'OK'];
    }

    public function getAttachmentInfos($params)
    {
        return $this->getResource()->getObject('repository', 'passport-attachmentInfo')->getDatas($params);
    }

    public function getAttachmentInfo($params, $onlyUrl)
    {
        return $this->getResource()->getObject('repository', 'passport-attachmentInfo')->getData($params, $onlyUrl);
    }
}
