<?php

namespace App\Services;

use App\Models\Category;


class CategoryService
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getAll()
    {
        return $this->category->paginate(10);
    }

    public function getById($id)
    {
        return $this->category->find($id);
    }

    public function store(array $data)
    {
        return $this->category->create($data);
    }

    public function update(array $data,$category)
    {
        $category = $this->getById($category->id);
        Category::where('id',$category->id)->update($data);

        return $category;
    }

    public function destroy($category)
    {
        return $category->delete();
    }
}
