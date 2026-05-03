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
        Schema::create('client_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description', 200)->nullable();
        });

        Schema::create('truck_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
        });

        Schema::create('trip_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description', 200)->nullable();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('description', 200)->nullable();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description', 200)->nullable();
        });

        Schema::create('spare_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
            $table->string('description', 200)->nullable();
        });

        Schema::create('spare_sale_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spare_sale_types');
        Schema::dropIfExists('spare_categories');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('trip_statuses');
        Schema::dropIfExists('truck_statuses');
        Schema::dropIfExists('client_categories');
    }
};
