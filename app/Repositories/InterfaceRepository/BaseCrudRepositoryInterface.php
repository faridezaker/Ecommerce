<?php

namespace App\Repositories\InterfaceRepository;

interface BaseCrudRepositoryInterface
{
    public function getAll();

    public function store(array $data);

    public function update(array $data, $model);

    public function delete($model);
}
