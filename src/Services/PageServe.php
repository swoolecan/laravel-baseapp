<?php

namespace Framework\Baseapp\Services;

use Illuminate\Http\Request;

class PageServe
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPageParameters($limit = 20)
    {
        $page = $this->request->input('page', 1);
        $limit = $this->request->input('page_size', $limit);
        $offset = ($page - 1) * $limit;

        return [$limit, $offset, $page];
    }

    public function input($key, $default = null)
    {
        return $this->request->input($key, $default);
    }
}
