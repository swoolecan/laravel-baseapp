<?php

namespace Framework\Baseapp\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
//use Prettus\Repository\Contracts\Transformable;

/**
 * Class ModelTransformer // package Prettus\Repository\Transformer
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Transformers
 * @license https://opensource.org/licenses/MIT MIT
 */
class AbstractTransformer extends TransformerAbstract
{
    /*public function transform(Transformable $model)
    {
        return $model->transform();
    }*/

    public function transform(Model $object)
    {
        return $object->attributesToArray();
    }

    /**
     * Include User.
     *
     * @param BaseModel $model
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(BaseModel $model)
    {
        $user = $model->user;
        if ($user) {
            return $this->item($user, new UserTransformer(), 'user');
        }
    }

    /**
     * Include User.
     *
     * @param BaseModel $model
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthUser(BaseModel $model)
    {
        $user = auth_user();
        if ($user) {
            return $this->item($user, new UserTransformer());
        }
    }

    protected $availableIncludes = ['user', 'comments'];

    public function transform(Post $post)
    {
        return $post->attributesToArray();
    }

    public function includeUser(Post $post)
    {
        return $this->item($post->user, new UserTransformer());
    }

    public function includeComments(Post $post, ParamBag $params = null)
    {
        $limit = 10;
        if ($params) {
            $limit = (array) $params->get('limit');
            $limit = (int) current($limit);
        }

        $comments = $post->comments()->limit($limit)->get();
        $total = $post->comments()->count();

        return $this->collection($comments, new PostCommentTransformer())->setMeta(['total' => $total]);
    }
}
