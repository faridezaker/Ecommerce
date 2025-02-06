<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\BaseCrudRepository;

class CategoryService extends BaseCrudRepository
{
    protected $category;
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }
}
