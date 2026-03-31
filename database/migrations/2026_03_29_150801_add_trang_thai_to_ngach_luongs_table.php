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
        Schema::table('ngach_luongs', function (Blueprint $table) {
            $table->tinyInteger('TrangThai')->default(1)->after('Nhom')->comment('1: Hoạt động, 0: Bị khóa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngach_luongs', function (Blueprint $table) {
            $table->dropColumn('TrangThai');
        });
    }
};
