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
        Schema::table('luongs', function (Blueprint $table) {
            if (!Schema::hasColumn('luongs', 'KhenThuong')) {
                $table->decimal('KhenThuong', 15, 2)->default(0)->after('PhuCap');
            }
            if (!Schema::hasColumn('luongs', 'KyLuat')) {
                $table->decimal('KyLuat', 15, 2)->default(0)->after('ThueTNCN');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('luongs', function (Blueprint $table) {
            $table->dropColumn(array_filter(['KhenThuong', 'KyLuat'], function($col) {
                return Schema::hasColumn('luongs', $col);
            }));
        });
    }
};
