<?php

namespace App\Repositories;

use App\Repositories\InterfaceRepository\BaseCrudeRepositoryInterface;

class BaseCrudRepository implements BaseCrudeRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }
    public function getAll($columns = ['*'])
    {
        return $this->model::paginate(10, $columns);
    }

    public function store(array $data)
    {
        return $this->model::create($data);
    }

    public function update(array $data, $model)
    {
        $model->update($data);
        return $model->refresh();
    }

    public function delete($model)
    {
        return $model->delete();
    }

}
