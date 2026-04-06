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
        Schema::table('dang_ky_nghi_pheps', function (Blueprint $table) {
            $table->json('ChiTietBuoi')->nullable()->after('DenBuoi')->comment('Chi tiết các buổi nghỉ trong khoảng thời gian {date: [sang, chieu]}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dang_ky_nghi_pheps', function (Blueprint $table) {
            $table->dropColumn('ChiTietBuoi');
        });
    }
};
