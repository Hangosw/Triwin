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
        Schema::create('nhan_vien_hop_dong_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('NhanVienId');
            $table->string('FileName');
            $table->string('FilePath');
            $table->unsignedBigInteger('FileSize')->nullable();
            $table->timestamps();

            $table->foreign('NhanVienId')->references('id')->on('nhan_viens')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhan_vien_hop_dong_files');
    }
};
