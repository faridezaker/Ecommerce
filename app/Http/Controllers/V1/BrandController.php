<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Brand\StoreBrandRequest;
use App\Http\Requests\V1\Brand\UpdateBrandRequest;
use App\Http\Resources\V1\BrandResource;
use App\Models\Brand;
use App\Services\BrandService;


class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $BrandService;
    public function __construct(BrandService $BrandService)
    {
        return $this->BrandService = $BrandService;
    }
    public function index()
    {

        $brands = $this->BrandService->getAll();
        return self::success([
            'brands' => BrandResource::collection($brands),
            'links'=>BrandResource::collection($brands)->response()->getData()->links,
            'meta'=>BrandResource::collection($brands)->response()->getData()->meta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $brand = $this->BrandService->store($request->validated());
        return self::success(new BrandResource($brand),'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return self::success(new BrandResource($brand));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand = $this->BrandService->update($request->validated(),$brand);


        return self::success(new BrandResource($brand),'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->BrandService->destroy($brand);
        return self::success(null,'Brand deleted successfully.');
    }
}
