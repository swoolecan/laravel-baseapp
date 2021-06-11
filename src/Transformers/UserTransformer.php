<?php

namespace ModuleInfocms\Transformers;

use ModuleInfocms\Repositories\Enums\PermissionEnum;
use ModuleInfocms\Repositories\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $data = [
            'id' => $user->id,
            'nickname' => $user->name,
            'email' => $user->email,
        ];

        if (! $this->checkColumnPermission()) {
            $data['email'] = '**** ****';
        }

        return $data;
    }

    protected function checkColumnPermission()
    {
        return auth('api')->user()->can(PermissionEnum::DATA_USERS_COLUMN_EMAIL()->name);
    }
}
