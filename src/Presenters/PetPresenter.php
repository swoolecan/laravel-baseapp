<?php

namespace ModuleInfocms\Presenters;

use ModuleInfocms\Transformers\Infocms\PetTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class PetPresenter.
 *
 * @package namespace ModuleInfocms\Presenters;
 */
class PetPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PetTransformer();
    }
}
