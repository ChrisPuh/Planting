<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestQueueProjection extends Model
{
    protected $table = 'request_queue_projections';

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'plant_uuid',
        'request_type',
        'proposed_data',
        'reason',
        'requested_by',
        'requested_at',
        'status',
        'admin_comment',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'proposed_data' => 'array',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the plant that this request refers to (for update requests)
     * Note: No foreign key constraint - validation happens at application level
     */
    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_uuid', 'uuid');
    }

    // ===== Query Scopes =====

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('request_type', $type);
    }

    public function scopeNewPlantRequests($query)
    {
        return $query->where('request_type', 'new_plant');
    }

    public function scopeUpdateRequests($query)
    {
        return $query->where('request_type', 'update_contribution');
    }

    public function scopeByUser($query, string $username)
    {
        return $query->where('requested_by', $username);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderByDesc('requested_at');
    }

    public function scopeOldestFirst($query)
    {
        return $query->orderBy('requested_at');
    }

    // ===== Accessor Methods =====

    public function getIsNewPlantRequestAttribute(): bool
    {
        return $this->request_type === 'new_plant';
    }

    public function getIsUpdateRequestAttribute(): bool
    {
        return $this->request_type === 'update_contribution';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Ausstehend',
            'approved' => 'Genehmigt',
            'rejected' => 'Abgelehnt',
            default => ucfirst($this->status)
        };
    }

    public function getRequestTypeDisplayAttribute(): string
    {
        return match ($this->request_type) {
            'new_plant' => 'Neue Pflanze',
            'update_contribution' => 'Verbesserungsvorschlag',
            default => ucfirst($this->request_type)
        };
    }

    /**
     * Get the proposed plant name for new plant requests
     */
    public function getProposedPlantNameAttribute(): ?string
    {
        return $this->proposed_data['name'] ?? null;
    }

    /**
     * Get the fields that were proposed to be changed (for update requests)
     */
    public function getProposedFieldsAttribute(): array
    {
        if (!$this->is_update_request) {
            return [];
        }

        return array_keys($this->proposed_data);
    }

    /**
     * Get formatted list of proposed fields for display
     */
    public function getProposedFieldsDisplayAttribute(): string
    {
        $fields = $this->proposed_fields;

        if (empty($fields)) {
            return '';
        }

        $fieldTranslations = [
            'name' => 'Name',
            'type' => 'Typ',
            'category' => 'Kategorie',
            'latin_name' => 'Lateinischer Name',
            'description' => 'Beschreibung',
            'image_url' => 'Bild',
        ];

        $translatedFields = array_map(
            fn($field) => $fieldTranslations[$field] ?? ucfirst($field),
            $fields
        );

        if (count($translatedFields) === 1) {
            return $translatedFields[0];
        }

        if (count($translatedFields) === 2) {
            return implode(' und ', $translatedFields);
        }

        $last = array_pop($translatedFields);
        return implode(', ', $translatedFields) . ' und ' . $last;
    }

    /**
     * Check if request has been reviewed
     */
    public function getIsReviewedAttribute(): bool
    {
        return $this->reviewed_at !== null;
    }

    /**
     * Get days since request was made
     */
    public function getDaysOldAttribute(): int
    {
        return $this->requested_at->diffInDays(now());
    }
}
