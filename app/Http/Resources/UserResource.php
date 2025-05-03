<?php

namespace App\Http\Resources;

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
            'Message' => 'User data retrieved successfully',
            'User'=>[
                'id'=>$this->id,
                'user_name'=>$this->name,
                'email'=>$this->email,
            ],
            'token'=>$this->createToken('auth_token')->plainTextToken,
        ];
    }
}
