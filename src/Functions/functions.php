<?php

require __DIR__ . '/response.php';

if (! function_exists('initDatabaseData')) {
    function initDatabaseData($elems)
    {
        $datas = [];
        $default = [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX', ''),
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ];
        foreach ($elems as $elem) {
            $datas[$elem] = $default;
            if ($elem == 'mysql') {
                continue;
            }
            foreach (array_keys($default) as $key) {
                if ($key == 'options') {
                    continue;
                }
                $value = env('DB_' . strtoupper($key) . '_' . strtoupper($elem));
                if (!is_null($value)) {
                    $datas[$elem][$key] = $value;
                }
            }
        }
        return $datas;
    }
}

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param  string  $path
     * @return string
     */
    function app_path($path = '')
    {
        return app('path').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

/**
 * 根据路径生成一个图片标签
 *
 * @param string       $url
 * @param string $disk
 * @param int    $width
 * @param int    $height
 * @return string
 */
function image($url, $disk = 'public', int $width = 50, int $height = 50) : string
{
    if (is_null($url) || empty($url)) {

        $url = get404Image();
    } else {

        $url = assertUrl($url, $disk);
    }

    return "<img width='{$width}' height='{$height}' src='{$url}' />";
}

function assertUrl($url, $disk = 'public')
{
    static $driver  = null;

    if (is_null($url) || empty($url)) {

        return get404Image();
    }

    if (is_null($driver)) {
        $driver = Storage::disk($disk);
    }

    if (! starts_with($url, 'http')) {
        $url = $driver->url($url);
    }

    return $url;
}

function get404Image()
{
    return asset('images/404.jpg');
}


/**
 * 把字符串变成固定长度
 *
 * @param     $str
 * @param     $length
 * @param     $padString
 * @param int $padType
 * @return bool|string
 */
function fixStrLength($str, $length, $padString = '0', $padType = STR_PAD_LEFT)
{
    if (strlen($str) > $length) {
        return substr($str, strlen($str) - $length);
    } elseif (strlen($str) < $length) {
        return str_pad($str, $length, $padString, $padType);
    }

    return $str;
}


/**
 * 价格保留两位小数
 *
 * @param $price
 * @return float|int
 */
function ceilTwoPrice($price)
{
    return round($price, 2);
}


/**
 * 或者设置的配置项
 *
 * @param \App\Enums\SettingKeyEnum $settingEnum
 * @param null                        $default
 * @return mixed|null
 */
function setting(\App\Enums\SettingKeyEnum $settingEnum, $default = null)
{
    $key = \App\Models\Setting::cacheKey($settingEnum->getValue());

    $val = Cache::get($key);
    if (is_null($val)) {

        $val = \App\Models\Setting::query()->where('key', $settingEnum->getValue())->value('value');
        if (is_null($val)) {
            return $default;
        }

        Cache::put($key, $val);
    }

    return $val;
}

/**
 * 生成系统日志
 *
 * @param       $description
 * @param array $input
 */
function createSystemLog($description, $input = [])
{
    $operate = new \Encore\Admin\Auth\Database\OperationLog();
    $operate->path = config('app.url');
    $operate->method = 'GET';
    $operate->ip = '127.0.0.1';
    $operate->input = json_encode($input);
    $operate->description = $description;
    $operate->save();
}
//转数组
function objectToArray($object) {
    //先编码成json字符串，再解码成数组
    return json_decode(json_encode($object), true);
}
/**
 * 判断远程图片是否存在
 * @param $url
 * @return bool
 */
function imgExists($url){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_NOBODY, true); // 不取回数据
    curl_exec($curl); // 发送请求
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if($httpCode==200){
        return true;
    }else{
        return false;
    }
}



function getUserAvatarById($uid)
{
    $md5 = md5($uid);

    $sc  = '/' . substr($md5, 0, 2) . '/' . substr($md5, 2, 2) . '/' . substr($md5, 4, 2);

    $fileName = config('imageUrl.cos_storage_url').$sc."/".$uid.".jpg";

    $cosUrl = config('imageUrl.cosv5_cdn').$fileName;

    return $cosUrl;
}

/**
 * 阿拉伯数字转中文
 * @param $num
 * @return mixed
 */
function numeral($num){
    $china=array('零','一','二','三','四','五','六','七','八','九');
    $arr=str_split($num);
    for($i=0;$i<count($arr);$i++){
        return $china[$arr[$i]];
    }
}

/**
 * 字帖评测年级类型
 * @param $num
 * @return mixed
 */
function numeralGrade($num){
    $china=array('零','一年级','二年级','三年级','四年级','五年级','六年级','自由打卡','diy7*10','笔画偏旁','一年级同步','二年级同步','diy5*5','一年级上册期末','二年级上册期末');
    return $china[$num];
//    $arr=str_split($num);
//    for($i=0;$i<count($arr);$i++){
//        return $china[$arr[$i]];
//    }
}



/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 17-10-25
 * Time: 下午4:51.
 */
if (!function_exists('current_auth_user')) {

    /**
     * @param bool $throwException
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     * @return \Yeelight\Models\Foundation\BaseUser
     */
    function current_auth_user($throwException = true)
    {
        $user = app('Dingo\Api\Auth\Auth')->user(false);
        $user = !empty($user) ? $user : \Auth::user();
        if (!$user || !($user instanceof \Yeelight\Models\Foundation\BaseUser)) {
            if ($throwException) {
                throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('User Invalid Or Token Expired');
            }
        }

        return $user;
    }
}

if (!function_exists('auth_check')) {

    /**
     * @return bool
     */
    function auth_check()
    {
        return \Auth::check();
    }
}

if (!function_exists('current_full_url')) {
    function current_full_url($withQueryString = true)
    {
        $url = \Request::url();
        $query = $withQueryString ? (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null) : null;

        if ($query) {
            $path = \Request::path();
            if (starts_with($query, $path.'&')) {
                $query = substr($query, strlen($path) + 1);
            } elseif (starts_with($query, $path)) {
                $query = substr($query, strlen($path));
            }
        }

        $url = $query ? $url.'?'.$query : $url;

        return $url;
    }
}

if (!function_exists('get_client_ip')) {

    /**
     * @return array|string
     */
    function get_client_ip()
    {
        $request = request();
        $clientIp = $request->header('X-Client-Ip');
        if (empty($clientIp)) {
            $clientIp = $request->getClientIp(true);
        }

        return $clientIp;
    }
}

if (!function_exists('app_locale')) {
    function app_locale()
    {
        return \App::getLocale();
    }
}

if (!function_exists('app_timezone')) {

    /**
     * @return mixed
     */
    function app_timezone()
    {
        return \Config::get('app.timezone');
    }
}

if (!function_exists('jwt_token')) {

    /**
     * @return string|null
     */
    function jwt_token()
    {
        $jwt_token = \Session::get('jwt_token');
        if (is_jwt_token_valid_for_refresh($jwt_token, true)
            || (empty($jwt_token) && \Auth::check())
        ) {
            $refreshed_token = refresh_jwt_token();
            if (!empty($refreshed_token)) {
                $jwt_token = $refreshed_token;
            }
        }

        return $jwt_token;
    }
}

if (!function_exists('refresh_jwt_token')) {

    /**
     * @return string|null
     */
    function refresh_jwt_token()
    {
        $jwt_token = null;
        if (\Auth::check()) {
            $jwt_token = \JWTAuth::fromUser(current_auth_user());
            \Session::put('jwt_token', $jwt_token);
        }

        return $jwt_token;
    }
}

if (!function_exists('is_jwt_token_valid_for_refresh')) {

    /**
     * @param $token
     * @param bool $allowExpireRefresh
     *
     * @return bool
     */
    function is_jwt_token_valid_for_refresh($token, $allowExpireRefresh = false)
    {
        $is_jwt_token_valid_for_refresh = false;

        try {
            $payload = \JWTAuth::getPayload($token);
            $exp = $payload->get('exp');
            $nbf = $payload->get('nbf');
            if ($exp > 0 && $nbf > 0) {
                $nowTime = \Carbon\Carbon::now('UTC');
                $expireTime = \Carbon\Carbon::createFromTimestampUTC($exp);
                $validTime = \Carbon\Carbon::createFromTimestampUTC($nbf);

                // if now time is after valid time
                if ($nowTime->gt($validTime)) {
                    $minutesAfterValid = $nowTime->diffInMinutes($validTime);
                    $minutesBeforeExpire = $nowTime->diffInMinutes($expireTime);
                    $totalValidLength = $validTime->diffInMinutes($expireTime);
                    $halfAmountOfMinutes = floor($totalValidLength / 2);
                    if ($minutesAfterValid >= $halfAmountOfMinutes) {
                        $is_jwt_token_valid_for_refresh = true;
                    }
                }
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            if ($allowExpireRefresh) {
                $is_jwt_token_valid_for_refresh = true;
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        }

        return $is_jwt_token_valid_for_refresh;
    }
}

if (!function_exists('phone_parse')) {

    /**
     * @param string $phone_number
     * @param string $country_code An ISO 3166-1 two letter country code
     *
     * @return null|\libphonenumber\PhoneNumber
     */
    function phone_parse($phone_number, $country_code)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $phoneNumberProto = $phoneUtil->parseAndKeepRawInput($phone_number, $country_code);

            return $phoneNumberProto;
        } catch (\libphonenumber\NumberParseException $e) {
            return;
        }
    }
}

if (!function_exists('phone_model_from')) {

    /**
     * @param string $phone_number
     * @param string $country_code An ISO 3166-1 two letter country code
     *
     * @return \Yeelight\Models\Basic\PhoneNumberModel
     */
    function phone_model_from($phone_number, $country_code)
    {
        return new \Yeelight\Models\Basic\PhoneNumberModel($phone_number, $country_code);
    }
}

if (!function_exists('ip_to_country_iso_code')) {
    function ip_to_country_iso_code($ip = null, $default_iso_code = 'US')
    {
        if (empty($ip)) {
            $ip = get_client_ip();
        }

        $location = \GeoIP::getLocation($ip);

        // check if NOT returned default
        if ($location['default'] === false && !empty($location['iso_code'])) {
            return $location['iso_code'];
        } elseif ($location['default'] === false && !empty($location['isoCode'])) {
            return $location['isoCode'];
        } else {
            return $default_iso_code;
        }
    }
}

if (!function_exists('collection_paginate')) {

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param $perPage
     * @param string $pageName
     * @param null   $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    function collection_paginate(\Illuminate\Support\Collection $collection, $perPage, $pageName = 'page', $page = null)
    {
        $page = $page ?: \Illuminate\Pagination\Paginator::resolveCurrentPage($pageName);

        $results = $collection->forPage($page, $perPage);

        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);

        return new \Illuminate\Pagination\LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
                'pageName' => $pageName,
                'path'     => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query'    => $query,
            ]
        );
    }
}

if (!function_exists('str_contains_ascii_only')) {
    function str_contains_ascii_only($string)
    {
        return preg_match('%^[ -~]+$%', $string, $m);
    }
}

if (!function_exists('in_arrayi')) {
    function in_arrayi($needle, $haystack)
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}

if (!function_exists('auth_user')) {

    /**
     * @param bool $throwException
     *
     * @return null|\Yeelight\Models\Foundation\BaseUser|\Yeelight\Models\Foundation\BaseUser
     */
    function auth_user($throwException = true)
    {
        return current_auth_user($throwException);
    }
}

if (!function_exists('yee_mix')) {
    function yee_mix($path, $manifestDirectory = '', $supportHot = true)
    {
        $path = mix($path, $manifestDirectory);
        if (!$supportHot) {
            $hotUrl = '//localhost:8080';
            if (starts_with($path, $hotUrl)) {
                $path = str_replace($hotUrl, '', $path);
            }
        }

        return $path;
    }
}

if (!function_exists('rest_client')) {

    /**
     * @param null $service_name
     * @param null $debug_mode
     *
     * @return \Yeelight\Services\Rest\RestClient
     */
    function rest_client($service_name = null, $debug_mode = null)
    {
        return new \Yeelight\Services\Rest\RestClient($service_name, $debug_mode);
    }
}

if (!function_exists('json_encode_safe')) {
    function json_encode_safe($value, $options = 0, $depth = 512)
    {
        $encoded = json_encode($value, $options, $depth);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw new Exception('Maximum stack depth exceeded');
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('Underflow or the modes mismatch');
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw new Exception('Syntax error, malformed JSON');
            case JSON_ERROR_UTF8:
                $clean = utf8ize($value);

                return json_encode_safe($clean, $options, $depth);
            default:
                throw new Exception('Unknown error');
        }
    }
}

if (!function_exists('json_decode_safe')) {
    function json_decode_safe($json, $assoc = false, $depth = 512)
    {
        $decoded = json_decode($json, $assoc, $depth);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $decoded;
            case JSON_ERROR_DEPTH:
                throw new Exception('Maximum stack depth exceeded');
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception('Underflow or the modes mismatch');
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception('Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw new Exception('Syntax error, malformed JSON');
            case JSON_ERROR_UTF8:
                $clean = utf8ize($decoded);

                return json_decode_safe($clean, $assoc, $depth);
            default:
                throw new Exception('Unknown error');
        }
    }
}

if (!function_exists('utf8ize')) {
    function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return utf8_encode($mixed);
        }

        return $mixed;
    }
}

if (!function_exists('read_exif_data_safe')) {
    function read_exif_data_safe($file, $sections_needed = null, $sub_arrays = null, $read_thumbnail = null)
    {
        $data = null;

        try {
            $data = read_exif_data($file);
        } catch (\Exception $e) {
            $data = null;
        }

        return $data;
    }
}
