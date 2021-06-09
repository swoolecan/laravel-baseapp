<?php

namespace Framework\Baseapp\Presenters;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Prettus\Repository\Presenter\FractalPresenter;
use Framework\Baseapp\Transformers\AbstractTransformer;
use Framework\Baseapp\Helpers\ResourceContainer;
use Swoolecan\Foundation\Helpers\TraitResourceManager;

/**
 * Class Presenter
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Presenters
 * @license https://opensource.org/licenses/MIT MIT
 */
abstract class AbstractPresenter extends FractalPresenter
{
    use TraitResourceManager;
    protected $meta = [];
    /**
     * @var string
     */
    protected $transformer = null;

    public function __construct()
    {
        $this->resource = app(ResourceContainer::class);
        $this->config = config();
        parent::__construct();
    }

    /**
     * @param string $transformer
     */
    public function setTransformer(string $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        if ($this->transformer) {
            return new $this->transformer();
        } else {
            return new AbstractTransformer();
        }
    }

    /**
     * Prepare data to present.
     *
     * @param Collection $data data
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function present($data)
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new \Exception(trans('repository::packages.league_fractal_required'));
        }

        if ($data instanceof Collection) {
            $this->resource = $this->transformCollection($data);
        } elseif ($data instanceof AbstractPaginator) {
            $this->resource = $this->transformPaginator($data);
        } else {
            $this->resource = $this->transformItem($data);
        }

        // set meta
        $this->resource->setMeta($this->meta);

        return $this->fractal->createData($this->resource)->toArray();
    }

    /**
     * Set Meta
     *
     * @param array $meta meta
     *
     * @return mixed
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }
}
