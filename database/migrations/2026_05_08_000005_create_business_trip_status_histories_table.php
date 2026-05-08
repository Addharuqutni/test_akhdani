<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_trip_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_trip_request_id')->constrained('business_trip_requests')->cascadeOnDelete();
            $table->string('from_status', 32)->nullable();
            $table->string('to_status', 32);
            $table->foreignId('changed_by')->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['business_trip_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_trip_status_histories');
    }
};
