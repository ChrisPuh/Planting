<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_queue_projections', function (Blueprint $table) {
            $table->uuid()->primary()->comment('Request aggregate UUID');

            // Plant reference (nullable for new plant requests)
            $table->string('plant_uuid', 36)->nullable()->comment('Target plant UUID (null for new plants)');

            // Request type and data
            $table->string('request_type')->comment('Type: new_plant, update_contribution');
            $table->json('proposed_data')->comment('Proposed plant data or changes');
            $table->text('reason')->comment('Reason for the request');

            // Request metadata
            $table->string('requested_by')->comment('Username who made the request');
            $table->timestamp('requested_at')->comment('When request was submitted');
            // Status and review
            $table->string('status')->default('pending')->comment('Status: pending, approved, rejected');
            $table->text('admin_comment')->nullable()->comment('Admin review comment');
            $table->string('reviewed_by')->nullable()->comment('Admin who reviewed the request');
            $table->timestamp('reviewed_at')->nullable()->comment('When request was reviewed');

            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index('request_type');
            $table->index('requested_by');
            $table->index('requested_at');
            $table->index(['plant_uuid', 'status']);
            $table->index(['status', 'requested_at']);

            // Note: No foreign key constraint in event sourcing - events may arrive out of order
            // Validation happens at application level
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_queue_projections');
    }
};
