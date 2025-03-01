<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Product\StoreProductRequest;
use App\Http\Requests\V1\Product\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $ProductService;
    public function __construct(ProductService $ProductService)
    {
        $this->ProductService = $ProductService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->ProductService->getAll();

        return self::success([
            'products' => ProductResource::collection($products->load('images')),
            'links'=> ProductResource::collection($products)->response()->getData()->links,
            'meta' => ProductResource::collection($products)->response()->getData()->meta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $insertProduct = $this->ProductService->store($request->validated());
        if ($insertProduct['status']) {
            return self::success(new ProductResource($insertProduct['product']),'Product created successfully.');
        }else{
            return self::error($insertProduct['message']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return self::success(new ProductResource($product->load('images')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $updateProduct = $this->ProductService->update($request->validated(),$product);
        if ($updateProduct['status']) {
            return self::success(new ProductResource($updateProduct['product']),'Product created successfully.');
        }else{
            return self::error($updateProduct['message']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
