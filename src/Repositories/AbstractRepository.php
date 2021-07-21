<?php

namespace Framework\Baseapp\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Framework\Baseapp\Criteria\RequestCriteria;
use Framework\Baseapp\Helpers\ResourceContainer;
use Swoolecan\Foundation\Repositories\TraitRepository;

/**
 * Class AbstractRepository
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Repositories
 * @license https://opensource.org/licenses/MIT MIT
 */
abstract class AbstractRepository extends BaseRepository
{
    use TraitRepository;

    public function __construct()
    {
        $app = app();
        $this->resource = app(ResourceContainer::class);
        $this->config = config();
        parent::__construct($app);
    }

    public function model()
    {
        return $this->resource->getClassName('model', get_called_class());
    }

    public function getModuleCode()
    {
        return '';
    }

    public function __call($name, $arguments)
    {   
        return $this->model->{$name}(...$arguments);
    }

    public function getModel()
    {
        $modelCode = !empty($this->pointModel) ? $this->pointModel : get_called_class();
        $this->model = $this->resource->getObject('model', $modelCode);
        //$this->criteria = $collection;
        $this->resetScope();

        return $this->model;
    }

    /*public function presenter()
    {
        return PostPresenter::class;
    }*/

    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /*public function checkPermission()
    {
        $authUser = auth('api')->user();

        // 登录用户是否有 Posts 数据表操作权限
        $permission = PermissionEnum::DATA_POSTS()->name;
        if (! $authUser->can($permission)) {
            throw UnauthorizedException::forPermissions(Arr::wrap($permission));
        }
    }*/

    public function searchPage()
    {
        // 使用预加载，避免 N+1
        $posts = $this->model->with('author')->published()->paginate(10);

        return $this->parserResult($posts);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->first($columns);
    }
}
