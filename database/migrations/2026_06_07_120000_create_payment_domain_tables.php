<?php

declare(strict_types=1);

use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            if (! Schema::hasColumn('payments', 'payable_type')) {
                $table->nullableMorphs('payable');
            }

            if (! Schema::hasColumn('payments', 'gateway')) {
                $table->string('gateway', 60)->nullable()->after('payment_method_id');
            }

            if (! Schema::hasColumn('payments', 'status')) {
                $table->string('status', 40)->default('initiated')->after('gateway');
            }

            if (! Schema::hasColumn('payments', 'provider_reference')) {
                $table->string('provider_reference', 200)->nullable()->after('transaction_reference');
            }
        });

        DB::table('payments')
            ->whereNull('payable_type')
            ->whereNotNull('trip_id')
            ->update([
                'payable_type' => Trip::class,
                'payable_id' => DB::raw('trip_id'),
            ]);

        DB::table('payments')
            ->whereNull('gateway')
            ->update(['gateway' => 'offline']);

        DB::table('payments')
            ->whereNull('status')
            ->update(['status' => 'succeeded']);

        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('gateway', 60);
            $table->string('gateway_transaction_id', 191)->nullable();
            $table->string('provider_reference', 191)->nullable();
            $table->string('status', 40);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('BDT');
            $table->json('raw_response')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'status']);
            $table->index(['gateway', 'gateway_transaction_id']);
        });

        Schema::create('payment_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('gateway', 60);
            $table->unsignedInteger('attempt_no')->default(1);
            $table->string('status', 40);
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'attempt_no']);
            $table->index(['gateway', 'status']);
        });

        Schema::create('payment_webhooks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('gateway', 60);
            $table->string('event_type', 120);
            $table->json('payload');
            $table->string('signature', 191)->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'event_type']);
        });

        Schema::create('payment_audits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('event', 100);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['payment_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_audits');
        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('payment_attempts');
        Schema::dropIfExists('payment_transactions');

        Schema::table('payments', function (Blueprint $table): void {
            $columns = [];

            if (Schema::hasColumn('payments', 'payable_type')) {
                $columns[] = 'payable_type';
            }

            if (Schema::hasColumn('payments', 'payable_id')) {
                $columns[] = 'payable_id';
            }

            if (Schema::hasColumn('payments', 'gateway')) {
                $columns[] = 'gateway';
            }

            if (Schema::hasColumn('payments', 'status')) {
                $columns[] = 'status';
            }

            if (Schema::hasColumn('payments', 'provider_reference')) {
                $columns[] = 'provider_reference';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
