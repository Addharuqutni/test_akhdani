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
        Schema::table('business_trip_requests', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('employee_id', 'idx_employee_id');
            $table->index('status', 'idx_status');
            $table->index('submitted_at', 'idx_submitted_at');
            
            // Composite index for approval queue queries (status + submitted_at)
            $table->index(['status', 'submitted_at'], 'idx_status_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_trip_requests', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex('idx_status_submitted_at');
            $table->dropIndex('idx_submitted_at');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_employee_id');
        });
    }
};
