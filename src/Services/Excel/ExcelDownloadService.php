<?php

namespace Framework\Baseapp\Services\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Models\Sale\SalePeople;

class ExcelDownloadService implements FromArray
//class ExcelDownloadService implements FromCollection
{
	protected $sourceDatas;

	public function setSourceDatas($sourceDatas)
	{
		$this->sourceDatas = $sourceDatas;
	}

	public function collection()
	{
		return SalePeople::all();
	}

    public function array(): array
	{
		return $this->sourceDatas;
	}
}
