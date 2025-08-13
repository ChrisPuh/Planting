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
        Schema::create('plants', function (Blueprint $table) {
            // Primary key using UUID
            $table->uuid('uuid')->primary();

            // Current plant state (projected from events)
            $table->string('name')->comment('Current name of the plant');
            $table->string('type')->comment('Current plant type (gemuese, blume, etc.)');
            $table->string('category')->nullable()->comment('Current category (wurzelgemuese, kraeuter, etc.)');
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
            $table->index('type');
            $table->index('category');
            $table->index(['type', 'category']);
            $table->index('is_deleted');
            $table->index('was_community_requested');
            $table->index('last_event_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};
