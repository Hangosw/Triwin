<?php

namespace App\Imports;

use App\Models\DmChucVu;
use App\Models\DmPhongBan;

use App\Models\NhanVien;
use App\Models\TtNhanVienCongViec;
use App\Models\NguoiDung;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class NhanVienImport implements ToCollection, WithStartRow
{
    public $errors = [];
    public $successCount = 0;

    public function startRow(): int
    {
        // Skip header row
        return 2;
    }

    public function collection(Collection $rows)
    {
        $phongBans = DmPhongBan::all();
        $chucVus = DmChucVu::all();

        // Chuẩn bị counter cho mã nhân viên tự động
        $year = date('y');
        $latestEmployee = NhanVien::where('Ma', 'like', "NV_{$year}_%")
            ->orderBy('Ma', 'desc')
            ->first();
        
        $currentSequence = 0;
        if ($latestEmployee) {
            $currentSequence = intval(substr($latestEmployee->Ma, -5));
        }

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; 

            // Chỉ bỏ qua nếu không có Họ tên (Cột C)
            if (!isset($row[2]) || empty(trim($row[2]))) {
                continue;
            }

            $maNV = isset($row[1]) ? trim($row[1]) : null;
            $hoTen = trim($row[2]);
            $email = isset($row[3]) ? trim($row[3]) : null;
            $sdt = isset($row[4]) ? trim($row[4]) : null;
            $ngaySinhRaw = isset($row[5]) ? trim($row[5]) : null;
            $gioiTinhRaw = isset($row[6]) ? trim($row[6]) : null;
            $cccd = isset($row[7]) ? trim($row[7]) : null;
            $tenPhongBan = isset($row[8]) ? trim($row[8]) : null;
            $tenChucVu = isset($row[9]) ? trim($row[9]) : null;
            $ngayTuyenDungRaw = isset($row[10]) ? trim($row[10]) : null;

            // Tự động tạo mã nếu thiếu
            if (empty($maNV)) {
                $currentSequence++;
                $sequenceStr = str_pad($currentSequence, 5, '0', STR_PAD_LEFT);
                $maNV = "NV_{$year}_{$sequenceStr}";
            }

            // 1. Kiểm tra nhân viên đã tồn tại
            $exists = NhanVien::where('Ma', $maNV)
                ->when($cccd, function($q) use ($cccd) {
                    return $q->orWhere('SoCCCD', $cccd);
                })
                ->exists();

            if ($exists) {
                $this->errors[] = "Dòng $rowNumber: Nhân viên có Mã '$maNV' hoặc CCCD '$cccd' đã tồn tại.";
                continue;
            }

            // 2. Tra cứu Phòng ban, Chức vụ (không phân biệt hoa thường)
            $phongBanModel = $phongBans->first(function($item) use ($tenPhongBan) {
                return mb_strtolower($item->Ten) == mb_strtolower($tenPhongBan);
            });
            $chucVuModel = $chucVus->first(function($item) use ($tenChucVu) {
                return mb_strtolower($item->Ten) == mb_strtolower($tenChucVu);
            });

            if (!$phongBanModel) {
                $this->errors[] = "Dòng $rowNumber: Phòng ban '$tenPhongBan' không tìm thấy.";
                continue;
            }
            if (!$chucVuModel) {
                $this->errors[] = "Dòng $rowNumber: Chức vụ '$tenChucVu' không tìm thấy.";
                continue;
            }

            // 3. Xử lý logic giới tính, ngày tháng
            $gioiTinh = (mb_strtolower($gioiTinhRaw) == 'nam') ? 1 : 0;
            $ngaySinh = $this->parseDate($ngaySinhRaw);
            $ngayTuyenDung = $this->parseDate($ngayTuyenDungRaw);

            DB::beginTransaction();
            try {
                // Tạo tài khoản người dùng
                $taiKhoan = $email ?: ($sdt ?: $maNV);
                $matKhau = $sdt ?: $maNV;

                $user = NguoiDung::create([
                    'TaiKhoan' => $taiKhoan,
                    'Email' => $email,
                    'SoDienThoai' => $sdt,
                    'MatKhau' => Hash::make($matKhau),
                    'TrangThai' => 1,
                ]);

                // Tạo nhân viên
                $nhanVien = NhanVien::create([
                    'Ma' => $maNV,
                    'Ten' => $hoTen,
                    'NguoiDungId' => $user->id,
                    'Email' => $email,
                    'SoDienThoai' => $sdt,
                    'NgaySinh' => $ngaySinh,
                    'GioiTinh' => $gioiTinh,
                    'SoCCCD' => $cccd,
                ]);

                // Tạo thông tin công tác
                TtNhanVienCongViec::create([
                    'NhanVienId' => $nhanVien->id,
                    'PhongBanId' => $phongBanModel->id,
                    'ChucVuId' => $chucVuModel->id,
                    'NgayTuyenDung' => $ngayTuyenDung,
                    'LoaiNhanVien' => 1,
                ]);

                // Khởi tạo phép năm
                \App\Models\QuanLyPhepNam::khoiTaoPhepNam($nhanVien->id, date('Y'));

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Dòng $rowNumber: Lỗi lưu dữ liệu - " . $e->getMessage();
            }
        }
    }

    private function parseDate($dateString)
    {
        if (empty($dateString))
            return null;

        if (is_numeric($dateString)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString)->format('Y-m-d');
        }

        try {
            // Thử parse các định dạng phổ biến ở VN
            if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $dateString, $matches)) {
                return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
            }
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
