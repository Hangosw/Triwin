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
            $table->string('Loai')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hop_dongs', function (Blueprint $table) {
            // Reverting to common enum values if needed, 
            // but usually leaving it as string is safer.
            $table->enum('Loai', ['chinh_thuc', 'thu_viec', 'khoan_viec'])->change();
        });
    }
};
