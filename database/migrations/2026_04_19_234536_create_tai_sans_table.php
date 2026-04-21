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
        Schema::create('tai_sans', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('ten_tai_san');
            $blueprint->string('hinh_anh')->nullable();
            $blueprint->date('ngay_nhap_kho');
            $blueprint->bigInteger('nhan_vien_id')->unsigned()->nullable(); // Match nhan_viens id if it is unsigned
            $blueprint->date('ngay_muon')->nullable();
            $blueprint->string('trang_thai')->default('SanSang'); // SanSang, DangMuon, BaoTri, Hong
            $blueprint->text('ghi_chu')->nullable();
            $blueprint->timestamps();

            // Only add foreign key if we are sure about the type. 
            // For now, let's use the standard Laravel way but ensure types match.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tai_sans');
    }
};
