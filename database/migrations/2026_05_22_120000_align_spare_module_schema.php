<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->alignSpareCategories();
        $this->alignSpareSaleTypes();
        $this->alignSpareParts();
        $this->alignSpareSales();
    }

    public function down(): void
    {
        // Forward-only alignment migration for legacy spare schema.
    }

    private function alignSpareCategories(): void
    {
        if (! Schema::hasTable('spare_categories')) {
            return;
        }

        Schema::table('spare_categories', function (Blueprint $table): void {
            if (! Schema::hasColumn('spare_categories', 'ulid')) {
                $table->ulid('ulid')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('spare_categories', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (! Schema::hasColumn('spare_categories', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('spare_categories')
            ->whereNull('ulid')
            ->get(['id'])
            ->each(function (object $row): void {
                DB::table('spare_categories')->where('id', $row->id)->update(['ulid' => (string) str()->ulid()]);
            });

        DB::table('spare_categories')
            ->whereNull('created_at')
            ->update(['created_at' => now(), 'updated_at' => now()]);
    }

    private function alignSpareSaleTypes(): void
    {
        if (! Schema::hasTable('spare_sale_types')) {
            return;
        }

        Schema::table('spare_sale_types', function (Blueprint $table): void {
            if (! Schema::hasColumn('spare_sale_types', 'ulid')) {
                $table->ulid('ulid')->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('spare_sale_types', 'description')) {
                $table->string('description', 255)->nullable()->after('name');
            }

            if (! Schema::hasColumn('spare_sale_types', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }

            if (! Schema::hasColumn('spare_sale_types', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        DB::table('spare_sale_types')
            ->whereNull('ulid')
            ->get(['id'])
            ->each(function (object $row): void {
                DB::table('spare_sale_types')->where('id', $row->id)->update(['ulid' => (string) str()->ulid()]);
            });

        DB::table('spare_sale_types')
            ->whereNull('created_at')
            ->update(['created_at' => now(), 'updated_at' => now()]);
    }

    private function alignSpareParts(): void
    {
        if (! Schema::hasTable('spare_parts')) {
            return;
        }

        Schema::table('spare_parts', function (Blueprint $table): void {
            if (! Schema::hasColumn('spare_parts', 'name')) {
                $table->string('name', 255)->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('spare_parts', 'source_memo_number')) {
                $table->string('source_memo_number', 100)->nullable()->after('condition');
            }

            if (! Schema::hasColumn('spare_parts', 'source_truck_id')) {
                $table->foreignId('source_truck_id')->nullable()->after('source_memo_number')->constrained('trucks')->nullOnDelete();
            }

            if (! Schema::hasColumn('spare_parts', 'quantity')) {
                $table->integer('quantity')->default(0)->after('source_truck_id');
            }

            $table->index('category_id', 'spare_parts_category_id_idx_v2');
            $table->index('condition', 'spare_parts_condition_idx_v2');
        });

        if (Schema::hasColumn('spare_parts', 'part_name')) {
            DB::statement('UPDATE spare_parts SET name = COALESCE(name, part_name)');
        }

        if (Schema::hasColumn('spare_parts', 'memo_number')) {
            DB::statement('UPDATE spare_parts SET source_memo_number = COALESCE(source_memo_number, memo_number)');
        }

        if (Schema::hasColumn('spare_parts', 'sourced_from_truck_id')) {
            DB::statement('UPDATE spare_parts SET source_truck_id = COALESCE(source_truck_id, sourced_from_truck_id)');
        }

        if (Schema::hasColumn('spare_parts', 'quantity_in_stock')) {
            DB::statement('UPDATE spare_parts SET quantity = COALESCE(quantity, quantity_in_stock)');
        }
    }

    private function alignSpareSales(): void
    {
        if (! Schema::hasTable('spare_sales')) {
            return;
        }

        Schema::table('spare_sales', function (Blueprint $table): void {
            if (Schema::hasColumn('spare_sales', 'spare_part_id')) {
                $table->dropForeign(['spare_part_id']);
                $table->unsignedBigInteger('spare_part_id')->nullable()->change();
            }

            if (! Schema::hasColumn('spare_sales', 'quantity')) {
                $table->integer('quantity')->nullable()->after('buyer_name');
            }

            if (! Schema::hasColumn('spare_sales', 'sold_at')) {
                $table->date('sold_at')->nullable()->after('note');
            }

            if (! Schema::hasColumn('spare_sales', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('sold_at')->constrained('users')->restrictOnDelete();
            }

            if (! Schema::hasColumn('spare_sales', 'deleted_at')) {
                $table->softDeletes();
            }

            if (Schema::hasColumn('spare_sales', 'spare_part_id')) {
                $table->foreign('spare_part_id')->references('id')->on('spare_parts')->nullOnDelete();
            }

            $table->index('sold_at', 'spare_sales_sold_at_idx_v2');
            $table->index('sale_type_id', 'spare_sales_sale_type_idx_v2');
            $table->index('spare_part_id', 'spare_sales_spare_part_idx_v2');
        });

        if (Schema::hasColumn('spare_sales', 'quantity_sold')) {
            DB::statement('UPDATE spare_sales SET quantity = COALESCE(quantity, quantity_sold)');
        }

        if (Schema::hasColumn('spare_sales', 'sale_date')) {
            DB::statement('UPDATE spare_sales SET sold_at = COALESCE(sold_at, sale_date)');
        }

        if (Schema::hasColumn('spare_sales', 'sold_by')) {
            DB::statement('UPDATE spare_sales SET created_by = COALESCE(created_by, sold_by)');
        }
    }
};
