<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('reference_no', 40)->unique();
            $table->string('reference_type')->nullable();
            $table->string('reference_id', 100)->nullable();
            $table->string('channel', 40);
            $table->string('provider', 60)->nullable();
            $table->string('recipient', 191);
            $table->string('subject', 191)->nullable();
            $table->text('body');
            $table->string('status', 40)->index();
            $table->string('provider_message_id', 191)->nullable();
            $table->string('template_key', 120)->nullable();
            $table->json('template_data')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable()->index();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['channel', 'provider']);
            $table->index(['recipient', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('communication_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->unsignedInteger('attempt_no');
            $table->string('provider', 60)->nullable();
            $table->string('status', 40);
            $table->string('provider_message_id', 191)->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();

            $table->unique(['communication_id', 'attempt_no']);
            $table->index(['provider', 'status']);
        });

        Schema::create('communication_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('communication_id')->constrained('communications')->cascadeOnDelete();
            $table->string('event', 100);
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('logged_at')->nullable();
            $table->timestamps();

            $table->index(['communication_id', 'event']);
        });

        Schema::create('communication_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 120);
            $table->string('channel', 40);
            $table->string('subject_template', 191)->nullable();
            $table->text('body_template');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['key', 'channel']);
            $table->index(['channel', 'is_active']);
        });

        Schema::create('otp_codes', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->string('reference_no', 40)->unique();
            $table->string('purpose', 80)->index();
            $table->string('recipient', 191)->index();
            $table->string('channel', 40)->default('sms');
            $table->string('provider', 60)->nullable();
            $table->string('code_hash', 255);
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('max_attempts')->default(5);
            $table->timestamp('expires_at')->index();
            $table->timestamp('verified_at')->nullable()->index();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_type')->nullable();
            $table->string('reference_id', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['purpose', 'recipient', 'verified_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('communication_templates');
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('communication_attempts');
        Schema::dropIfExists('communications');
    }
};
