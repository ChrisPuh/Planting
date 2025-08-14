<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $uuid
 * @property string $name Current name of the plant
 * @property string $type Current plant type (gemuese, blume, etc.)
 * @property string|null $category Current category (wurzelgemuese, kraeuter, etc.)
 * @property string|null $latin_name Current scientific name
 * @property string|null $description Current description
 * @property string|null $image_url Current image URL
 * @property bool $is_deleted Currently deleted status
 * @property bool $was_community_requested Was originally requested by community
 * @property string|null $created_by Who created this plant
 * @property string|null $last_updated_by Who last updated this plant
 * @property \Illuminate\Support\Carbon|null $last_event_at When last event occurred
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool $is_active
 * @property-read string $type_display
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlantTimelineProjection> $timelineEvents
 * @property-read int|null $timeline_events_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant byCategory(string $category)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant communityRequested()
 * @method static \Database\Factories\PlantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant notDeleted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereLastEventAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereLastUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereLatinName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plant whereWasCommunityRequested($value)
 */
	class Plant extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $plant_uuid Plant aggregate UUID - no FK constraint in event sourcing
 * @property string $event_type requested, created, updated, update_requested, deleted, restored
 * @property string $performed_by Username who performed this action
 * @property \Illuminate\Support\Carbon $performed_at When this event occurred
 * @property array<array-key, mixed>|null $event_details Details like changed_fields, etc.
 * @property string|null $display_text Human-readable text for UI
 * @property int $sequence_number Order of events for this plant
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array|null $changed_fields
 * @property-read string $event_type_display
 * @property-read array|null $requested_fields
 * @property-read \App\Models\Plant|null $plant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection byType(string $eventType)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection byUser(string $username)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection chronological()
 * @method static \Database\Factories\PlantTimelineProjectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection reverseChronological()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereDisplayText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereEventDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection wherePerformedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection wherePerformedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection wherePlantUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereSequenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlantTimelineProjection whereUpdatedAt($value)
 */
	class PlantTimelineProjection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $uuid Request aggregate UUID
 * @property string|null $plant_uuid Target plant UUID (null for new plants)
 * @property string $request_type Type: new_plant, update_contribution
 * @property array<array-key, mixed> $proposed_data Proposed plant data or changes
 * @property string $reason Reason for the request
 * @property string $requested_by Username who made the request
 * @property \Illuminate\Support\Carbon $requested_at When request was submitted
 * @property string $status Status: pending, approved, rejected
 * @property string|null $admin_comment Admin review comment
 * @property string|null $reviewed_by Admin who reviewed the request
 * @property \Illuminate\Support\Carbon|null $reviewed_at When request was reviewed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $days_old
 * @property-read bool $is_approved
 * @property-read bool $is_new_plant_request
 * @property-read bool $is_pending
 * @property-read bool $is_rejected
 * @property-read bool $is_reviewed
 * @property-read bool $is_update_request
 * @property-read array $proposed_fields
 * @property-read string $proposed_fields_display
 * @property-read string|null $proposed_plant_name
 * @property-read string $request_type_display
 * @property-read string $status_display
 * @property-read \App\Models\Plant|null $plant
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection byUser(string $username)
 * @method static \Database\Factories\RequestQueueProjectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection newPlantRequests()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection oldestFirst()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection recentFirst()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection updateRequests()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereAdminComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection wherePlantUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereProposedData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereRequestType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RequestQueueProjection whereUuid($value)
 */
	class RequestQueueProjection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property bool $is_admin
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

