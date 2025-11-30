<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Http\Resources\SummaryResource;
use App\ResponseFormatter;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }
    public function getSummary()
    {
        $user = auth()->user();
        return ResponseFormatter::success(new SummaryResource($user));
    }

    public function getProfile()
    {
        $user = auth()->user();
        return ResponseFormatter::success(new ProfileResource($user));
    }

    public function updateProfile(Request $request)
    {
        $validator = \Validator::make(request()->all(), [
            'name' => 'required|min:5|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'password' => 'nullable|min:3|confirmed',
            'username' => 'required|unique:host_details,username,' . auth()->user()->id,
            'service_type' => 'required|exists:service_types,uuid',
            'meet_location' => 'required|max:100',
            'meet_timezone' => 'required|max:2',
            'profile_photo' => 'nullable|image|max:1024',
            'is_available' => 'required|boolean',
            'is_public' => 'required|boolean',
            'is_auto_approve' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $payload = $validator->validated();
        $user = $this->profileService->updateProfile(auth()->user(), $payload);

        return ResponseFormatter::success(new ProfileResource($user));
    }
}
