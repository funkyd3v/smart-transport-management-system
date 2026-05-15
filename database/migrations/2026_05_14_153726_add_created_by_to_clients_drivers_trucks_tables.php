<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('ulid')->constrained('users')->nullOnDelete();
            $table->index('created_by');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('ulid')->constrained('users')->nullOnDelete();
            $table->index('created_by');
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('ulid')->constrained('users')->nullOnDelete();
            $table->index('created_by');
        });

        DB::table('trucks')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(200, function ($trucks): void {
                foreach ($trucks as $truck) {
                    $createdBy = DB::table('trips')
                        ->where('truck_id', (int) $truck->id)
                        ->orderBy('created_at')
                        ->value('created_by');

                    if ($createdBy === null) {
                        continue;
                    }

                    DB::table('trucks')
                        ->where('id', (int) $truck->id)
                        ->whereNull('created_by')
                        ->update(['created_by' => (int) $createdBy]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropConstrainedForeignId('created_by');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropConstrainedForeignId('created_by');
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropConstrainedForeignId('created_by');
        });
    }
};
