<?php

namespace ModuleInfocms\Presenters;

use ModuleInfocms\Repositories\Transformers\UserTransformer;

class UserPresenter extends Presenter
{
    /**
     * Prepare data to present.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserTransformer();
    }
}
