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
        Schema::table('hop_dongs', function (Blueprint $table) {
            $table->string('TenNhanVien')->nullable()->after('NhanVienId')->comment('Lưu tên nhân viên phòng phi trường hợp nhân viên bị xóa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hop_dongs', function (Blueprint $table) {
            $table->dropColumn('TenNhanVien');
        });
    }
};
