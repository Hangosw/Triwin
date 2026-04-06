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
        Schema::table('hop_dongs', function (Blueprint $table) {
            $table->decimal('phu_cap_bhxh', 15, 2)->default(0)->after('LuongCoBan');
            $table->decimal('phu_cap_ngoai_bhxh', 15, 2)->default(0)->after('phu_cap_bhxh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hop_dongs', function (Blueprint $table) {
            $table->dropColumn(['phu_cap_bhxh', 'phu_cap_ngoai_bhxh']);
        });
    }
};
