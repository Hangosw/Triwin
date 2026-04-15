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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('chi_tiet_phu_luc');
        Schema::dropIfExists('phu_luc_hop_dongs');
        Schema::enableForeignKeyConstraints();

        Schema::create('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('HopDongGocId');
            $table->unsignedBigInteger('HopDongPLId');
            $table->string('ten_phu_luc')->nullable();
            $table->date('ngay_ky')->nullable();
            $table->integer('TrangThai')->default(1);
            $table->timestamps();

            $table->index('HopDongGocId');
            $table->index('HopDongPLId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->dropForeign(['HopDongGocId']);
            $table->dropForeign(['HopDongPLId']);
            
            $table->dropColumn(['HopDongGocId', 'HopDongPLId']);
            
            // Restore legacy columns (basic reconstruction)
            $table->unsignedBigInteger('HopDongId')->nullable();
            $table->unsignedBigInteger('dm_pl_hop_dong_id')->nullable();
            $table->decimal('so_tien', 15, 2)->nullable();
        });
    }
};
