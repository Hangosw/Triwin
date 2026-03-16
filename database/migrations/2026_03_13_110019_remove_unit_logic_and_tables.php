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
        Schema::disableForeignKeyConstraints();
        // 1. tt_nhan_vien_cong_viecs
        if (Schema::hasTable('tt_nhan_vien_cong_viecs')) {
            try {
                Schema::table('tt_nhan_vien_cong_viecs', function (Blueprint $table) {
                    if (Schema::hasColumn('tt_nhan_vien_cong_viecs', 'DonViId')) {
                        $table->dropForeign(['DonViId']);
                        $table->dropColumn('DonViId');
                    }
                });
            } catch (\Exception $e) {}
        }

        // 2. dm_phong_bans
        if (Schema::hasTable('dm_phong_bans')) {
            try {
                Schema::table('dm_phong_bans', function (Blueprint $table) {
                    if (Schema::hasColumn('dm_phong_bans', 'DonViId')) {
                        $table->dropForeign(['DonViId']);
                        $table->dropColumn('DonViId');
                    }
                });
            } catch (\Exception $e) {}
        }

        // 3. hop_dongs
        if (Schema::hasTable('hop_dongs')) {
            try {
                Schema::table('hop_dongs', function (Blueprint $table) {
                    if (Schema::hasColumn('hop_dongs', 'DonViId')) {
                        $table->dropForeign(['DonViId']);
                        $table->dropColumn('DonViId');
                    }
                });
            } catch (\Exception $e) {}
        }

        // 4. qua_trinh_cong_tacs
        if (Schema::hasTable('qua_trinh_cong_tacs')) {
            try {
                Schema::table('qua_trinh_cong_tacs', function (Blueprint $table) {
                    if (Schema::hasColumn('qua_trinh_cong_tacs', 'DonViId')) {
                        $table->dropForeign(['DonViId']);
                        $table->dropColumn('DonViId');
                    }
                });
            } catch (\Exception $e) {}
        }

        // 5. phieu_dieu_chuyen_noi_bos
        if (Schema::hasTable('phieu_dieu_chuyen_noi_bos')) {
            try {
                Schema::table('phieu_dieu_chuyen_noi_bos', function (Blueprint $table) {
                    if (Schema::hasColumn('phieu_dieu_chuyen_noi_bos', 'DonViMoiId')) {
                        try {
                            $table->dropForeign(['DonViMoiId']);
                        } catch (\Exception $e) {}
                        $table->dropColumn('DonViMoiId');
                    }
                    if (Schema::hasColumn('phieu_dieu_chuyen_noi_bos', 'DonViId')) {
                        try {
                            $table->dropForeign(['DonViId']);
                        } catch (\Exception $e) {}
                        $table->dropColumn('DonViId');
                    }
                });
            } catch (\Exception $e) {}
        }

        // Drop pivot table first
        Schema::dropIfExists('nguoi_dung_don_vi');

        // Drop the units table
        Schema::dropIfExists('don_vis');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the units table
        if (!Schema::hasTable('don_vis')) {
            Schema::create('don_vis', function (Blueprint $table) {
                $table->id();
                $table->string('Ma')->unique();
                $table->string('Ten');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Re-create pivot table
        if (!Schema::hasTable('nguoi_dung_don_vi')) {
            Schema::create('nguoi_dung_don_vi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('NguoiDungId');
                $table->unsignedBigInteger('DonViId');
                $table->timestamps();

                $table->foreign('NguoiDungId')->references('id')->on('nguoi_dungs')->onDelete('cascade');
                $table->foreign('DonViId')->references('id')->on('don_vis')->onDelete('cascade');
            });
        }

        // Add DonViId back to tables
        if (Schema::hasTable('tt_nhan_vien_cong_viecs')) {
            Schema::table('tt_nhan_vien_cong_viecs', function (Blueprint $table) {
                if (!Schema::hasColumn('tt_nhan_vien_cong_viecs', 'DonViId')) {
                    $table->unsignedBigInteger('DonViId')->nullable()->after('NhanVienId');
                }
            });
        }

        if (Schema::hasTable('dm_phong_bans')) {
            Schema::table('dm_phong_bans', function (Blueprint $table) {
                if (!Schema::hasColumn('dm_phong_bans', 'DonViId')) {
                    $table->unsignedBigInteger('DonViId')->nullable()->after('id');
                }
            });
        }

        if (Schema::hasTable('hop_dongs')) {
            Schema::table('hop_dongs', function (Blueprint $table) {
                if (!Schema::hasColumn('hop_dongs', 'DonViId')) {
                    $table->unsignedBigInteger('DonViId')->nullable()->after('NhanVienId');
                }
            });
        }

        if (Schema::hasTable('qua_trinh_cong_tacs')) {
            Schema::table('qua_trinh_cong_tacs', function (Blueprint $table) {
                if (!Schema::hasColumn('qua_trinh_cong_tacs', 'DonViId')) {
                    $table->unsignedBigInteger('DonViId')->nullable()->after('NhanVienId');
                }
            });
        }

        if (Schema::hasTable('phieu_dieu_chuyen_noi_bos')) {
            Schema::table('phieu_dieu_chuyen_noi_bos', function (Blueprint $table) {
                if (!Schema::hasColumn('phieu_dieu_chuyen_noi_bos', 'DonViMoiId')) {
                    $table->unsignedBigInteger('DonViMoiId')->nullable()->after('NhanVienId');
                }
            });
        }
    }
};
