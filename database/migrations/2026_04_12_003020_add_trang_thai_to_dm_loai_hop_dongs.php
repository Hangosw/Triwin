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
        Schema::table('dm_loai_hop_dongs', function (Blueprint $table) {
            $table->enum('TrangThai', ['mo', 'khoa'])->default('mo')->after('CoDongBaoHiem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dm_loai_hop_dongs', function (Blueprint $table) {
            $table->dropColumn('TrangThai');
        });
    }
};
