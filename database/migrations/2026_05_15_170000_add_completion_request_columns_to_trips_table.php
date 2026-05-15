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
        Schema::table('trips', function (Blueprint $table): void {
            $table->timestamp('completion_requested_at')->nullable()->after('completed_at');
            $table->foreignId('completion_requested_by')->nullable()->after('completion_requested_at')->constrained('users')->nullOnDelete();
            $table->text('completion_requested_note')->nullable()->after('completion_requested_by');

            $table->index('completion_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table): void {
            $table->dropIndex(['completion_requested_at']);
            $table->dropConstrainedForeignId('completion_requested_by');
            $table->dropColumn(['completion_requested_at', 'completion_requested_note']);
        });
    }
};
