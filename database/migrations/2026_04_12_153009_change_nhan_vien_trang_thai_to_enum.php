<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the new enum column temporarily or use raw SQL to change the column
        // Since we are changing type from tinyint to enum, we need to handle data migration
        
        // 1. Change type to string first to avoid issues with enum mapping initially if needed, 
        // but raw SQL is often better for this in MySQL.
        
        DB::statement("ALTER TABLE nhan_viens MODIFY COLUMN TrangThai VARCHAR(20)");
        
        // 2. Map old numeric values to new string values
        DB::table('nhan_viens')->where('TrangThai', '1')->update(['TrangThai' => 'dang_lam']);
        DB::table('nhan_viens')->where('TrangThai', '0')->update(['TrangThai' => 'nghi_viec']);
        DB::table('nhan_viens')->where('TrangThai', '2')->update(['TrangThai' => 'nghi_thai_san']);
        
        // 3. Ensure any nulls or other values are set to default
        DB::table('nhan_viens')->whereNotIn('TrangThai', ['dang_lam', 'nghi_viec', 'nghi_thai_san'])->update(['TrangThai' => 'dang_lam']);

        // 4. Change to ENUM type
        DB::statement("ALTER TABLE nhan_viens MODIFY COLUMN TrangThai ENUM('dang_lam', 'nghi_viec', 'nghi_thai_san') DEFAULT 'dang_lam'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE nhan_viens MODIFY COLUMN TrangThai VARCHAR(20)");
        
        DB::table('nhan_viens')->where('TrangThai', 'dang_lam')->update(['TrangThai' => '1']);
        DB::table('nhan_viens')->where('TrangThai', 'nghi_viec')->update(['TrangThai' => '0']);
        DB::table('nhan_viens')->where('TrangThai', 'nghi_thai_san')->update(['TrangThai' => '2']);
        
        DB::statement("ALTER TABLE nhan_viens MODIFY COLUMN TrangThai TINYINT DEFAULT 1");
    }
};
