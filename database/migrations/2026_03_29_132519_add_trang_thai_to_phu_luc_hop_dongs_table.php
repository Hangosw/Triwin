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
        Schema::table('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->tinyInteger('TrangThai')->default(1)->after('ngay_ky')->comment('1: Đang hiệu lực, 0: Hết hiệu lực');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->dropColumn('TrangThai');
        });
    }
};
