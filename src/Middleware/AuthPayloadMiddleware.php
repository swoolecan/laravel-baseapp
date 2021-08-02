<?php

namespace Framework\Baseapp\Middleware;

use Closure;
use Framework\Baseapp\Exceptions\BusinessException;
 
class AuthPayloadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $inTest = config('app.inTest');
        $jwtResult = auth('api')->getPayload()->get('userData');
        if (empty($jwtResult) || empty($jwtResult->id)) {
            throw new BusinessException('您没有权限');
        }
        $request->request->set('jwtResult', $jwtResult);

        return $next($request);
    }
}
