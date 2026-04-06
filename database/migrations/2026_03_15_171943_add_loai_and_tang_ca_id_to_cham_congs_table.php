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
        error_log('Migrating cham_congs table...');
        Schema::table('cham_congs', function (Blueprint $table) {
            if (!Schema::hasColumn('cham_congs', 'Loai')) {
                $table->tinyInteger('Loai')->default(0)->after('NhanVienId')->comment('0: Hành chính, 1: Tăng ca');
            }
            if (!Schema::hasColumn('cham_congs', 'TangCaId')) {
                $table->unsignedBigInteger('TangCaId')->nullable()->after('Loai');
            }
            
            // $table->foreign('TangCaId')->references('id')->on('tang_cas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cham_congs', function (Blueprint $table) {
            $table->dropForeign(['TangCaId']);
            $table->dropColumn(['Loai', 'TangCaId']);
        });
    }
};
