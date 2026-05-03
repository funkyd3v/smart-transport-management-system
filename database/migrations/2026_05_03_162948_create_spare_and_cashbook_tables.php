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
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->unsignedInteger('category_id');
            $table->string('part_name', 200);
            $table->enum('condition', ['new', 'old']);
            $table->foreignId('sourced_from_truck_id')->nullable()->constrained('trucks')->nullOnDelete();
            $table->string('memo_number', 100)->nullable();
            $table->integer('quantity_in_stock')->default(0);
            $table->decimal('purchase_price', 15, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('spare_categories')->restrictOnDelete();
            $table->index('part_name');
            $table->index('condition');
            $table->index('quantity_in_stock');
            $table->index('deleted_at');
        });

        Schema::create('spare_sales', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->restrictOnDelete();
            $table->unsignedInteger('sale_type_id');
            $table->foreignId('sold_by')->constrained('users')->restrictOnDelete();
            $table->string('buyer_name', 150);
            $table->string('buyer_contact', 20)->nullable();
            $table->integer('quantity_sold');
            $table->decimal('purchase_price_snapshot', 15, 2);
            $table->decimal('sale_price', 15, 2);
            $table->decimal('profit', 15, 2);
            $table->date('sale_date');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('sale_type_id')->references('id')->on('spare_sale_types')->restrictOnDelete();
            $table->index('sale_date');
            $table->index('profit');
        });

        Schema::create('daily_cashbook', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->date('entry_date')->unique();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('total_due_collected', 15, 2)->default(0);
            $table->decimal('spare_income', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->boolean('is_finalized')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('is_finalized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_cashbook');
        Schema::dropIfExists('spare_sales');
        Schema::dropIfExists('spare_parts');
    }
};
