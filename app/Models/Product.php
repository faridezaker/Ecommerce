<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ["name","brand_id","category_id","slug","primary_image","description","is_active","status","delivery_amount","delivery_amount_per_product"];
}
