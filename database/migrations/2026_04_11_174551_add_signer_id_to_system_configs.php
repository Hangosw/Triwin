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
        DB::table('system_configs')->updateOrInsert(
            ['key' => 'signer_id'],
            [
                'value' => null,
                'group' => 'general',
                'description' => 'ID của nhân viên đại diện ký tên trên các văn bản hệ thống'
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_configs')->where('key', 'signer_id')->delete();
    }
};
