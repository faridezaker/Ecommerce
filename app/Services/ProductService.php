<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function store(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {

                $primaryImageName = Carbon::now()->timestamp . '.' . $data["primary_image"]->getClientOriginalExtension();
                $data['primary_image']->storeAs('Products/Images', $primaryImageName, 'public');
                $data["primary_image"] = $primaryImageName;


                $product = Product::create($data);


                if (!empty($data['images']) && is_array($data['images'])) {
                    $imagesToInsert = [];

                    foreach ($data['images'] as $image) {
                        $imageName = Carbon::now()->timestamp . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->storeAs('Products/Images', $imageName, 'public');

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
}
