<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Category\StoreCategoryRequest;
use App\Http\Requests\V1\Category\UpdateCategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $CategoryService;
    public function __construct(CategoryService $CategoryService)
    {
        return $this->CategoryService = $CategoryService;
    }

    public function index()
    {
        $categories = $this->CategoryService->getAll();
        return self::success([
           'categories' => CategoryResource::collection($categories),
            'links'=> CategoryResource::collection($categories)->response()->getData()->links,
            'meta' => CategoryResource::collection($categories)->response()->getData()->meta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $storeCategoryRequest)
    {
        $category = $this->CategoryService->store($storeCategoryRequest->validated());
        return self::success(new CategoryResource($category),'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(category $category)
    {
        return self::success(new CategoryResource($category));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->CategoryService->update($request->validated(),$category);
        return self::success(new CategoryResource($category),'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
       $this->CategoryService->delete($category);
        return self::success(null,'Category deleted successfully.');
    }

    public function children(Category $category)
    {
        return self::success(new CategoryResource($category->load('children')));
    }

    public function parent(Category $category)
    {
        return self::success(new CategoryResource($category->load('parent')));
    }
}
