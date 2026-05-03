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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('license_number', 50)->unique();
            $table->string('nid_number', 30)->unique();
            $table->enum('driving_type', ['permanent', 'backup']);
            $table->date('joining_date');
            $table->string('image_path', 500)->nullable();
            $table->integer('total_trips')->default(0);
            $table->decimal('total_profit_generated', 15, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->index('driving_type');
            $table->index('is_available');
            $table->index('deleted_at');
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('category_id');
            $table->string('company_name', 150)->nullable();
            $table->string('project_name', 200)->nullable();
            $table->string('agreement_number', 100)->nullable();
            $table->decimal('project_value', 18, 2)->nullable();
            $table->date('project_start_date')->nullable();
            $table->date('project_end_date')->nullable();
            $table->integer('total_trips')->default(0);
            $table->decimal('total_business_amount', 18, 2)->default(0);
            $table->decimal('total_due', 18, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('client_categories')->restrictOnDelete();
            $table->index('company_name');
            $table->index('total_due');
            $table->index('deleted_at');
        });

        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('truck_number', 50)->unique();
            $table->string('model', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->integer('year')->nullable();
            $table->decimal('capacity_tons', 8, 2)->nullable();
            $table->unsignedInteger('status_id');
            $table->foreignId('current_driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->integer('total_trips')->default(0);
            $table->date('last_service_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->foreign('status_id')->references('id')->on('truck_statuses')->restrictOnDelete();
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('drivers');
    }
};
