<?php

namespace Framework\Baseapp\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AbstractViewComponent extends Component
{
    public string $route;
    public $params;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($params = '')
    {
        $this->route = \Request::route()->getName();
        $this->params = json_decode($params, true);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        if (empty($this->getView())) {
            return '';
        }
        $datas = $this->getDatas();
        return view('components.' . $this->getView(), $datas);
    }

    public function getDatas()
    {
        $datas = $this->params;
        $datas['route'] = $this->route;

        return $datas;
    }

    public function getView()
    {
        return '';
    }
}
