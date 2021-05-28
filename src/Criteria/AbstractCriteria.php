<?php

namespace Framework\Baseapp\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

abstract class Criteria implements CriteriaInterface
{
    protected $field;
    protected $value;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param $query
     * @param RepositoryInterface $repository
     * @param array $params
     * @return mixed
     */
    public abstract function apply($query, RepositoryInterface $repository)
    {
        return $this->_pointApply($query, $repository);
    }

    public function _pointApply($query, $repository)
    {
        return $query;
    }

    public function getField()
    {
        return isset($this->params['field']) ? $this->params['field'] : false;
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function _applyBase($query, $repository)
    {
        $field = $this->getField();
        if (empty($field)) {
            return $query;
        }
        $value = $this->params['value'];
        $operator = $this->params['operator'];
        $query->where($field, $operator, $value);

        return $query;
    }

    /*public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where('user_id', '=', current_auth_user()->user_id);

        return $model;
    }

    public function __construct(SocialiteAuthRequest $request)
    {
        $this->request = $request;
    }

    public function apply1($model, RepositoryInterface $repository)
    {
        if ($this->request->email) {
            $model = $model
                ->where('email', '=', $this->request->email)
                ->orWhere('email', '=', $this->request->username)
                ->orWhere('username', '=', $this->request->username);
        } else {
            $model = $model
                ->where('username', '=', $this->request->username)
                ->orWhere('email', '=', $this->request->username);
        }

        return $model;
        $model = $model
            ->where('provider', '=', $this->request->provider)
            ->where('provider_user_id', '=', $this->request->provider_user_id);

        return $model;

        if ($name = $this->request->get('name')) {
            $query->where('name', '=', $name);
        }

        if ($email = $this->request->get('email')) {
            $query->where('email', 'like', "%$email%");
        }
    }*/
}
