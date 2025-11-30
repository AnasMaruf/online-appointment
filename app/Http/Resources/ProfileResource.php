<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->hostDetail->status,
            'username' => $this->hostDetail->username,
            'service_type' => $this->hostDetail->serviceType->only([
                'uuid',
                'name'
            ]),
            'profile_photo' => is_null($this->hostDetail->profile_photo) ? null : asset('storage/' . $this->hostDetail->profile_photo),
            'is_available' => (bool) $this->hostDetail->is_available,
            'meet_location' => $this->hostDetail->meet_location,
            'meet_timezone' => $this->hostDetail->meet_timezone,
            'is_public' => (bool) $this->hostDetail->is_public,
            'is_auto_approve' => (bool) $this->hostDetail->is_auto_approve,
        ];
    }
}
