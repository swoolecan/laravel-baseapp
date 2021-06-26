<?php

namespace Framework\Baseapp\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\ParameterBag;
 
/**
 * Class ApiCaseConverter
 *
 * 1. 将前端发送来的请求参数的驼峰命名转换为后端的下划线命名
 * 2. 将后端响应参数的下划线命名转换为前端的驼峰命名
 *
 * @package App\Http\Middleware
 */
class ApiCaseConverterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->convertRequestNameCase($request);
 
        /** @var Response $response */
        $response = $next($request);
 
        $this->convertResponseNameCase($response);
 
        return $response;
    }
 
    /**
     * 转换请求参数中的下划线命名转换为驼峰命名
     *
     * @param Request $request
     */
    private function convertRequestNameCase($request)
    {
        $this->convertParameterNameCase($request->request);
        $this->convertParameterNameCase($request->query);
        $this->convertParameterNameCase($request->files);
        $this->convertParameterNameCase($request->cookies);
    }
 
    /**
     * 将参数名称的驼峰命名转换为下划线命名
     *
     * @param ParameterBag $parameterBag
     */
    private function convertParameterNameCase($parameterBag)
    {
        $parameters = $parameterBag->all();
        $newParameters = [];
        foreach ($parameters as $key => $value) {
            $newParameters[Str::snake($key)] = $value;
        }
 
        $parameterBag->replace($newParameters);
    }
 
    /**
     * 将响应中的参数命名从下划线命名转换为驼峰命名
     *
     * @param Response $response
     */
    private function convertResponseNameCase($response)
    {
        $content = $response->getContent();
        $json = json_decode($content, true);
        if (is_array($json)) {
            $json = $this->recursiveConvertNameCaseToCamel($json);
            $json['data'] = empty($json['data']) ? (object)[] : $json['data'];
            $response->setContent(json_encode($json));
        }
    }
 
    /**
     * 循环迭代将数组键值转换为驼峰格式
     *
     * @param array $arr
     * @return array
     */
    private function recursiveConvertNameCaseToCamel($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
 
        $outArr = [];
        foreach ($arr as $key => $value) {
            $outArr[Str::camel($key)] = $this->recursiveConvertNameCaseToCamel($value);
        }
 
        return $outArr;
    }
}
