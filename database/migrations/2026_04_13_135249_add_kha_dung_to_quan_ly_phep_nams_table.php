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
        Schema::table('quan_ly_phep_nams', function (Blueprint $table) {
            $table->decimal('KhaDung', 8, 1)->default(0)->after('DaNghi')->comment('Số phép tích lũy có thể sử dụng hiện tại');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quan_ly_phep_nams', function (Blueprint $table) {
            $table->dropColumn('KhaDung');
        });
    }
};
