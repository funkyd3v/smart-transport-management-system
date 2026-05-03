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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('trip_code', 30)->unique();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('status_id');
            $table->string('pickup_point', 300);
            $table->string('delivery_point', 300);
            $table->text('route_description')->nullable();
            $table->text('goods_description')->nullable();
            $table->dateTime('load_date');
            $table->dateTime('expected_delivery_date')->nullable();
            $table->dateTime('actual_delivery_date')->nullable();
            $table->decimal('trip_rate', 15, 2);
            $table->decimal('advance_payment', 15, 2)->default(0);
            $table->decimal('total_income', 15, 2);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->decimal('profit', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('sms_note')->nullable();
            $table->timestamp('invoice_generated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->foreign('status_id')->references('id')->on('trip_statuses')->restrictOnDelete();
            $table->index('load_date');
            $table->index('due_amount');
            $table->index('created_at');
            $table->index('deleted_at');
        });

        Schema::create('trip_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->restrictOnDelete();
            $table->string('item_name', 200);
            $table->string('unit', 50)->nullable();
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->string('measurement_details', 300)->nullable();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('trip_id')->unique()->constrained('trips')->restrictOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('advance_paid', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->string('company_logo_path', 500)->nullable();
            $table->string('authority_signature_path', 500)->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->timestamp('issued_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index('issued_at');
        });

        Schema::create('trip_expenses', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('trip_id')->constrained('trips')->restrictOnDelete();
            $table->unsignedInteger('category_id');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('description', 300)->nullable();
            $table->date('expense_date');
            $table->string('receipt_path', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('category_id')->references('id')->on('expense_categories')->restrictOnDelete();
            $table->index('expense_date');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('trip_id')->constrained('trips')->restrictOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('collected_by')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('payment_method_id');
            $table->decimal('amount', 15, 2);
            $table->string('transaction_reference', 200)->nullable();
            $table->date('payment_date');
            $table->boolean('is_advance')->default(false);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->restrictOnDelete();
            $table->index('payment_date');
            $table->index('is_advance');
        });

        Schema::create('due_records', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('trip_id')->constrained('trips')->restrictOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->decimal('original_due', 15, 2);
            $table->decimal('collected_amount', 15, 2)->default(0);
            $table->decimal('remaining_due', 15, 2);
            $table->date('due_date')->nullable();
            $table->boolean('is_settled')->default(false);
            $table->timestamp('settled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index('is_settled');
            $table->index('due_date');
            $table->index('remaining_due');
        });

        Schema::create('reload_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->restrictOnDelete();
            $table->foreignId('truck_id')->constrained('trucks')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->string('reload_point', 300)->nullable();
            $table->timestamp('reloaded_at');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('reloaded_at');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->enum('type', ['sms', 'whatsapp', 'system']);
            $table->enum('channel', ['trip_start', 'trip_complete', 'invoice', 'due_reminder', 'thank_you']);
            $table->string('recipient_phone', 20)->nullable();
            $table->text('message');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reload_history');
        Schema::dropIfExists('due_records');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('trip_expenses');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('trip_goods');
        Schema::dropIfExists('trips');
    }
};
