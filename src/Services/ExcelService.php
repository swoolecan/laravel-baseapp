<?php

declare(strict_types = 1);

namespace Framework\Baseapp\Services;

use Maatwebsite\Excel\Facades\Excel;

class ExcelService extends AbstractService
{
    public function excelDatas($file = null, $ignoreFirst = true, $type = '', $fileField = 'excel_file')
    {
        $excelService = new ExcelService();
        $file = is_null($file) ? $this->fileByRequest($fileField, $type) : $file;

        $datas = Excel::toArray($excelService, $file);
        $datas = isset($datas[0]) ? $datas[0] : false;
        if ($ignoreFirst && isset($datas[0])) {
            unset($datas[0]);
        }
        return $datas;
    }

    protected function fileByRequeset($fileField, $type)
    {
        $fileObj = $request->file($fileField);
        $extName = $fileObj->extension();
        $extName = $extName == 'zip' ? 'xlsx' : $extName;
        $file = $fileObj->storeAs('import', $type . '.' . $extName);
        return $file;
    }
}
