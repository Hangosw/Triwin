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
        Schema::table('tt_nhan_vien_cong_viecs', function (Blueprint $table) {
            if (Schema::hasColumn('tt_nhan_vien_cong_viecs', 'LoaiNhanVien')) {
                $table->dropColumn('LoaiNhanVien');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tt_nhan_vien_cong_viecs', function (Blueprint $table) {
            $table->integer('LoaiNhanVien')->default(1)->comment('0: công nhân, 1: văn phòng');
        });
    }
};
