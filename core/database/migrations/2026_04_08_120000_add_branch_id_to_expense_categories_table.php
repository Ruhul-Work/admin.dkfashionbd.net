<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            if (! Schema::hasColumn('expense_categories', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            }

            if (! $this->hasBranchIndex()) {
                $table->index('branch_id', 'expense_categories_branch_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            if (Schema::hasColumn('expense_categories', 'branch_id')) {
                if ($this->hasBranchIndex()) {
                    $table->dropIndex('expense_categories_branch_id_index');
                }
                $table->dropColumn('branch_id');
            }
        });
    }

    protected function hasBranchIndex(): bool
    {
        $indexes = DB::select("SHOW INDEX FROM expense_categories WHERE Key_name = 'expense_categories_branch_id_index'");

        return ! empty($indexes);
    }
};
