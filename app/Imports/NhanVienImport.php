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

        // Mã nhân viên tự động sẽ dựa trên ID tiếp theo và tên nhân viên
        $maxId = NhanVien::max('id') ?: 0;
        $nextId = $maxId + 1;

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

            if ($sdt) {
                // Loại bỏ khoảng trắng, dấu gạch ngang, dấu chấm
                $sdt = preg_replace('/[\s\-\.]/', '', $sdt);
                
                // Đổi +84 hoặc 84 ở đầu thành số 0
                if (preg_match('/^(?:\+?84)(.*)$/', $sdt, $matches)) {
                    $sdt = '0' . $matches[1];
                }
                
                // Loại bỏ mọi ký tự không phải là số
                $sdt = preg_replace('/[^0-9]/', '', $sdt);
                
                // Thêm số 0 ở đầu nếu Excel tự xóa (độ dài 9 chữ số)
                if (strlen($sdt) === 9 && substr($sdt, 0, 1) !== '0') {
                    $sdt = '0' . $sdt;
                }
            }

            $ngaySinhRaw = isset($row[5]) ? trim($row[5]) : null;
            $gioiTinhRaw = isset($row[6]) ? trim($row[6]) : null;
            $cccd = isset($row[7]) ? trim($row[7]) : null;
            
            if ($cccd) {
                // Loại bỏ khoảng trắng và ký tự không phải số
                $cccd = preg_replace('/[^0-9]/', '', $cccd);
                
                // Thêm số 0 ở đầu nếu Excel tự động xóa
                if (strlen($cccd) > 0 && strlen($cccd) <= 9) {
                    $cccd = str_pad($cccd, 9, '0', STR_PAD_LEFT);
                } elseif (strlen($cccd) > 9 && strlen($cccd) <= 12) {
                    $cccd = str_pad($cccd, 12, '0', STR_PAD_LEFT);
                }
            }

            $tenPhongBan = isset($row[8]) ? trim($row[8]) : null;
            $tenChucVu = isset($row[9]) ? trim($row[9]) : null;
            $ngayTuyenDungRaw = isset($row[10]) ? trim($row[10]) : null;
            $diaChi = isset($row[11]) ? trim($row[11]) : null;

            // Tự động tạo mã nếu thiếu: 3W[ID]_[NAME]
            if (empty($maNV)) {
                $nameParts = explode(' ', $hoTen);
                $lastName = end($nameParts);
                $cleanName = $this->removeAccents($lastName);
                $maNV = "3W" . ($nextId++) . "_" . $cleanName;
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
                    'Ten' => $hoTen,
                    'TaiKhoan' => $taiKhoan,
                    'Email' => $email,
                    'SoDienThoai' => $sdt,
                    'MatKhau' => Hash::make($matKhau),
                    'TrangThai' => 1,
                ]);

                $user->assignRole('Nhân viên');

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
                    'DiaChi' => $diaChi,
                ]);

                // Tạo thông tin công tác
                TtNhanVienCongViec::create([
                    'NhanVienId' => $nhanVien->id,
                    'PhongBanId' => $phongBanModel->id,
                    'ChucVuId' => $chucVuModel->id,
                    'NgayTuyenDung' => $ngayTuyenDung,
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

    /**
     * Loại bỏ dấu tiếng Việt và chuyển thành chữ hoa
     */
    private function removeAccents($str)
    {
        $accents = [
            'a' => ['à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ'],
            'e' => ['è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ'],
            'i' => ['ì', 'í', 'ị', 'ỉ', 'ĩ'],
            'o' => ['ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ'],
            'u' => ['ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ'],
            'y' => ['ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ'],
            'd' => ['đ'],
            'A' => ['À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ'],
            'E' => ['È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ'],
            'I' => ['Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ'],
            'O' => ['Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ'],
            'U' => ['Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ'],
            'Y' => ['Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ'],
            'D' => ['Đ'],
        ];

        foreach ($accents as $nonAccent => $accentList) {
            foreach ($accentList as $accent) {
                $str = str_replace($accent, $nonAccent, $str);
            }
        }

        return strtoupper($str);
    }
}
