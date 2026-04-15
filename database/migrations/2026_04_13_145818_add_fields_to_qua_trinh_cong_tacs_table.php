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
            $table->string('DiaDiem')->nullable()->after('ChucVuId');
            $table->text('GhiChu')->after('DiaDiem')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qua_trinh_cong_tacs', function (Blueprint $table) {
            $table->dropColumn(['DiaDiem', 'GhiChu']);
        });
    }
};
