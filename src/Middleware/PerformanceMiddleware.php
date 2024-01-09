<?php

namespace Framework\Baseapp\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMiddleware
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        $route = \Request::route();

        // 继续处理请求
        $response = $next($request);

        // 计算请求执行时间
        $end = microtime(true);
        $executionTime = $end - $start;

        // 获取SQL查询次数
        $queryCount = DB::getQueryLog();
        $queryCount = count($queryCount);

        // 记录性能信息
        Log::info('Request completed in ' . $executionTime . ' seconds');
        Log::info('Number of database queries: ' . '-'. $queryCount);

        return $response;
    }
}
