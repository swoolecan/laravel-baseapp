<?php

namespace Framework\Baseapp\Services\Excel;

use Yeelight\Repositories\Eloquent\BaseRepository as Repository;

abstract class AbstractExporter
{
    protected $repository;

    public function __construct(Repository $repository = null)
    {
        if ($repository) {
            $this->setRepository($repository);
        }
    }

    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function getTable()
    {
        return $this->repository->makeModel()->getTable();
    }

    public function getData()
    {
        return $this->repository->all();
    }

    public function chunk(callable $callback, $count = 100)
    {
        return $this->repository->chunk($callback, $count);
    }

    public function withScope($scope)
    {
        if ($scope == Exporter::SCOPE_ALL) {
            return $this;
        }

        list($scope, $args) = explode(':', $scope);

        if ($scope == Exporter::SCOPE_CURRENT_PAGE) {
            $this->repository->paginate();
        }

        if ($scope == Exporter::SCOPE_SELECTED_ROWS) {
            $selected = explode(',', $args);
            $this->repository->findWhereIn($this->repository->makeModel()->getKeyName(), $selected);
        }

        return $this;
    }

    abstract public function export();
}
