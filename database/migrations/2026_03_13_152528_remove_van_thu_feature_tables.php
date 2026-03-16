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
        Schema::dropIfExists('van_thus');
        Schema::dropIfExists('dm_van_bans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse table drops without knowing the exact schema
    }
};
