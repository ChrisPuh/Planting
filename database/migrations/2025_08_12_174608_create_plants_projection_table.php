<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Plants Projection - Read Model für Performance
        Schema::create('plants', function (Blueprint $table) {
            $table->string('uuid', 36)->primary()->comment('Plant aggregate UUID');

            // Current plant state (projected from events)
            $table->string('name')->comment('Current name of the plant');
            $table->string('type')->comment('Current plant type (Gemüse, Blume, etc.)');
            $table->string('category')->nullable()->comment('Current category (Wurzelgemüse, Kräuter, etc.)');
            $table->string('latin_name')->nullable()->comment('Current scientific name');
            $table->text('description')->nullable()->comment('Current description');
            $table->string('image_url')->nullable()->comment('Current image URL');

            // Status flags (projected from events)
            $table->boolean('is_deleted')->default(false)->comment('Currently deleted status');
            $table->boolean('was_community_requested')->default(false)->comment('Was originally requested by community');

            // Timeline summary (for quick access)
            $table->string('created_by')->nullable()->comment('Who created this plant');
            $table->string('last_updated_by')->nullable()->comment('Who last updated this plant');
            $table->timestamp('last_event_at')->nullable()->comment('When last event occurred');

            // Laravel timestamps for the projection itself
            $table->timestamps();

            // Indexes for performance
            $table->index('type', 'idx_plants_type');
            $table->index('category', 'idx_plants_category');
            $table->index(['type', 'category'], 'idx_plants_type_category');
            $table->index('is_deleted', 'idx_plants_is_deleted');
            $table->index('was_community_requested', 'idx_plants_community_requested');
            $table->index('last_event_at', 'idx_plants_last_event');
        });

        // Plant Timeline Projection - für die schöne UI Timeline
        Schema::create('plant_timeline_projections', function (Blueprint $table) {
            $table->id();
            $table->string('plant_uuid', 36)->comment('Plant aggregate UUID');

            // Event data for timeline display
            $table->string('event_type')->comment('requested, created, updated, update_requested, deleted, restored');
            $table->string('performed_by')->comment('Username who performed this action');
            $table->timestamp('performed_at')->comment('When this event occurred');
            $table->json('event_details')->nullable()->comment('Details like changed_fields, etc.');
            $table->text('display_text')->nullable()->comment('Human-readable text for UI');

            // For ordering in timeline
            $table->unsignedBigInteger('sequence_number')->comment('Order of events for this plant');

            $table->timestamps();

            // Foreign key to plants projection
            $table->foreign('plant_uuid', 'fk_timeline_plant')
                ->references('uuid')
                ->on('plants')
                ->onDelete('cascade');


            // Indexes
            $table->index('plant_uuid', 'idx_timeline_plant');
            $table->index('event_type', 'idx_timeline_event_type');
            $table->index(['plant_uuid', 'sequence_number'], 'idx_timeline_order');
            $table->index('performed_at', 'idx_timeline_performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_timeline_projections');
        Schema::dropIfExists('plants');
    }
};
