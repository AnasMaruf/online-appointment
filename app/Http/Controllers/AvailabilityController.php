<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvailabilityResource;
use App\ResponseFormatter;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    protected $availabilityService;
    public function __construct(AvailabilityService $service)
    {
        $this->availabilityService = $service;
    }
    public function index()
    {
        $availabilities = $this->availabilityService->getAvailability(auth()->user());
        return ResponseFormatter::success(AvailabilityResource::collection($availabilities));
    }
    public function upsert(Request $request)
    {
        $validator = \Validator::make(request()->all(), [
            'availabilities' => 'required|array',
            'availabilities.*.uuid' => 'nullable|exists:availabilities,uuid',
            'availabilities.*.day' => 'required|integer|min:1|max:7',
            'availabilities.*.time_start' => 'required|date_format:H:i',
            'availabilities.*.time_end' => 'required|date_format:H:i',
        ]);

        if (!$validator) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $payload = $validator->validated()['availabilities'];

        foreach ($payload as $key => $item) {
            if ($this->availabilityService->hasTimeConflict(
                request()->user(),
                $item['day'],
                $item['time_start'],
                $item['time_end'],
                $item['uuid'] ?? null,
            )) {
                return ResponseFormatter::error(400, [], [
                    'Waktu bentrok pada item ke-' . ($key + 1)
                ]);
            }
        }

        $updatedAvailabilities = $this->availabilityService->upsert(auth()->user(), $payload);

        return ResponseFormatter::success(AvailabilityResource::collection($updatedAvailabilities));
    }
}
