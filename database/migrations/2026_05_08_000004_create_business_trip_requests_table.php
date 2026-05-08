<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_trip_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('employee_id')->constrained('users');
            $table->text('purpose');
            $table->date('departure_date');
            $table->date('return_date');
            $table->foreignId('origin_city_id')->constrained('cities');
            $table->foreignId('destination_city_id')->constrained('cities');
            $table->unsignedInteger('duration_days')->default(1);
            $table->decimal('distance_km', 10, 2)->default(0);
            $table->string('allowance_rule_type', 64)->nullable();
            $table->string('allowance_currency', 3)->default('IDR');
            $table->decimal('allowance_per_day', 16, 2)->default(0);
            $table->decimal('allowance_total', 16, 2)->default(0);
            $table->string('status', 32)->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('approval_note')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_trip_requests');
    }
};
