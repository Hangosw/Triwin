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
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->text('anh_cccd')->nullable()->after('SoCCCD');
            $table->text('anh_bhxh')->nullable()->after('BHXH');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropColumn(['anh_cccd', 'anh_bhxh']);
        });
    }
};
