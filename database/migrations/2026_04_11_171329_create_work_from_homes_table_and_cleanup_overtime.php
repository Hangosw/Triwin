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
        // 1. Drop old tables
        Schema::dropIfExists('tang_cas');
        Schema::dropIfExists('dm_tang_cas');

        // 2. Create new WFH table
        Schema::create('work_from_homes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('NhanVienId');
            $table->bigInteger('NguoiDuyetId')->nullable();
            
            $table->date('NgayBatDau');
            $table->date('NgayKetThuc');
            $table->decimal('Ngay', 8, 2)->default(0)->comment('Số ngày WFH = KetThuc - BatDau + 1');
            
            $table->string('LyDo')->nullable();
            $table->text('GhiChu')->nullable();
            $table->enum('TrangThai', ['dang_cho', 'da_duyet', 'tu_choi'])->default('dang_cho');
            $table->date('NgayDuyet')->nullable();
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('NhanVienId')->references('id')->on('nhan_viens')->onDelete('cascade');
            $table->foreign('NguoiDuyetId')->references('id')->on('nhan_viens')->onDelete('set null');
        });

        // 3. Clean up cham_congs table if needed
        if (Schema::hasColumn('cham_congs', 'TangCaId')) {
            Schema::table('cham_congs', function (Blueprint $table) {
                if (Schema::hasColumn('cham_congs', 'TangCaId')) {
                    $table->dropColumn('TangCaId');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_from_homes_table_and_cleanup_overtime');
    }
};
