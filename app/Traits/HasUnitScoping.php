<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasUnitScoping
{
    /**
     * Scope a query to only include records from the user's unit if they are a Unit Admin.
     * Super Admin sees everything.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUnit(Builder $query)
    {
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        // Super Admin thấy toàn bộ
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $table = $this->getTable();

        // 1. Lấy danh sách ID đơn vị được gán trực tiếp cho người dùng (qua bảng pivot nguoi_dung_don_vi)
        $donViIds = $user->donVis->pluck('id')->toArray();

        // 2. Nếu không có đơn vị nào được gán cụ thể, lấy đơn vị của chính nhân viên đó (fallback)
        if (empty($donViIds)) {
            $nhanVien = $user->nhanVien;
            if ($nhanVien && $nhanVien->ttCongViec) {
                $donViIds = [$nhanVien->ttCongViec->DonViId];
            }
        }

        // Apply filtering if we have unit IDs
        if (!empty($donViIds)) {
            // Trường hợp 1: Model là NhanVien
            if ($table === 'nhan_viens') {
                return $query->whereHas('ttCongViec', function ($q) use ($donViIds) {
                    $q->whereIn('DonViId', $donViIds);
                });
            }

            // Trường hợp 2: Model có cột DonViId hoặc chính nó là đơn vị
            if ($table === 'don_vis') {
                return $query->whereIn('id', $donViIds);
            }

            if (in_array($table, ['hop_dongs', 'dm_phong_bans', 'dm_to_dois', 'phieu_dieu_chuyen_noi_bos', 'tt_nhan_vien_cong_viecs'])) {
                return $query->whereIn('DonViId', $donViIds);
            }

            // Trường hợp 3: Model có quan hệ nhanVien
            if (method_exists($this, 'nhanVien')) {
                return $query->whereHas('nhanVien.ttCongViec', function ($q) use ($donViIds) {
                    $q->whereIn('DonViId', $donViIds);
                });
            }

            // Trường hợp 4: Model có quan hệ phongBan (ví dụ: DmToDoi)
            if (method_exists($this, 'phongBan')) {
                return $query->whereHas('phongBan', function ($q) use ($donViIds) {
                    $q->whereIn('DonViId', $donViIds);
                });
            }

            // Fallback: Thử tìm cột DonViId nếu không khớp các case trên
            return $query->whereIn('DonViId', $donViIds);
        }

        return $query;
    }
}
