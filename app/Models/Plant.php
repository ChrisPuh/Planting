<?php

namespace App\Models;

use Database\Factories\PlantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $uuid
 */
class Plant extends Model
{
    /** @use HasFactory<PlantFactory> */
    use HasFactory;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'name',
        'type',
        'category',
        'latin_name',
        'description',
        'image_url',
        'is_deleted',
        'was_community_requested',
        'created_by',
        'last_updated_by',
        'last_event_at',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'was_community_requested' => 'boolean',
        'last_event_at' => 'datetime',
    ];

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(PlantTimelineProjection::class, 'plant_uuid', 'uuid')
            ->orderBy('sequence_number');
    }

    // Scopes
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeCommunityRequested($query)
    {
        return $query->where('was_community_requested', true);
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return ! $this->is_deleted;
    }

    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'gemuese' => 'Gemüse',
            'blume' => 'Blume',
            'gras' => 'Gras',
            'strauch' => 'Strauch',
            'baum' => 'Baum',
            default => ucfirst($this->type)
        };
    }
}
