<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Swoolecan\Foundation\Resources\TraitResource;
use Framework\Baseapp\Helpers\ResourceContainer;

class AbstractResource extends JsonResource
{
    use TraitResource;
    public $preserveKeys = true;
    public $with = ['code' => 200, 'message' => 'OK'];

    public function toArray($request = null): array
    {
        return $this->_toArray($request);
    }

    public function getResource()
    {
        return app(ResourceContainer::class);
    }
}
