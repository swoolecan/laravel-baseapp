<?php

namespace Framework\Baseapp\Helpers;

class TreeTool
{
    public static function createTree(& $infos, $parent = '', $indexBy = 'key', $parentField = 'parent_code', $keyField = 'code')
    {
        $datas = [];
        foreach ($infos as $key => $info) {
            if ($info[$parentField] != $parent) {
                continue;
            }
            unset($infos[$key]);

            $info['subInfos'] = self::createTree($infos, $info[$keyField], $indexBy, $parentField, $keyField);
            if ($indexBy == 'num') {
                $datas[] = $info;
            } else {
                $datas[$info[$keyField]] = $info;
            }
        }
        return $datas;
    }
}
