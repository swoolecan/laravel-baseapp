<?php

namespace Framework\Baseapp\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Swoolecan\Foundation\Criteria\TraitCriteria;

abstract class AbstractCriteria implements CriteriaInterface
{
    use TraitCriteria;

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
    public function apply($query, RepositoryInterface $repository)
    {
        return $this->_pointApply($query, $repository);
    }

    /*public function __construct(SocialiteAuthRequest $request)
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

        if ($email = $this->request->get('email')) {
            $query->where('email', 'like', "%$email%");
        }

        $model = $model->where('user_id', '=', current_auth_user()->user_id);
    }*/
}
