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
        Schema::create('request_queue_projections', function (Blueprint $table) {
            $table->string('uuid', 36)->primary();
            $table->string('plant_uuid', 36)->nullable()->comment('Für Updates, null für neue Pflanzen');

            // Request Type & Data
            $table->string('request_type')->comment('new_plant, update_contribution');
            $table->json('proposed_data')->comment('Vorgeschlagene Daten');
            $table->text('reason')->comment('Begründung des Users');

            // Requester Info
            $table->string('requested_by')->comment('Username/Email des Requesters');
            $table->timestamp('requested_at');

            // Status & Admin Response
            $table->string('status')->default('pending')->comment('pending, approved, rejected');
            $table->text('admin_comment')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('plant_uuid')->references('uuid')->on('plants')->onDelete('set null');
            $table->index(['status', 'requested_at']);
            $table->index('requested_by');
            $table->index('plant_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_queue_projections');
    }
};
