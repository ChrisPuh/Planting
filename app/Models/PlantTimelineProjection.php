<?php

namespace App\Models;

use Database\Factories\PlantTimelineProjectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantTimelineProjection extends Model
{
    /** @use HasFactory<PlantTimelineProjectionFactory> */
    use HasFactory;

    protected $fillable = [
        'plant_uuid',
        'event_type',
        'performed_by',
        'performed_at',
        'event_details',
        'display_text',
        'sequence_number',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'event_details' => 'array',
    ];

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class, 'plant_uuid', 'uuid');
    }

    // Scopes
    public function scopeByType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeByUser($query, string $username)
    {
        return $query->where('performed_by', $username);
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('sequence_number');
    }

    public function scopeReverseChronological($query)
    {
        return $query->orderByDesc('sequence_number');
    }

    // Accessors
    public function getEventTypeDisplayAttribute(): string
    {
        return match ($this->event_type) {
            'requested' => 'Beantragt',
            'created' => 'Erstellt',
            'updated' => 'Aktualisiert',
            'update_requested' => 'Änderung beantragt',
            'deleted' => 'Gelöscht',
            'restored' => 'Wiederhergestellt',
            default => ucfirst($this->event_type)
        };
    }

    public function getChangedFieldsAttribute(): ?array
    {
        return $this->event_details['changed_fields'] ?? null;
    }

    public function getRequestedFieldsAttribute(): ?array
    {
        return $this->event_details['requested_fields'] ?? null;
    }
}
