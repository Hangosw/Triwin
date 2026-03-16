<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Drop foreign key
        try {
            Schema::table('lich_lam_viecs', function (Blueprint $table) {
                $table->dropForeign(['NhanVienId']);
            });
        } catch (\Exception $e) {}

        // Step 2: Modify column and add foreign key
        try {
            Schema::table('lich_lam_viecs', function (Blueprint $table) {
                $table->unsignedBigInteger('NhanVienId')->nullable()->change();
                $table->foreign('NhanVienId')->references('id')->on('nhan_viens')->nullOnDelete();
            });
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lich_lam_viecs', function (Blueprint $table) {
            $table->dropForeign(['NhanVienId']);
        });

        Schema::table('lich_lam_viecs', function (Blueprint $table) {
            $table->unsignedBigInteger('NhanVienId')->nullable(false)->change();
            $table->foreign('NhanVienId')->references('id')->on('nhan_viens')->cascadeOnDelete();
        });
    }
};
