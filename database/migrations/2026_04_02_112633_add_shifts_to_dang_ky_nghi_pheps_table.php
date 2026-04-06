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
            $table->string('TuBuoi')->default('ca_ngay')->after('TuNgay')->comment('sang, chieu, ca_ngay');
            $table->string('DenBuoi')->default('ca_ngay')->after('DenNgay')->comment('sang, chieu, ca_ngay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dang_ky_nghi_pheps', function (Blueprint $table) {
            $table->dropColumn(['TuBuoi', 'DenBuoi']);
        });
    }
};
