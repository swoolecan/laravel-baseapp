<?php

namespace Framework\Baseapp\Criteria;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Criteria\RequestCriteria as BaseRequestCriteria;

class RequestCriteria extends BaseRequestCriteria
{
    /**
     * Apply criteria in query repository.
     *
     * @param  Builder|Model  $model
     * @param  RepositoryInterface  $repository
     *
     * @return mixed
     * @throws \Exception
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $sortedBy = $this->request->get(config('repository.criteria.params.sortedBy', 'sortedBy'), 'asc');
        $cursor = $this->request->get(config('repository.criteria.params.cursor', 'cursor'), null);
        $sortedBy = ! empty($sortedBy) ? Str::lower($sortedBy) : 'asc';

        if ($cursor) {
            $keyType = $model->getKeyType();
            $key = $model->getKeyName();
            $cursor = in_array($keyType, ['int', 'integer']) ? (int) $cursor : $cursor;

            $model = ($sortedBy === 'asc') ? $model->where($key, '>', $cursor) : $model->where($key, '<', $cursor);
        }

        return parent::apply($model, $repository);
    }

    /**
     * Parser query to Search String.
     *
     * @param $columns
     *
     * @return string
     */
    protected function parserQueryToSearch($columns)
    {
        /** @var \Illuminate\Http\Request $request * */
        $request = Request::instance();

        $query = $request->query();
        $search = '';
        $searchFields = '';

        foreach ($columns as $index => $column) {
            if (is_int($index)) {
                $index = $column;
                $column = '=';
            }
            if (isset($query[$index]) && !empty($query[$index])) {
                $search .= $index.':'.$query[$index].';';
                $searchFields .= $index.':'.$column.';';
            }
        }

        return [rtrim($search, ';'), rtrim($searchFields, ';')];
    }
}
