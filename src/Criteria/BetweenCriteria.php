<?php 

namespace Framework\Baseapp\Criteria;

class BetweenCriteria extends AbstractCriteria
{
    public function _pointApply($query, $repository)
    {
        $field = $this->getField();
        if (empty($field)) {
            return $query;
        }
        $value = $this->params['value'];
        $value = explode('|', $value);

        $query->whereBetween($field, $value);

        return $query;
    }
}
