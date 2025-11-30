<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'appointmonet_count' => $this->appointments()->count(),
            'today_count' => $this->appointments()->whereDate('date', now()->format('Y-m-d'))->count(),
            'upcoming_count' => $this->appointments()->whereDate('date', '>', now()->format('Y-m-d'))->count(),
        ];
    }
}
