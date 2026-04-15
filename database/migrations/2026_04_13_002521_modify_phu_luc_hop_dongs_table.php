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
        Schema::table('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->unsignedBigInteger('dm_pl_hop_dong_id')->nullable()->after('HopDongId');
            $table->decimal('so_tien', 15, 2)->default(0)->after('dm_pl_hop_dong_id');
            $table->string('ten_phu_luc')->nullable()->change();
            $table->date('ngay_ky')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->dropColumn(['dm_pl_hop_dong_id', 'so_tien']);
        });
    }
};
