<?php

namespace Framework\Baseapp\Middleware;
 
use Closure;

class PayloadAttemptMiddleware
{
    use TraitUser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $userData = $this->getUserData($request, false);
        $request->request->set('current_user', $userData);
        return $next($request);
    }
}

