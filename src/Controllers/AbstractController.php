<?php

namespace Framework\Baseapp\Controllers;

use Framework\Baseapp\Services\ExcelService;
use Illuminate\Routing\Controller as BaseController;
use Maatwebsite\Excel\Facades\Excel;
use Swoolecan\Foundation\Controllers\TraitController;
use Framework\Baseapp\Helpers\ResourceContainer;

abstract class AbstractController extends BaseController
{
    use TraitController;

    public $resource;
    public $config;
    public $request;

    public function __construct()
    {
        $this->resource = app(ResourceContainer::class);
        $this->config = config();
        $this->request = request();
    }

    public function success($datas = [], $message = 'OK')
    {
        $message = $message ?: 'OK';
        return \responseJson(200, $message, $datas);
    }

    public function error($code, $message, $datas = [])
    {
        return \responseJson($code, $message, $datas);
    }

    public function successCustom($datas = [], $message = 'OK')
    {
        $message = $message ?: 'OK';
        return \responseJsonCustom(200, $message, $datas);
    }

    public function importData($fileType)
    {
        $excelService = new ExcelService();

        $fileObj = $this->request->file('import_file');
        if (empty($fileObj)) {
            $this->resource->throwException(400, '没有上传文件!');
        }
        $extName = $fileObj->extension();
        $extName = $extName == 'zip' ? 'xlsx' : $extName;
        if (!in_array($extName, ['xlsx', 'xls'])) {
            $this->resource->throwException(400, '上传类型有误!');
        }
        $file = $fileObj->storeAs('import', $fileType . '.' . $extName);

        $datas = Excel::toArray($excelService, $file);
        return $datas;
    }

}
