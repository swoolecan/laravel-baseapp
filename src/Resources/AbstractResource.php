<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Swoolecan\Foundation\Resources\TraitResource;

class AbstractResource extends JsonResource
{
    use TraitResource;

    public function __construct($resource, $scene = '', $repository = null, $simpleResult = false)
    {
        $this->setScene($scene);
        $this->_repository = $repository;
        $this->_simpleResult = $simpleResult;
        parent::__construct($resource);
    }
}
