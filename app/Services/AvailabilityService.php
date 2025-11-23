<?php

namespace App\Services;

use App\Models\User;

class AvailabilityService
{

    public function hasTimeConflict(User $host, int $day, string $timeStart, string $timeEnd, ?string $excludeUuid = '')
    {
        $checkQuery = $host->availabilities()->where('day', $day)
            ->where(function ($subQuery) use ($timeStart, $timeEnd) {
                $subQuery->whereBetween('time_start', [$timeStart, $timeEnd])
                    ->orWhereBetween('time_end', [$timeStart, $timeEnd])
                    ->orWhere(function ($subSubQuery) use ($timeStart, $timeEnd) {
                        $subSubQuery->where('time_start', '<=', $timeStart)
                            ->where('time_end', '>=', $timeEnd);
                    });
            });

        if (!is_null($excludeUuid)) {
            $checkQuery->where('uuid', '!=', $excludeUuid);
        }

        return $checkQuery->exists();
    }

    public function getAvailability(User $host)
    {
        return $host->availabilities()->get();
    }

    public function upsert(User $host, array $availabilities)
    {
        $results = collect();
        foreach ($availabilities as $payload) {
            $uuid = $payload['uuid'] ?? null;
            if (!is_null($uuid)) {
                $availability = $host->availabilities()->where('uuid', $uuid)->firstOrFail();
                $availability->update($payload);
            } else {
                $availability = $host->availabilities()->create($payload);
                $availability->refresh();
            }

            $results->push($availability);
        }

        $host->availabilities()->whereNotIn('uuid', $results->pluck('uuid'))->delete();

        return $results;
    }
}
