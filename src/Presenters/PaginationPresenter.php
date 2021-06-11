<?php

namespace ModuleInfocms\Presenters;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

class PaginationPresenter
{
    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator
     */
    protected $pagination = null;

    /**
     * Create a new RepositoryInterface instance.
     *
     * @param array $lists
     * @return string
     */
    public function pagination($lists)
    {
        //Initialize work for BackendPagination.
        $this->pagination = new LengthAwarePaginator(
            $lists['data'],
            $lists['meta']['pagination']['total'],
            $lists['meta']['pagination']['per_page'],
            $lists['meta']['pagination']['current_page'],
            [
                'path'     => Paginator::resolveCurrentPath(), //注释2
                'pageName' => config('yeelight.backend.pagination.pageName'),
            ]
        );

        $this->pagination->appends(Input::all());

        return $this->paginationRanger().
            $this->paginationLinks();
    }

    /**
     * Get Pagination links.
     *
     * @return string
     */
    protected function paginationLinks()
    {
        return $this->pagination->render('backend.pagination');
    }

    /**
     * Get range infomation of BackendPagination.
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function paginationRanger()
    {
        $parameters = [
            'first' => $this->pagination->firstItem(),
            'last'  => $this->pagination->lastItem(),
            'total' => $this->pagination->total(),
        ];

        $parameters = collect($parameters)->flatMap(function ($parameter, $key) {
            return [$key => "<b>$parameter</b>"];
        });

        return trans('backend.pagination.range', $parameters->all());
    }

    public function render()
    {
        if ($this->hasPages()) {
            return sprintf(
                '<ul class="am-pagination am-pagination-centered">%s %s %s</ul>',
                $this->getPreviousButton(),
                $this->getLinks(),
                $this->getNextButton()
            );
        }

        return '';
    }

    /**
     * Get HTML wrapper for disabled text.
     *
     * @param string $text
     *
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<li class="am-disabled"><span>'.$text.'</span></li>';
    }

    /**
     * Get HTML wrapper for active text.
     *
     * @param string $text
     *
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<li class="am-active"><span>'.$text.'</span></li>';
    }
}
