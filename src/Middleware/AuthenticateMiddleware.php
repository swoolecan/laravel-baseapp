<?php

namespace Framework\Baseapp\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

use ModelPassport\Models\User;

class AuthenticateMiddleware extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        /*if (! $request->expectsJson()) {
            return route('login');
        }*/
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
		$inSale = env('INSALETMP', false);
		if (false) {//$inSale) {
			return $this->saleTmpauth($request);
		}
        $testUser = $request->input('test_user');
        $testSorts = [
            'new' => 629380, 
            'simple' => 629221, 
            'common' => 629228, 
            'vip' => 629239
        ];
        if (!env('INTEST') || !in_array($testUser, array_keys($testSorts))) {
            return parent::authenticate($request, $guards);
        }

        $userModel = new User();
        $user = $userModel->query()->where('uid', $testSorts[$testUser])->first();
        foreach ($guards as $guard) {
            $guardModel = $this->auth->guard($guard);
            $r = $guardModel->setUser($user);
            return $this->auth->shouldUse($guard);
        }

        $this->unauthenticated($request, $guards);
    }

	public function saleTmpauth($request)
	{
		$guards = ['api'];
		$sessionId = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : false;
		if (empty($sessionId)) {
            return parent::authenticate($request, $guards);
		}
		$sPath = env('TPSESSION_PATH', '');
		$file = $sPath . '/sess_' . $sessionId;
		if (!file_exists($file)) {
            return parent::authenticate($request, $guards);
		}
		//$content = file_get_contents($file);
		//$data = unserialize(substr($content, 6));
		$uid = 629226;//isset($data['admin']) ? $data['admin']['uid'] : 0;
		$uid = isset($data['admin']) ? $data['admin']['uid'] : 0;
		if (empty($uid)) {
            return parent::authenticate($request, $guards);
		}

        $userModel = new User();
        $user = $userModel->query()->where('uid', $uid)->first();
        foreach ($guards as $guard) {
            $guardModel = $this->auth->guard($guard);
            $r = $guardModel->setUser($user);
            return $this->auth->shouldUse($guard);
        }
        $this->unauthenticated($request, $guards);
	}
}
