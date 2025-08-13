<?php

namespace App\Domains\RequestManagement\Projectors;

use App\Domains\RequestManagement\Events\PlantCreationRequested;
use App\Domains\RequestManagement\Events\PlantUpdateRequested;
use App\Domains\RequestManagement\Events\RequestApproved;
use App\Domains\RequestManagement\Events\RequestRejected;
use App\Models\RequestQueueProjection;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class RequestQueueProjector extends Projector
{
    /**
     * Handle PlantCreationRequested event - creates new plant request entry
     */
    public function onPlantCreationRequested(PlantCreationRequested $event): void
    {
        RequestQueueProjection::create([
            'uuid' => $event->requestId,
            'plant_uuid' => $event->plantId,
            'request_type' => 'new_plant',
            'proposed_data' => $event->proposedData,
            'reason' => $event->reason,
            'requested_by' => $event->requestedBy,
            'requested_at' => $event->requestedAt,
            'status' => 'pending',
            'admin_comment' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    /**
     * Handle PlantUpdateRequested event - creates plant update request entry
     */
    public function onPlantUpdateRequested(PlantUpdateRequested $event): void
    {
        RequestQueueProjection::create([
            'uuid' => $event->requestId,
            'plant_uuid' => $event->plantId,
            'request_type' => 'update_contribution',
            'proposed_data' => $event->proposedChanges,
            'reason' => $event->reason,
            'requested_by' => $event->requestedBy,
            'requested_at' => $event->requestedAt,
            'status' => 'pending',
            'admin_comment' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    /**
     * Handle RequestApproved event - updates request status to approved
     */
    public function onRequestApproved(RequestApproved $event): void
    {
        $request = RequestQueueProjection::whereUuid($event->requestId)->first();

        if (!$request) {
            \Log::error("Request not found for approval: {$event->requestId}");
            return;
        }

        $request->update([
            'status' => 'approved',
            'admin_comment' => $event->comment,
            'reviewed_by' => $event->reviewedBy,
            'reviewed_at' => $event->reviewedAt,
        ]);
    }

    /**
     * Handle RequestRejected event - updates request status to rejected
     */
    public function onRequestRejected(RequestRejected $event): void
    {
        $request = RequestQueueProjection::whereUuid($event->requestId)->first();

        if (!$request) {
            \Log::error("Request not found for rejection: {$event->requestId}");
            return;
        }

        $request->update([
            'status' => 'rejected',
            'admin_comment' => $event->comment,
            'reviewed_by' => $event->reviewedBy,
            'reviewed_at' => $event->reviewedAt,
        ]);
    }

    /**
     * Reset projection - delete all request queue entries
     * Used for rebuilding projections from scratch
     */
    public function resetState(): void
    {
        RequestQueueProjection::truncate();
    }
}
