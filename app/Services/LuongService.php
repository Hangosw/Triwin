<?php

namespace App\Services;

use App\Models\CauHinhBaoHiem;
use App\Models\CauHinhLichLamViec;
use App\Models\ChamCong;
use App\Models\PhuLucHopDong;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * LuongService - Tính lương tự động cho nhân viên
 *
 * Phân loại theo LoaiNhanVien trong tt_nhan_vien_cong_viecs:
 *   0 = Công nhân  → Tính theo ngày công thực tế (chấm công)
 *   1 = Văn phòng  → Tính theo lương cứng hợp đồng
 *
 * Công thức chung:
 *   Khấu trừ BH = LuongCoBan × Tổng tỉ lệ NLĐ
 *   Giảm trừ GC = 11.000.000 + (số NPT × 4.400.000)
 *   Thuế TNCN   = Lũy tiến 7 bậc
 *   Thực nhận   = Thu nhập − Khấu trừ BH − Thuế TNCN
 */
class LuongService
{
    /** Giảm trừ gia cảnh (Nghị quyết 954/2020/UBTVQH14) */
    const GIAM_TRU_BAN_THAN = 11_000_000;
    const GIAM_TRU_MOI_NGUOI = 4_400_000;

    /** Biểu thuế lũy tiến từng phần */
    const THUE_BAC_LUY_TIEN = [
        ['limit' => 5_000_000, 'rate' => 5],
        ['limit' => 10_000_000, 'rate' => 10],
        ['limit' => 18_000_000, 'rate' => 15],
        ['limit' => 32_000_000, 'rate' => 20],
        ['limit' => 52_000_000, 'rate' => 25],
        ['limit' => 80_000_000, 'rate' => 30],
        ['limit' => PHP_INT_MAX, 'rate' => 35],
    ];

    /**
     * Tính lương đầy đủ — phân nhánh theo LoaiNhanVien.
     *
     * @param  \App\Models\NhanVien  $nhanVien  đã eager-load hopDongs, thanNhans, ttCongViec
     * @param  int  $thang
     * @param  int  $nam
     * @return array
     */
    public static function tinhLuong($nhanVien, int $thang, int $nam): array
    {
        $salaryCalculationType = \App\Models\SystemConfig::getValue('salary_calculation_type', 'contract');
        
        // Theo chấm công cho tất cả hoặc nếu là Công nhân (trong chế độ mặc định)
        $isAttendanceMode = ($salaryCalculationType === 'attendance');
        $isWorker = ($nhanVien->ttCongViec?->LoaiNhanVien === 0);

        if ($isAttendanceMode || ($salaryCalculationType === 'contract' && $isWorker)) {
            return self::tinhLuongCongNhan($nhanVien, $thang, $nam, $isAttendanceMode);
        }

        // Mặc định hoặc theo hợp đồng: Lương văn phòng (không pro-rate)
        return self::tinhLuongVanPhong($nhanVien, $thang, $nam, ($salaryCalculationType === 'contract'));
    }

    /**
     * CÔNG NHÂN — dựa trên chấm công thực tế.
     * @param bool $forcedAttendance Nếu true, ghi chú loai_nhan_vien_text là dựa trên cấu hình hệ thống
     */
    public static function tinhLuongCongNhan($nhanVien, int $thang, int $nam, bool $forcedAttendance = false): array
    {
        $hopDong = $nhanVien->hopDongs ? $nhanVien->hopDongs->first() : null;
        $luongCoBan = (float) ($hopDong?->LuongCoBan ?? 0);

        // --- Ngày công chuẩn (ưu tiên config hệ thống, sau đó đến calendar) ---
        $ngayCongChuan = self::tinhNgayCongChuan($thang, $nam);

        // --- Ngày công thực tế (chấm công có giờ Ra) ---
        $ngayCongThucTe = self::tinhNgayCongThucTe($nhanVien->id, $thang, $nam);

        // Tỉ lệ ngày công (0.0 → 1.0)
        $tiLeNgayCong = $ngayCongChuan > 0 ? ($ngayCongThucTe / $ngayCongChuan) : 0;

        // --- Lương theo ngày công ---
        $donGiaNgay = $ngayCongChuan > 0 ? $luongCoBan / $ngayCongChuan : 0;
        $luongNgayCong = round($donGiaNgay * $ngayCongThucTe, 2);

        // --- Phụ cấp tỉ lệ theo ngày công ---
        $tongPhuCapHD = self::tinhTongPhuCap($hopDong);
        $tongPhuCap = round($tongPhuCapHD * $tiLeNgayCong, 2);

        // --- Tổng thu nhập ---
        $tongThuNhap = $luongNgayCong + $tongPhuCap;

        // --- Bảo hiểm ---
        $baoHiems = \App\Models\CauHinhBaoHiem::getHieuLucHienTai();
        $tongKhauTruBH = 0;
        $baoHiemsDetail = [];
        foreach ($baoHiems as $bh) {
            $amount = ($luongCoBan * $bh->TiLeNhanVien) / 100;
            $tongKhauTruBH += $amount;
            $baoHiemsDetail[] = [
                'ten' => $bh->TenLoai,
                'ti_le' => $bh->TiLeNhanVien,
                'so_tien' => $amount
            ];
        }

        // --- Giảm trừ gia cảnh ---
        $thanNhans = $nhanVien->thanNhans ?? collect();
        $soNguoiPhuThuoc = $thanNhans->where('LaGiamTruGiaCanh', 1)->count();
        $tongGiamTru = self::GIAM_TRU_BAN_THAN + ($soNguoiPhuThuoc * self::GIAM_TRU_MOI_NGUOI);

        // --- Thuế TNCN ---
        $thuNhapChiuThue = max(0, $tongThuNhap - $tongKhauTruBH);
        $thuNhapTinhThue = max(0, $thuNhapChiuThue - $tongGiamTru);
        $thueTNCN = self::tinhThueLuyTien($thuNhapTinhThue);

        $tongKhauTru = $tongKhauTruBH + $thueTNCN;
        $luongThucNhan = max(0, $tongThuNhap - $tongKhauTru);

        return [
            'loai_nhan_vien' => 0,
            'loai_nhan_vien_text' => $forcedAttendance ? 'Theo chấm công (Hệ thống)' : 'Theo chấm công',
            // Thu nhập
            'luong_co_ban' => $luongCoBan,
            'ngay_cong_chuan' => $ngayCongChuan,
            'ngay_cong_thuc_te' => $ngayCongThucTe,
            'don_gia_ngay' => round($donGiaNgay, 2),
            'luong_ngay_cong' => $luongNgayCong,
            'tong_phu_cap' => $tongPhuCap,
            'tong_thu_nhap' => $tongThuNhap,
            // Khấu trừ
            'tong_khau_tru_bh' => $tongKhauTruBH,
            'bao_hiems_detail' => $baoHiemsDetail,
            'so_nguoi_phu_thuoc' => $soNguoiPhuThuoc,
            'tong_giam_tru' => $tongGiamTru,
            'thu_nhap_chiu_thue' => $thuNhapChiuThue,
            'thu_nhap_tinh_thue' => $thuNhapTinhThue,
            'thue_tncn' => $thueTNCN,
            'tong_khau_tru' => $tongKhauTru,
            // Kết quả
            'luong_thuc_nhan' => $luongThucNhan,
            // Meta
            'thang' => $thang,
            'nam' => $nam,
            'hop_dong' => $hopDong,
            'bao_hiems' => $baoHiems,
        ];
    }

    /**
     * VĂN PHÒNG — lương cứng theo hợp đồng.
     * @param bool $forcedContract Nếu true, ghi chú loai_nhan_vien_text là dựa trên cấu hình hệ thống
     */
    public static function tinhLuongVanPhong($nhanVien, int $thang, int $nam, bool $forcedContract = false): array
    {
        $hopDong = $nhanVien->hopDongs ? $nhanVien->hopDongs->first() : null;
        $luongCoBan = (float) ($hopDong?->LuongCoBan ?? 0);

        // --- Phụ cấp ---
        $tongPhuCap = self::tinhTongPhuCap($hopDong);

        $ngayCongChuan = self::tinhNgayCongChuan($thang, $nam);

        // --- Tổng thu nhập ---
        // Nếu ở chế độ "Theo hợp đồng", sử dụng cột TongLuong trực tiếp
        // TongLuong thường đã bao gồm Lương cơ bản + Phụ cấp cố định
        if ($forcedContract) {
            $tongThuNhap = ($hopDong?->TongLuong ?? 0);
        } else {
            $tongThuNhap = $luongCoBan + $tongPhuCap;
        }

        // --- Bảo hiểm ---
        $baoHiems = \App\Models\CauHinhBaoHiem::getHieuLucHienTai();
        $tongKhauTruBH = 0;
        $baoHiemsDetail = [];
        foreach ($baoHiems as $bh) {
            $amount = ($luongCoBan * $bh->TiLeNhanVien) / 100;
            $tongKhauTruBH += $amount;
            $baoHiemsDetail[] = [
                'ten' => $bh->TenLoai,
                'ti_le' => $bh->TiLeNhanVien,
                'so_tien' => $amount
            ];
        }

        // --- Giảm trừ gia cảnh ---
        $thanNhans = $nhanVien->thanNhans ?? collect();
        $soNguoiPhuThuoc = $thanNhans->where('LaGiamTruGiaCanh', 1)->count();
        $tongGiamTru = self::GIAM_TRU_BAN_THAN + ($soNguoiPhuThuoc * self::GIAM_TRU_MOI_NGUOI);

        // --- Thuế TNCN ---
        $thuNhapChiuThue = max(0, $tongThuNhap - $tongKhauTruBH);
        $thuNhapTinhThue = max(0, $thuNhapChiuThue - $tongGiamTru);
        $thueTNCN = self::tinhThueLuyTien($thuNhapTinhThue);

        $tongKhauTru = $tongKhauTruBH + $thueTNCN;
        $luongThucNhan = max(0, $tongThuNhap - $tongKhauTru);

        return [
            'loai_nhan_vien' => 1,
            'loai_nhan_vien_text' => $forcedContract ? 'Theo hợp đồng (Hệ thống)' : 'Theo hợp đồng',
            // Thu nhập
            'luong_co_ban' => $luongCoBan,
            'ngay_cong_chuan' => $ngayCongChuan,
            'ngay_cong_thuc_te' => self::tinhNgayCongThucTe($nhanVien->id, $thang, $nam),
            'don_gia_ngay' => null,
            'luong_ngay_cong' => $forcedContract ? ($hopDong?->TongLuong ?? 0) : $luongCoBan,
            'tong_phu_cap' => $tongPhuCap,
            'tong_thu_nhap' => $tongThuNhap,
            // Khấu trừ
            'tong_khau_tru_bh' => $tongKhauTruBH,
            'bao_hiems_detail' => $baoHiemsDetail,
            'so_nguoi_phu_thuoc' => $soNguoiPhuThuoc,
            'tong_giam_tru' => $tongGiamTru,
            'thu_nhap_chiu_thue' => $thuNhapChiuThue,
            'thu_nhap_tinh_thue' => $thuNhapTinhThue,
            'thue_tncn' => $thueTNCN,
            'tong_khau_tru' => $tongKhauTru,
            // Kết quả
            'luong_thuc_nhan' => $luongThucNhan,
            // Meta
            'thang' => $thang,
            'nam' => $nam,
            'hop_dong' => $hopDong,
            'bao_hiems' => $baoHiems,
        ];
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Tính số ngày làm việc chuẩn trong tháng dựa theo cau_hinh_lich_lam_viecs.
     * Lặp qua từng ngày trong tháng, xem thứ đó có CoLamViec = 1 không.
     * PHP: dayOfWeek: 0=CN, 1=T2 ... 6=T7. Bảng: Thu (1=CN?, 2=T2?)
     * → Chuẩn hoá: bảng Thu(1..7) = Carbon dayOfWeekIso (1=T2..7=CN)
     */
    public static function tinhNgayCongChuan(int $thang, int $nam): int
    {
        // Ưu tiên config hệ thống
        $standardWorkDays = (int) \App\Models\SystemConfig::getValue('standard_work_days', 0);
        if ($standardWorkDays > 0) {
            return $standardWorkDays;
        }

        // Nếu không có, tính theo calendar config
        $lichLamViec = \App\Models\CauHinhLichLamViec::all()->keyBy('Thu');
        $soNgay = Carbon::createFromDate($nam, $thang, 1)->daysInMonth;
        $demNgay = 0;

        for ($ngay = 1; $ngay <= $soNgay; $ngay++) {
            $date = Carbon::createFromDate($nam, $thang, $ngay);
            $thuISO = $date->dayOfWeekIso; // 1=T2 ... 7=CN
            $config = $lichLamViec->get($thuISO);

            if ($config && $config->CoLamViec == 1) {
                $demNgay++;
            }
        }

        return $demNgay ?: 26; // fallback
    }

    /**
     * Đếm số ngày chấm công thực tế của nhân viên trong tháng
     * (chỉ tính bản ghi có Ra IS NOT NULL — đã check-out).
     */
    public static function tinhNgayCongThucTe(int $nhanVienId, int $thang, int $nam): float
    {
        return (float) ChamCong::where('NhanVienId', $nhanVienId)
            ->whereYear('Vao', $nam)
            ->whereMonth('Vao', $thang)
            ->whereNotNull('Ra')
            ->sum('Cong');
    }

    /**
     * Tổng phụ cấp từ phụ lục hợp đồng mới nhất.
     *
     * Ưu tiên lấy từ phụ lục (phu_luc_hop_dongs) mới nhất của hợp đồng gốc.
     * Mỗi phụ lục liên kết với một hợp đồng phiên bản (HopDongPLId), từ đó
     * lấy toàn bộ điều khoản phụ cấp (chi_tiet_phu_luc) và cộng so_tien lại.
     *
     * Nếu không có phụ lục nào, fallback về mảng PhuCap JSON trong hop_dongs.
     */
    public static function tinhTongPhuCap($hopDong): float
    {
        if (!$hopDong)
            return 0;

        // Tìm phụ lục mới nhất của hợp đồng gốc này
        // Hợp đồng gốc = $hopDong (HopDongGocId trỏ vào $hopDong->id)
        $phuLucMoiNhat = PhuLucHopDong::where('HopDongGocId', $hopDong->id)
            ->orderBy('ngay_ky', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($phuLucMoiNhat) {
            // Lấy tổng so_tien từ bảng chi_tiet_phu_luc của phụ lục này
            $tong = DB::table('chi_tiet_phu_luc')
                ->where('PhuLucId', $phuLucMoiNhat->id)
                ->sum('so_tien');
            return (float) $tong;
        }

        // Fallback: lấy từ JSON PhuCap cũ trong hop_dongs (nếu chưa có phụ lục)
        $phuCap = $hopDong->PhuCap;
        if (!is_array($phuCap))
            return 0;

        $tong = 0;
        foreach ($phuCap as $item) {
            $tong += (float) ($item['amount'] ?? 0);
        }
        return $tong;
    }

    /**
     * Tính thuế TNCN lũy tiến từng phần.
     */
    public static function tinhThueLuyTien(float $thuNhapTinhThue): float
    {
        if ($thuNhapTinhThue <= 0)
            return 0;

        $thueTNCN = 0;
        $remaining = $thuNhapTinhThue;
        $prevLimit = 0;

        foreach (self::THUE_BAC_LUY_TIEN as $bracket) {
            if ($remaining <= 0)
                break;
            $taxable = min($remaining, $bracket['limit'] - $prevLimit);
            $thueTNCN += $taxable * $bracket['rate'] / 100;
            $remaining -= $taxable;
            $prevLimit = $bracket['limit'];
        }

        return round($thueTNCN, 2);
    }
}
