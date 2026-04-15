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
            $table->string('ten_phu_luc')->nullable()->after('HopDongId');
            $table->date('ngay_ky')->nullable()->after('ten_phu_luc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phu_luc_hop_dongs', function (Blueprint $table) {
            $table->dropColumn(['ten_phu_luc', 'ngay_ky']);
        });
    }
};
