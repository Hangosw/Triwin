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
            $table->string('anh_cccd_sau')->nullable()->after('anh_cccd');
        });

        // Migrate existing data
        $nhanViens = \DB::table('nhan_viens')->get();
        foreach ($nhanViens as $nv) {
            if ($nv->anh_cccd) {
                $images = json_decode($nv->anh_cccd, true);
                if (is_array($images) && count($images) > 1) {
                    // Set the second image as back CCCD
                    $backImage = $images[1];
                    // Keep only the first image in the array for front CCCD
                    $frontImage = [$images[0]];
                    
                    \DB::table('nhan_viens')->where('id', $nv->id)->update([
                        'anh_cccd' => json_encode($frontImage),
                        'anh_cccd_sau' => $backImage
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhan_viens', function (Blueprint $table) {
            $table->dropColumn('anh_cccd_sau');
        });
    }
};
