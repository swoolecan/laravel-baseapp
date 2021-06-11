<?php

namespace ModuleInfocms\Transformers\Infocms;

use League\Fractal\TransformerAbstract;
use ModuleInfocms\Models\Pet;

/**
 * Class PetTransformer.
 *
 * @package namespace ModuleInfocms\Transformers;
 */
class PetTransformer extends TransformerAbstract
{
    /**
     * Transform the Pet entity.
     *
     * @param \ModuleInfocms\Entities\Pet $model
     *
     * @return array
     */
    public function transform(Pet $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
