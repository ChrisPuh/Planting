<?php

// App\Domains\Admin\Plants\Mappers\PlantTimelineMapper.php
namespace App\Domains\Admin\Plants\Mappers;

use Illuminate\Support\Carbon;

class PlantTimelineMapper
{
    public function createDummyTimelineEvents(array $plantData): array
    {
        $isAdmin = auth()->user()?->is_admin ?? false;
        $createdAt = Carbon::parse($plantData['created_at']);
        $updatedAt = $plantData['updated_at'] ? Carbon::parse($plantData['updated_at']) : null;
        $deletedAt = $plantData['deleted_at'] ? Carbon::parse($plantData['deleted_at']) : null;

        $events = [];

        // Plant Request
        if ($plantData['requested_by']) {
            $events[] = [
                'type' => 'requested',
                'by' => $plantData['requested_by'],
                'at' => $plantData['requested_at'],
                'show_by' => $isAdmin
            ];
        }

        // Creation
        $events[] = [
            'type' => 'created',
            'by' => $plantData['created_by'],
            'at' => $plantData['created_at'],
            'show_by' => true
        ];

        // Dummy Contribution Requests
        $events[] = [
            'type' => 'update_requested',
            'by' => 'Lisa Weber',
            'at' => $createdAt->copy()->addHours(6),
            'show_by' => $isAdmin,
            'details' => ['Beschreibung']
        ];

        $events[] = [
            'type' => 'update_requested',
            'by' => 'Thomas Müller',
            'at' => $createdAt->copy()->addHours(18),
            'show_by' => $isAdmin,
            'details' => ['Botanischer Name']
        ];

        // Admin Update
        if ($updatedAt) {
            $events[] = [
                'type' => 'updated',
                'by' => $plantData['updated_by'],
                'at' => $updatedAt,
                'show_by' => true,
                'details' => ['Beschreibung']
            ];
        }

        // Delete/Restore
        if ($deletedAt) {
            $events[] = [
                'type' => 'deleted',
                'by' => $plantData['deleted_by'],
                'at' => $deletedAt,
                'show_by' => $isAdmin
            ];

            $events[] = [
                'type' => 'restored',
                'by' => 'Admin User',
                'at' => $deletedAt->copy()->addMinutes(30),
                'show_by' => $isAdmin
            ];
        }

        return $events;
    }
}
