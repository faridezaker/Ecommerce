<?php

namespace App\Http\Requests\V1\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],
            "brand_id"=>["required","integer","exists:brands,id"],
            "category_id"=>["required","integer","exists:categories,id"],
            "description"=>["required","string","min:10"],
            "slug"=>["required","string","max:255","unique:products,slug"],
            "status"=>["integer","in:1,0"],
            "is_active"=>["boolean"],
            "primary_image"=>["required","image","mimes:jpg,jpeg,png","max:2048"],
            "delivery_amount"=>["nullable","integer","min:0"],
            "delivery_amount_per_product"=>["nullable","integer","min:0"],
            "images.*"=>["image","mimes:jpg,jpeg,png","max:2048"],
        ];
    }
}
