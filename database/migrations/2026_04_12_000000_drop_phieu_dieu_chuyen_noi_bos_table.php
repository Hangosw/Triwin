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
        Schema::dropIfExists('phieu_dieu_chuyen_noi_bos');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('phieu_dieu_chuyen_noi_bos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('NhanVienId');
            $table->unsignedBigInteger('PhongBanMoiId')->nullable();
            $table->unsignedBigInteger('ChucVuMoiId')->nullable();
            $table->date('NgayDieuChuyen');
            $table->text('LyDo')->nullable();
            $table->tinyInteger('TrangThai')->default(0); // 0: Chờ duyệt, 1: Đã duyệt, 2: Từ chối
            $table->boolean('DaTaoHopDong')->default(false);
            $table->timestamps();

            $table->foreign('NhanVienId')->references('id')->on('nhan_viens')->onDelete('cascade');
        });
    }
};
