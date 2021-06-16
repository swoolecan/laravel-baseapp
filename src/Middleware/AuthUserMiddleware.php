<?php

namespace Framework\Baseapp\Middleware;

use Closure;

class AuthUserMiddleware
{
    /**
     * Perform authentication before a request is executed.
     *
     * @param \Illuminate\Http\Request $request Request
     * @param \Closure $next Closure
     * @param string $grant grant
     *
     * @throws OAuthServerException
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $user = auth()->user();
        $request->request->set('current_user', $user);

        return $next($request);
    }
}
