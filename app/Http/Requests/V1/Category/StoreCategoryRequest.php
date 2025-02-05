<?php

namespace App\Http\Requests\V1\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'parent_id'   => 'required|integer|min:0',
            'name'        => 'required|string|max:255',
            'slug'        => 'string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|string|max:255',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->sometimes('parent_id', ['exists:categories,id'], function ($input) {
            return $input->parent_id > 0;
        });
    }
}
