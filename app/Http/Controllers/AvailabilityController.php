<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvailabilityResource;
use App\ResponseFormatter;
use AvailabilityService;
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
    public function upsert(Request $request) {}
}
