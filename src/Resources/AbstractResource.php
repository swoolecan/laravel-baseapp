<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Swoolecan\Foundation\Resources\TraitResource;

class AbstractResource extends JsonResource
{
    use TraitResource;
    public $with = ['code' => 200, 'message' => 'OK'];

    public function toArray($request = null): array
    {
        return $this->_toArray($request);
    }
}
