<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;

class PhoneCodeServe
{
    public function sendCode($phone){

        if($phone == null){
            $data = [
                'status' => 1,
                'message' => '请输入手机号'
            ];
            return $data;
        }

        if(!preg_match("/^1[3456789][0-9]{9}$/", $phone)){
            $data = [
                'status' => 1,
                'message' => '请输入正确的手机号码'
            ];
            return $data;
        }

        $time = time();
        $phoneCodes = Cache::get('phone_code'.$phone);

        if ($phoneCodes&&$phoneCodes[$phone]['send_time'] > $time) {
            $data = [
                'status' => 1,
                'message' => '请勿频繁获取短信验证码'
            ];
            return $data;
        }else{
            $phoneCodes = [];
        }

        $phoneCodes[$phone]['send_time'] = $time + 60; //缓存1分钟
        $code = rand(100000, 999999);
        $phoneCodes[$phone]['code'] = $code;
        try {
            //发送短信
            $easySms =new EasySms(config('easySms'));
            $res = $easySms->send($phone, [
                'template' => env('ALIYUN_TEMPLATE'),
                'data' => [
                    'code' => $code
                ],
            ]);
        } catch (\Throwable $e) {
            $data = [
                'status' => 1,
                'message' => '发送失败'
            ];
            return $data;
        }

        if ($res['aliyun']['status'] == "success"){
            Cache::put('phone_code'.$phone,$phoneCodes,now()->addMinutes(5));
            $data = [
                'status' => 0,
                'message' => '发送成功'
            ];
            return $data;
        }else{
            $data = [
                'status' => 1,
                'message' => '发送失败'
            ];
            return $data;
        }

    }

    public function verifyCode($phone,$phoneCode){
        if($phone == null || $phoneCode == null){
            $data = [
                'status' => 1,
                'message' => '缺少参数'
            ];
            return $data;
        }

        $phoneCodes = Cache::get('phone_code'.$phone);
        if($phoneCodes == null){
            $data = [
                'status' => 1,
                'message' => '验证码已过期'
            ];
            return $data;
        }

        $code = $phoneCodes[$phone]['code'] ;
        if($code != $phoneCode){
            $data = [
                'status' => 1,
                'message' => '验证码错误'
            ];
            return $data;
        }else{
            Cache::forget('phone_code'.$phone);
            $data = [
                'status' => 0,
                'message' => '验证码正确'
            ];
            return $data;
        }

    }
}
