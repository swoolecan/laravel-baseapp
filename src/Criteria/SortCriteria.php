<?php 

namespace Framework\Baseapp\Criteria;

class SortCriteria extends AbstractCriteria
{
    public function _pointApply($query, $repository)
    {
        foreach ($this->params as $field => $sortType) {
            $sortType = in_array($sortType, ['asc', 'desc']) ? $sortType : 'desc';
            $query->orderBy($field, $sortType);
        }

        return $query;
    }
}
