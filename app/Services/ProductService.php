<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ProductService
{
    protected $product;
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getAll()
    {
        return $this->product->paginate(10);
    }

    public function getById($id)
    {
        return $this->product->find($id);
    }

    public function store(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {

                $primaryImageName = Carbon::now()->timestamp . '.' . $data["primary_image"]->getClientOriginalExtension();
                $data['primary_image']->storeAs('images/products', $primaryImageName, 'public');
                $data["primary_image"] = $primaryImageName;


                $product = $this->product->create($data);


                if (!empty($data['images']) && is_array($data['images'])) {
                    $imagesToInsert = [];

                    foreach ($data['images'] as $image) {
                        $imageName = Carbon::now()->timestamp . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->storeAs('images/products', $imageName, 'public');

                        $imagesToInsert[] = [
                            'product_id' => $product->id,
                            'image' => $imageName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    ProductImage::insert($imagesToInsert);
                }

                return ["status"=>true,'product'=>$product];
            });
        } catch (\Exception $e) {
            return ["status"=>false,'product'=>null,"message"=>$e->getMessage()];
        }
    }

    public function update(array $data, Product $product)
    {
        try {
            return DB::transaction(function () use ($data, $product) {

                if (!empty($data['primary_image'])) {
                    $primaryImageName = Carbon::now()->timestamp . '.' . $data["primary_image"]->getClientOriginalExtension();
                    $data['primary_image']->storeAs('images/products', $primaryImageName, 'public');
                    $data["primary_image"] = $primaryImageName;
                } else {
                    unset($data["primary_image"]);
                }

                $product->update($data);

                if (!empty($data['images']) && is_array($data['images'])) {
                    foreach ($product->images as $image) {
                        Storage::disk('public')->delete('images/products/' . $image->image);
                        $image->delete();
                    }

                    $imagesToInsert = [];
                    foreach ($data['images'] as $image) {
                        $imageName = Carbon::now()->timestamp . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->storeAs('images/products', $imageName, 'public');

                        $imagesToInsert[] = [
                            'product_id' => $product->id,
                            'image' => $imageName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    ProductImage::insert($imagesToInsert);
                    $product->refresh();
                }

                return ["status" => true, 'product' => $product];

            });
        } catch (\Exception $e) {
            return ["status" => false, 'product' => null, "message" => $e->getMessage()];
        }
    }

}
