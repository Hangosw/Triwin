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
        Schema::create('tam_ungs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('NhanVienId');
            $table->unsignedBigInteger('NguoiDuyetId')->nullable();
            $table->decimal('SoTien', 15, 2);
            $table->decimal('HanMuc', 15, 2);
            $table->integer('TrangThai')->default(0)->comment('0: Chờ duyệt, 1: Đã duyệt, 2: Từ chối');
            $table->string('Lydo')->nullable();
            $table->text('GhiChu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tam_ungs');
    }
};
