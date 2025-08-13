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
        Schema::create('plant_timeline_projections', function (Blueprint $table) {
            $table->id();

            // Plant reference - NO FOREIGN KEY CONSTRAINT in Event Sourcing
            // Events may arrive before the plant exists in the projection
            $table->uuid('plant_uuid')->comment('Plant aggregate UUID - no FK constraint in event sourcing');

            // Event data for timeline display
            $table->string('event_type')->comment('requested, created, updated, update_requested, deleted, restored');
            $table->string('performed_by')->comment('Username who performed this action');
            $table->timestamp('performed_at')->comment('When this event occurred');
            $table->json('event_details')->nullable()->comment('Details like changed_fields, etc.');
            $table->text('display_text')->nullable()->comment('Human-readable text for UI');

            // For ordering in timeline
            $table->unsignedBigInteger('sequence_number')->comment('Order of events for this plant');

            $table->timestamps();

            // Indexes for performance - NO FOREIGN KEYS
            $table->index('plant_uuid');
            $table->index('event_type');
            $table->index(['plant_uuid', 'sequence_number']);
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_timeline_projections');
    }
};
