<?php

namespace App\Services;

use App\Models\Brand;

class BrandService
{
    protected $brand;
    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function getAll()
    {
        return $this->brand->paginate(10);
    }

    public function getById($id)
    {
        return $this->brand->find($id);
    }

    public function store(array $data)
    {
        return $this->brand->create($data);
    }

    public function update(array $data,$brand)
    {
        $brand = $this->getById($brand->id);
        Brand::where('id',$brand->id)->update($data);

        return $brand;
    }

    public function destroy($brand)
    {
        return $brand->delete();
    }
}
