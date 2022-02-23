<?php

namespace Framework\Baseapp\Services;

use Swoolecan\Foundation\Services\TraitWechatService as TraitWechatServiceBase;

trait TraitWechatService
{
    use TraitWechatServiceBase;

    public function sendDingNotice($title, $content, $url)
    {
        $ding = new \DingNotice\DingTalk(config('ding'));
        $r = $ding->link($title, $content, $url);
        \Log::debug(serialize($r));
        return true;
    }
}
