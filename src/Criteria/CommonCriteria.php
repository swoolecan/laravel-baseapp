<?php 

namespace Framework\Baseapp\Criteria;

class CommonCriteria extends AbstractCriteria
{
    public function _pointApply($query, $repository)
    {
        $field = $this->getField();
        if (empty($field)) {
            return $query;
        }
        $operator = $this->params['operator'];
        $value = $this->params['value'];
        if ($operator == 'like-left') {
            $operator = 'like';
            $value = "%{$value}";
        } elseif ($operator == 'like-right') {
            $operator = 'like';
            $value = "{$value}%";
        } else if ($operator == 'like') {
            $value = "%{$value}%";
        }
        
        $query->where($field, $operator, $value);
        //echo $query->toSql() . '=======';

        return $query;
    }
}
