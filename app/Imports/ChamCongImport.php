<?php

namespace App\Imports;

use App\Models\ChamCong;
use App\Models\NhanVien;
use App\Models\DmCaLamViec;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class ChamCongImport implements ToCollection, WithStartRow
{
    public $errors = [];
    public $successCount = 0;

    public function startRow(): int
    {
        return 11; // Bắt đầu từ dòng 11 (dòng chứa ngày)
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty())
            return;

        // ── Hàng đầu tiên (row 11) = tiêu đề ngày ─────────────────────────
        $headerRow = $rows->first();

        // Xây dựng bản đồ ngày: [ colVaoIndex => 'Y-m-d' ]
        // Từ cột F (index 5) trở đi, mỗi ngày chiếm 2 cột: (vào, ra)
        $dateMap = [];
        for ($col = 5; $col < count($headerRow); $col += 2) {
            $rawDate = $headerRow[$col] ?? null;
            if ($rawDate === null || $rawDate === '' || $rawDate === '-') {
                continue;
            }
            $dateStr = $this->parseDate($rawDate);
            if ($dateStr) {
                $dateMap[$col] = $dateStr; // key = cột giờ vào, value = ngày
            }
        }

        if (empty($dateMap)) {
            $this->errors[] = 'Không tìm thấy cột ngày hợp lệ nào trong hàng 11.';
            return;
        }

        // Lấy ca làm việc chung (không phụ thuộc lịch làm việc)
        $caLamViec = DmCaLamViec::first();

        // ── Cache danh sách nhân viên theo Mã ──────────────────────────────
        $nhanViens = NhanVien::all()->keyBy('Ma');

        // ── Xử lý từng dòng nhân viên (bỏ qua hàng 11) ────────────────────
        foreach ($rows->skip(1) as $index => $row) {
            $rowNumber = $index + 12; // row number thực tế trong Excel

            // Cột E (index 4) = MSNV
            $msnv = isset($row[4]) ? trim((string) $row[4]) : '';
            if ($msnv === '' || $msnv === null) {
                continue; // bỏ qua dòng trống
            }

            $nhanVien = $nhanViens->get($msnv);
            if (!$nhanVien) {
                $this->errors[] = "Dòng $rowNumber: Không tìm thấy nhân viên có MSNV '$msnv'. Bỏ qua.";
                continue;
            }

            // ── Xử lý từng cặp ngày (giờ vào / giờ ra) ────────────────────
            foreach ($dateMap as $vaoCol => $dateStr) {
                $raCol = $vaoCol + 1;

                $rawVao = $row[$vaoCol] ?? null;
                $rawRa = $row[$raCol] ?? null;

                // Dấu "-" hoặc rỗng = null
                $gioVao = $this->parseTimeToDatetime($rawVao, $dateStr);
                $gioRa = $this->parseTimeToDatetime($rawRa, $dateStr);

                // Xác định trạng thái
                $trangThai = null;
                if ($gioVao && $caLamViec) {
                    $caGioVao = $caLamViec->GioVao; 
                    $caGioRa = $caLamViec->GioRa;  

                    $inTime = $gioVao->format('H:i:s');
                    
                    // 1. Giờ vào > GioVao thì là 'tre', không cần xét giờ ra
                    if ($inTime > $caGioVao) {
                        $trangThai = 'tre';
                    } else {
                        if ($gioRa) {
                            $outTime = $gioRa->format('H:i:s');
                            // 2. Giờ chấm công vào trước GioVao và giờ ra sau GioRa thì trạng thái là dung_gio
                            if ($outTime >= $caGioRa) {
                                $trangThai = 'dung_gio';
                            } 
                            // 3. Giờ vào đúng giờ nhưng giờ ra trước GioRa thì là ve_som
                            else {
                                $trangThai = 've_som';
                            }
                        } else {
                            $trangThai = 'dung_gio'; // Tạm thời là đúng giờ nếu chưa có giờ ra
                        }
                    }
                }

                DB::beginTransaction();
                try {
                    // Ghi đè: xóa bản ghi cũ nếu có
                    ChamCong::where('NhanVienId', $nhanVien->id)
                        ->whereDate('Vao', $dateStr)
                        ->delete();

                    // Tạo bản ghi mới
                    // Nếu không có giờ vào, dùng 00:00:00 của ngày đó
                    $vaoDateTime = $gioVao ?? Carbon::parse($dateStr . ' 00:00:00');

                    ChamCong::create([
                        'NhanVienId' => $nhanVien->id,
                        'Vao' => $vaoDateTime,
                        'Ra' => $gioRa,
                        'TrangThai' => $trangThai,
                    ]);

                    DB::commit();
                    $this->successCount++;

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->errors[] = "Dòng $rowNumber / Ngày $dateStr: Lỗi hệ thống - " . $e->getMessage();
                }
            }
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Parse giá trị ngày (chuỗi hoặc số Excel) thành 'Y-m-d'
     */
    private function parseDate($value): ?string
    {
        if ($value === null || $value === '' || $value === '-')
            return null;

        // Số Excel
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse giá trị giờ (chuỗi "HH:mm" hoặc số fraction Excel) kết hợp với ngày
     * Trả về Carbon hoặc null nếu không có dữ liệu
     */
    private function parseTimeToDatetime($value, string $dateStr): ?Carbon
    {
        if ($value === null || $value === '' || $value === '-')
            return null;

        // Excel lưu thời gian dạng fraction-of-day (0.5 = 12:00:00)
        if (is_numeric($value)) {
            $seconds = (int) round((float) $value * 86400);
            $h = intdiv($seconds, 3600);
            $m = intdiv($seconds % 3600, 60);
            $s = $seconds % 60;
            $timeStr = sprintf('%02d:%02d:%02d', $h, $m, $s);
            return Carbon::parse("$dateStr $timeStr");
        }

        // Chuỗi thời gian thông thường (vd: "07:08", "07:08:00")
        $value = trim((string) $value);
        if ($value === '-' || $value === '')
            return null;

        try {
            return Carbon::parse("$dateStr $value");
        } catch (\Exception $e) {
            return null;
        }
    }
}
