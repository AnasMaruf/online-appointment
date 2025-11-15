<?php

use App\Models\User;

class AvailabilityService
{
    public function getAvailability(User $host)
    {
        return $host->availabilities()->get();
    }
}
