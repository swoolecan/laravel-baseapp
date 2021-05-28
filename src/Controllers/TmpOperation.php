<?php

namespace Framework\Baseapp\Controllers;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel; 
use App\Services\ExcelDownloadService;
use App\Services\ExcelService;

trait TmpOperation
{
    protected function _index($pageServe, $actionType = '', $dataExt = [])
    {
		DB::connection()->enableQueryLog();  // 开启QueryLog
        $params = $pageServe->request->all();
		$params = $this->formatPriv($params);
    	if (isset($params['code']) && $params['code'] == 403) {
    		return $params;
    	}

		$downloadData = isset($dataExt['download']) ? $dataExt['download'] : false;
		if ($downloadData) {
			$downloadData = $dataExt['download'];
			$downloadTitle = $downloadData['title'];
			$limit = isset($downloadData['limit']) ? $downloadData['limit'] : 2000;
			$page = 1;
			$offset = 0;
		} else {
            list($limit, $offset, $page) = $pageServe->getPageParameters();
		}

        $query = $this->getModel()->query();

        $query = $this->getModel()->filterCondition($query, $params, $actionType);
        if (!empty($params['order_elem']) && is_array($params['order_elem'])) {
			foreach ($params['order_elem'] as $oField => $oType) {
				$query->orderBy($oField, $oType);
			}
		} elseif (isset($params['order_field']) && !empty($params['order_field']) && !empty($params['order_type'])) {
            $orderType = isset($params['order_type']) ? $params['order_type'] : 'desc';
            $query->orderBy($params['order_field'], $orderType);
		} else {
            $query->orderBy('id', 'desc');
        }
		if (isset($params['exclude_field']) && is_array($params['exclude_field']) && !empty($params['exclude_field'])) {
			$query = $this->dealExclude($query, $params['exclude_field']);
		}

        $count = $query->count();
        $datas = $query->limit($limit)->offset($offset)->get();
		if (isset($_GET['intest'])) {
		    dump(DB::getQueryLog());
		}
		if ($downloadData) {
			$formatMethod = "_{$actionType}DownDatas";
			$downDatas = $this->getModel()->$formatMethod($datas, $params);
			return $this->_download($downDatas['datas'], $downDatas['ext'] . '_' . $downloadTitle);
		}
        $datas = $this->getResource($datas, $actionType);
        $return = array_merge([
            'infos' => $datas,
            'totalNum' => $count,
            'pageSize' => $limit,
            'currentPage' => $page
        ], $dataExt);

        return responseJsonHttp(200, 'success', $return);
    }

	protected function dealExclude($query, $excludeField)
	{
		//print_r($params);exit();
		foreach ($excludeField as $eField => $eValue) {
			$eValue = (array) $eValue;
			$query->whereNotIn($eField, $eValue);
		}
		return $query;
	}


    protected function _downloadbak($datas, $title)
    {
		$service = new ExcelDownloadService();
		$service->setSourceDatas($datas);
		//print_R($service);exit();
		$file = 'sale/' . $title . time() . '.xlsx';
		/*$file = 'sale-' . $title . '.xlsx';
        //return Excel::download($service, $file);
        $r = Excel::download($service, $file);
		$c = $r->getFile();
		$c =$c->getRealPath();
		$c = file_get_contents($c);
		//print_r($c);exit();
		return responseJsonHttp(200, 'success', $c);*/
        Excel::store($service, $file);

        return responseJsonHttp(200, 'success', config('app.url') . $file);

    }

    protected function _download($datas, $title)
    {
		$service = new ExcelDownloadService();
		$service->setSourceDatas($datas);
		$file = $title . time() . '.xlsx';
        $r = Excel::store($service, $file);

        $host = config('app.api_url');
        $host = rtrim($host, '/');
        return $this->success('success', ['url' => $host . '/smartpen/docs/' . $file]);
    }

    protected function _store($request)
    {
        $params = $request->all();
        $result = $this->getModel()->createData($params);
        if (isset($result['code']) && $result['code'] == 400) {
			return $this->error($result['message']);
        }
        return $this->success('success', $result);
    }

    protected function _update($request)
    {
        $inputs = $request->all();
        $result = $this->getModel()->updateData($inputs);
		if (isset($result['code']) && $result['code'] == 400) {
            return $this->error($result['message'], 400);
		}
        return $this->success('success', []);
    }

    protected function _delete($request)
    {
        $inputs = $request->all();
        $model = $this->getModel();
        $result = $model->deleteData($inputs);
		if (isset($result['code']) && $result['code'] == 400) {
            return $this->error($result['message'], 400);
		}
        return $this->success('success', []);
    }

    protected function _importDatas($request, $model, $type = '')
    {
        /*$resourceId = $request->input('resource_id');
        $resource = $model->getResourceById($resourceId);
        if (empty($resource)) {
            return $this->error('资源不存在', 400);
        }*/
        $excelService = new ExcelService();

        $fileObj = $request->file('excel_file');
        $extName = $fileObj->extension();
        $extName = $extName == 'zip' ? 'xlsx' : $extName;
        $file = $fileObj->storeAs('import', $type . '.' . $extName);

        $datas = Excel::toArray($excelService, $file);//"/tmp/{$type}.xlsx");
        //$datas = Excel::toArray($excelService, "/tmp/test/{$type}.xlsx");
        $datas = isset($datas[0]) ? $datas[0] : false;
        if (isset($datas[0])) {
            unset($datas[0]);
        }
        $count = count($datas);
        if (empty($count)) {
            return $this->error('没有有效的数据', 400);
        }

        $datas = $model->importDatas($datas);
        $message = '导入了' . count($datas['result']) . '条数据';
        if (isset($datas['errors']) && count($datas['errors']) > 0) {
            $message .= '; 导入失败的信息数：' . count($datas['errors']);
        }
        if (isset($datas['updates']) && count($datas['updates']) > 0) {
            $message .= '; 编辑的信息数：' . count($datas['updates']);
        }
        return $this->success($message, $datas);
    }

    protected function _downloadExcel($datas, $title = 'title')
    {
        return Excel::download(new ExcelDownloadService, $title . '.xlsx');
    }

    protected function _indexbak($pageServe, $actionType = '', $dataExt = [])
    {
        DB::connection()->enableQueryLog();  // 开启QueryLog
        $params = $pageServe->request->all();
        $params = $this->formatPriv($params);
        if (isset($params['code']) && $params['code'] == 403) {
            return $params;
        }

		$downloadData = isset($dataExt['download']) ? $dataExt['download'] : false;
		if ($downloadData) {
			$downloadData = $dataExt['download'];
			$downloadTitle = $downloadData['title'];
			$limit = isset($downloadData['limit']) ? $downloadData['limit'] : 2000;
			$page = 1;
			$offset = 0;
		} else {
            list($limit, $offset, $page) = $pageServe->getPageParameters();
		}

        $model = $this->getModel();
        $query = $model->query();

        $query = $model->filterCondition($query, $params, $actionType);
        if (!empty($params['order_elem']) && is_array($params['order_elem'])) {
            foreach ($params['order_elem'] as $oField => $oType) {
                $query->orderBy($oField, $oType);
            }
        } elseif (isset($params['order_field']) && !empty($params['order_field']) && !empty($params['order_type'])) {
            $orderType = isset($params['order_type']) ? $params['order_type'] : 'desc';
            $query->orderBy($params['order_field'], $orderType);
        } else {
            $query->orderBy($model->getKeyName(), 'desc');
        }
        if (isset($params['exclude_field']) && is_array($params['exclude_field']) && !empty($params['exclude_field'])) {
            $query = $this->dealExclude($query, $params['exclude_field']);
        }

        $count = $query->count();
        $datas = $query->limit($limit)->offset($offset)->get();
        if (isset($_GET['intest'])) {
            dump(DB::getQueryLog());
        }
        $datas = $this->getResource($datas, $actionType);

		if ($downloadData) {
			$formatMethod = "_{$actionType}DownDatas";
			$downDatas = $this->getModel()->$formatMethod($datas, $params);
			return $this->_download($downDatas['datas'], $downDatas['ext'] . '_' . $downloadTitle);
		}
        $return = array_merge([
            'infos' => $datas,
            'totalNum' => $count,
            'pageSize' => $limit,
            'currentPage' => $page
        ], $dataExt);

        return $this->success('success', $return);
    }
}
