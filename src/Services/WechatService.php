<?php

namespace Framework\Baseapp\Services;

use Swoolecan\Foundation\Services\TraitWechatService;

class WechatService extends AbstractService
{
    use TraitWechatService;

    public function sendDingNotice($title, $content, $url)
    {
        $ding = new \DingNotice\DingTalk(config('ding'));
        $r = $ding->link($title, $content, $url);
        \Log::debug(serialize($r));
        return true;
    }
}
