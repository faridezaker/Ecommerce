<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->name,
            'email'=>$this->email,
            'cellphone'=>$this->cellphone,
            'avatar'=>$this->avatar,
            'create_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
