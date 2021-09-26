<?php

namespace Framework\Baseapp\Exceptions;

use Exception;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Framework\Baseapp\Exceptions\BusinessException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        BusinessException::class
        //AuthorizationException::class,
        //HttpException::class,
        //ModelNotFoundException::class,
        //ValidationException::class,
        //HttpResponseException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $route = $request->route();//->getAction();
        $action = empty($route) ? ['middleware' => ['no']] : $route->getAction();
        //if (false) {
        if (isset($action['middleware']) && in_array('api', $action['middleware'])) {
            if ($exception instanceof JWTException) {
                $mapExceptions = [
                    TokenInvalidException::class => '无效的token',
                    JWTException::class => '无效的token',
                    TokenBlacklistedException::class => 'token 已被加入黑名单,请重新登录'
                ];

                $msg = $mapExceptions[get_class($exception)] ?? $exception->getMessage();
                $msg .= get_class($exception);
                return responseJsonAsUnAuthorized($msg);
            }
            if ($exception instanceof AuthenticationException) {
                $msg = '你没有权限';
                $msg .= get_class($exception);
                return responseJsonAsUnAuthorized($msg);
            } elseif ($exception instanceof BusinessException) {
                return responseJson($exception->getCode(), $exception->getMessage());
            } elseif ($exception instanceof ValidationException) { // 拦截表单验证错误抛出的异常
                return responseJsonAsBadRequest($exception->validator->errors()->first());
            }

            return responseJsonAsServerError($exception->getMessage());
        }

        /*if ($request->is('api/e-commerce*') && $exception instanceof ValidationException) {
            return  response()->json(['status'=>1,"msg"=>$exception->validator->errors()->first()]);
        }*/

        return parent::render($request, $exception);
    }
}
