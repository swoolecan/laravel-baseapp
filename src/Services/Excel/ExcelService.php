<?php

namespace Framework\Baseapp\Services\Excel;

use Maatwebsite\Excel\Facades\Excel;
//use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToArray;

class ExcelService implements ToArray
{
	public function array(array $array)
	{
		return $array;
	}
    /**
     * @param array $row
     *
     * @return User|null
     */
    /*public function model(array $row)
    {
		print_r($row);
		return $row;
        return new User([
           'name'     => $row[0],
           'email'    => $row[1], 
        ]);
	}*/
}
