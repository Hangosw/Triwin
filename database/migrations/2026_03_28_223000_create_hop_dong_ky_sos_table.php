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
        Schema::create('hop_dong_ky_sos', function (Blueprint $table) {
            $table->id();
            $table->morphs('signable'); // signable_id, signable_type
            $table->unsignedBigInteger('nhan_vien_id')->nullable();
            $table->unsignedBigInteger('nguoi_dai_dien_id')->nullable();
            
            $table->string('chu_ky_nhan_vien')->nullable();
            $table->dateTime('ngay_ky_nhan_vien')->nullable();
            
            $table->string('chu_ky_dai_dien')->nullable();
            $table->dateTime('ngay_ky_dai_dien')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hop_dong_ky_sos');
    }
};
