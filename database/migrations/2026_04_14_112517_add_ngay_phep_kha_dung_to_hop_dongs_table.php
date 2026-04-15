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
            $table->decimal('NgayPhepKhaDung', 3, 1)->nullable()->after('NgayPhepNam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hop_dongs', function (Blueprint $table) {
            $table->dropColumn('NgayPhepKhaDung');
        });
    }
};
