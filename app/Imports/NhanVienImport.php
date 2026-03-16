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
        // Load relationships into memory to avoid N+1 queries during import

        $phongBans = DmPhongBan::all()->keyBy('Ten');
        $chucVus = DmChucVu::all()->keyBy('Ten');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +1 for 0-index, +1 for startRow tracking

            // Bỏ qua dòng trống
            if (!isset($row[1]) || !isset($row[2])) {
                continue;
            }

            $maNV = trim($row[1]); // B
            $hoTen = trim($row[2]); // C
            $email = isset($row[3]) ? trim($row[3]) : null; // D
            $sdt = isset($row[4]) ? trim($row[4]) : null; // E
            $ngaySinhRaw = isset($row[5]) ? trim($row[5]) : null; // F
            $gioiTinhRaw = isset($row[6]) ? trim($row[6]) : null; // G
            $cccd = isset($row[7]) ? trim($row[7]) : null; // H

            $tenPhongBan = isset($row[9]) ? trim($row[9]) : null; // J
            $tenChucVu = isset($row[10]) ? trim($row[10]) : null; // K
            $ngayTuyenDungRaw = isset($row[11]) ? trim($row[11]) : null; // L

            // 1. Kiểm tra nhân viên đã tồn tại
            if (
                NhanVien::where('Ma', $maNV)->orWhere(function ($query) use ($cccd) {
                    if ($cccd) {
                        $query->where('SoCCCD', $cccd);
                    } else {
                        $query->whereRaw('1 = 0'); // false condition if cccd is null
                    }
                })->exists()
            ) {
                $this->errors[] = "Dòng $rowNumber: Nhân viên có Mã '$maNV' hoặc CCCD '$cccd' đã tồn tại. Bỏ qua.";
                continue;
            }

            // 2. Tra cứu Phòng ban, Chức vụ
            $phongBanModel = $phongBans->get($tenPhongBan);
            $chucVuModel = $chucVus->get($tenChucVu);


            if (!$phongBanModel) {
                $this->errors[] = "Dòng $rowNumber: Phòng ban '$tenPhongBan' không tồn tại trên hệ thống.";
                continue;
            }
            if (!$chucVuModel) {
                $this->errors[] = "Dòng $rowNumber: Chức vụ '$tenChucVu' không tồn tại trên hệ thống.";
                continue;
            }

            // 3. Xử lý logic giới tính, ngày tháng
            $gioiTinh = (strtolower($gioiTinhRaw) == 'nam') ? 1 : 0;
            $ngaySinh = $this->parseDate($ngaySinhRaw);
            $ngayTuyenDung = $this->parseDate($ngayTuyenDungRaw);

            DB::beginTransaction();
            try {
                // Tạo tài khoản người dùng
                $taiKhoan = $email ?: ($sdt ?: $maNV);
                $matKhau = $sdt ?: $maNV; // Default password to phone or employee code

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
                    'Email' => $email, // Need to add Email to fillable or check if it's there
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
                    'LoaiNhanVien' => 1, // Mặc định là Khối văn phòng (1) hoặc bạn có thể quy định khác
                ]);

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Dòng $rowNumber: Lỗi hệ thống khi lưu - " . $e->getMessage();
            }
        }
    }

    private function parseDate($dateString)
    {
        if (!$dateString)
            return null;

        // Handle Excel numeric date format
        if (is_numeric($dateString)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString)->format('Y-m-d');
        }

        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // or log error/default date
        }
    }
}
