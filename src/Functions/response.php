<?php

/**
 * 创建成功使用的状态码
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsCreated($message = 'created success', $data = [], $extFields = [])
{
    return responseJson(201, $message, $data, $extFields);
}

/**
 * 修改成功
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsDeleted($message = 'deleted success', $data = [], $extFields = [])
{
    return responseJson(204, $message, $data, $extFields);
}

/**
 * 表单验证错误使用
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsBadRequest($message = 'bad request', $data = [], $extFields = [])
{
    return responseJson(400, $message, $data, $extFields);
}


/**
 * 身份验证失败。
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsUnAuthorized($message = 'un authorized', $data = [], $extFields = [])
{
    return responseJson(401, $message, $data, $extFields);
}


/**
 * 用户身份过期, 需重新登录。
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsAccountExpired($message = 'account expired', $data = [], $extFields = [])
{
    return responseJson(402, $message, $data, $extFields);
}

/**
 * 权限不足
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsForbidden($message = 'forbidden', $data = [], $extFields = [])
{
    return responseJson(403, $message, $data, $extFields);
}


/**
 * 未找到使用 404
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsNoFound($message = 'no found', $data = [], $extFields = [])
{
    return responseJson(404, $message, $data, $extFields);
}


/**
 * 服务器未知错误
 *
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJsonAsServerError($message = 'server error', $data = [], $extFields = [])
{
    return responseJson(500, $message, $data, $extFields);
}

/**
 * 正常状态使用
 *
 * @param int    $code
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function responseJson($code = 200, $message = 'success', $data = [], $extFields = [])
{
    $data = empty($data) ? (object)[] : $data;
    $responseCodeFormat = config('app.responseCodeFormat');
    //print_r($responseCodeFormat);exit();
    if (!empty($responseCodeFormat)) {
        $code = $responseCodeFormat[$code] ?? $responseCodeFormat['default'];
    }

    //$responseData = compact('code', 'message', 'data');
    $responseFormat = config('app.responseFormat');
    $codeKey = $responseFormat['code'] ?? 'code';
    $messageKey = $responseFormat['message'] ?? 'message';
    $dataKey = $responseFormat['data'] ?? 'data';
    $responseData = [
        $codeKey => intval($code),
        $messageKey => $message,
        $dataKey => $data,
    ];
    $responseData = array_merge($responseData, $extFields);

    return response()->json($responseData);
}

/**
 * 正常状态使用
 *
 * @param int    $code
 * @param string $message
 * @param array  $data
 * @param array  $extFields
 * @return \Illuminate\Http\JsonResponse
 */
function ResponseJsonCustom($code = 200, $message = 'success', $data = [], $extFields = [])
{
    $data = empty($data) ? (object)[] : $data;
    $data['status'] = $code == 200 ? 'success' : 'error';
    $data['message'] = $message;
    $responseData = array_merge($data, $extFields);

    return response()->json($responseData);
}
