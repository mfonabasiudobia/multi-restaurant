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
        $profilePhoto = null;
        
        if ($this->media) {
            $profilePhoto = \Illuminate\Support\Facades\Storage::disk('s3')->url($this->media->src);
        } elseif ($this->thumbnail) {
            $profilePhoto = $this->thumbnail;
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_verified' => $this->phone_verified_at ? true : false,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at ? true : false,
            'is_active' => (bool) $this->is_active,
            'profile_photo' => $profilePhoto,
            'media_id' => $this->media_id,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'country' => $this->country,
            'phone_code' => $this->phone_code,
        ];
    }
}
