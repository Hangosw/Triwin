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
        Schema::table('nhan_vien_than_nhans', function (Blueprint $row) {
            $row->integer('TrangThai')->default(0)->after('TepDinhKem')->comment('0: Chưa duyệt, 1: Đã duyệt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhan_vien_than_nhans', function (Blueprint $row) {
            $row->dropColumn('TrangThai');
        });
    }
};
