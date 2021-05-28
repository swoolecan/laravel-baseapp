<?php

namespace Framework\Baseapp\Helpers;

class StringTool
{
    /**
     * 数字转换为中文
     * @param  string|integer|float  $num  目标数字
     * @return string
     */
    public static function numberToChinese($number)
    {
        if (!is_numeric($number)) {
            return $number; // '含有非数字非小数点字符！';
        }
        $char = ['零','一','二','三','四','五','六','七','八','九'];
        $unit = ['','十','百','千','','万','亿','兆'];

        $retval  = '点';
        // 小数部分
        if (strpos($number, '.')) {
            list($num, $dec) = explode('.', $number);
            $dec = strval(round($dec, 2));
            for($i = 0,$c = strlen($dec);$i < $c;$i++) {
                $retval .= $char[$dec[$i]];
            }
        }

        // 整数部分
        $str = strrev(intval($number));
        for ($i = 0, $c = strlen($str); $i < $c; $i++) {
            $out[$i] = $char[$str[$i]];
            $out[$i] .= $str[$i] != '0'? $unit[$i%4] : '';
            if($i>1 and $str[$i]+$str[$i-1] == 0){
                $out[$i] = '';
            }
            if($i%4 == 0){
                $out[$i] .= $unit[4+floor($i/4)];
            }
        }
        $retval = join('', array_reverse($out)) . $retval;
        return $retval;
    }
}
