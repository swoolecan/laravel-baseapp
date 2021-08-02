<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Middleware;

use Closure;
use Framework\Baseapp\Exceptions\BusinessException;

class UserCenterMiddleware
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
        $jwtResult = $request->get('jwtResult');
        //$passportBase = make(PassportRpcClient::class);
        $passportBase = app()->make(\Framework\Baseapp\RpcClient\PassportRpcClient::class);
        $user = $passportBase->getUserById($jwtResult->id);
        if (!isset($user['code']) || $user['code'] != 200) {
            $message = isset($user['message']) ? $user['message'] : 'Token未验证通过';
            $code = isset($user['code']) ? $user['code'] : 401;
            throw new BusinessException($code, $message);
        }
        $request->request->set('current_user', $user['data']);
        return $next($request);
    }
}
