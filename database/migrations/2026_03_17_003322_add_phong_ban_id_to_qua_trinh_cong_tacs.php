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
        Schema::table('qua_trinh_cong_tacs', function (Blueprint $table) {
            if (!Schema::hasColumn('qua_trinh_cong_tacs', 'PhongBanId')) {
                $table->unsignedBigInteger('PhongBanId')->nullable()->after('NhanVienId');
                $table->foreign('PhongBanId')->references('id')->on('dm_phong_bans')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qua_trinh_cong_tacs', function (Blueprint $table) {
            if (Schema::hasColumn('qua_trinh_cong_tacs', 'PhongBanId')) {
                $table->dropForeign(['PhongBanId']);
                $table->dropColumn('PhongBanId');
            }
            if (!Schema::hasColumn('qua_trinh_cong_tacs', 'DonViId')) {
                $table->unsignedBigInteger('DonViId')->nullable()->after('NhanVienId');
            }
        });
    }
};
