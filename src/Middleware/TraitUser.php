<?php

namespace Framework\Baseapp\Middleware;

trait TraitUser
{
    public function getUserData($request, $throw = true)
    {
        $testUser = $this->getTestUser($request);
        if (!empty($testUser)) {
            return $testUser;
        }

        try {
            $user = auth('api')->user();
        } catch (\Exception $exception) {
            if (empty($throw)) {
                return false;
            }
            throw new \Framework\Baseapp\Exceptions\BusinessException(401, '您没有权限');
        }
        return $user;
    }

    protected function getTestUser($request)
    {
        $testUid = $request->input('point_testuid');
        $inTest = config('app.inTest');
        if (!$inTest || empty($testUid)) {
            return false;
        }

        $user = [
            'uid' => $testUid,
            'userName' => '测试用户',
            'phone' =>'',
            'userPic' => '',
            'userRealName' => '真实姓名',
        ];
        return $user;
    }
}
