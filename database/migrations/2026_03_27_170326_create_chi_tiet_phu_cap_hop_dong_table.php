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
        Schema::create('hop_dong_allowances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hop_dong_id')->index();
            $table->bigInteger('dm_pl_hop_dong_id')->index();
            $table->decimal('so_tien', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hop_dong_allowances');
    }
};
