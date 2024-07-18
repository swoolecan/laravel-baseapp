<?php

namespace Framework\Baseapp\Services;

use Illuminate\Http\Request;

class PageServe
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPageParameters($limit = 20, $pageField = 'page', $pageSizeField = 'page_size')
    {
        $page = $this->request->input($pageField, 1);
        $limit = $this->request->input($pageSizeField, $limit);
        $offset = ($page - 1) * $limit;

        return [$limit, $offset, $page];
    }

    public function input($key, $default = null)
    {
        return $this->request->input($key, $default);
    }
}
