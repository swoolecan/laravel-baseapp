<?php 

namespace Framework\Baseapp\Criteria;

class OrCriteria extends AbstractCriteria
{
    public function _pointApply($query, $repository)
    {
        $field = $this->getField();
        if (empty($field)) {
            return $query;
        }
        $operator = $this->params['operator'];
        $value = $this->params['value'];
        $query->where($field, $operator, $value);

        return $query;
    }
}
