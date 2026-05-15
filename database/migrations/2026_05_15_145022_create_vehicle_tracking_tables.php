<?php

declare(strict_types=1);

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
        Schema::create('current_vehicle_locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('trip_id')->unique()->constrained('trips')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('speed_kph', 8, 2)->nullable();
            $table->unsignedSmallInteger('heading_degrees')->nullable();
            $table->timestamp('captured_at');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('last_history_at')->nullable();
            $table->timestamp('last_broadcast_at')->nullable();
            $table->boolean('is_online')->default(true);
            $table->timestamp('tracking_stopped_at')->nullable();
            $table->string('source', 32)->default('driver_device');
            $table->timestamps();

            $table->index('driver_id');
            $table->index('truck_id');
            $table->index('captured_at');
            $table->index(['is_online', 'captured_at']);
        });

        Schema::create('vehicle_location_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('speed_kph', 8, 2)->nullable();
            $table->unsignedSmallInteger('heading_degrees')->nullable();
            $table->timestamp('captured_at');
            $table->timestamp('received_at')->useCurrent();
            $table->string('source', 32)->default('driver_device');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['trip_id', 'captured_at']);
            $table->index(['driver_id', 'captured_at']);
            $table->index(['truck_id', 'captured_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_location_histories');
        Schema::dropIfExists('current_vehicle_locations');
    }
};
