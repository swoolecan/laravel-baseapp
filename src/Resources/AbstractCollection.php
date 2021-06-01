<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Resources;

use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Swoolecan\Foundation\Resources\TraitCollection;

class AbstractCollection extends ResourceCollection
{
    use TraitCollection;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     */
    public function __construct($resource, $scene, $repository, $simpleResult = false)
    {
        $this->setScene($scene);
        $this->repository = $repository;
        $this->simpleResult = $simpleResult;
        parent::__construct($resource);
    }
}
