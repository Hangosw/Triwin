<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Collection;

class SalaryExport
{
    protected Collection $luongs;
    protected array $insuranceDetails;
    protected int $thang;
    protected int $nam;

    public function __construct(Collection $luongs, array $insuranceDetails, int $thang, int $nam)
    {
        $this->luongs = $luongs;
        $this->insuranceDetails = $insuranceDetails;
        $this->thang = $thang;
        $this->nam = $nam;
    }

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $spreadsheet = $this->build();
        $writer = new Xlsx($spreadsheet);
        $filename = "BaoCaoLuong_T{$this->thang}_{$this->nam}.xlsx";

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function build(): Spreadsheet
    {
        $ss = new Spreadsheet();
        $ws = $ss->getActiveSheet();
        $ws->setTitle('Danh Sach Luong');

        // ── Màu sắc ─────────────────────────────────────
        $C = [
            'green_dark' => '0BAA4B',
            'green_light' => 'DCFCE7',
            'green_text' => '166534',
            'blue_light' => 'E0F2FE',
            'blue_text' => '0369A1',
            'red_light' => 'FEE2E2',
            'red_text' => 'BE123C',
            'ins_bg' => 'FFF1F2',
            'ins_text' => '9F1239',
            'header_bg' => 'F8FAFC',
            'group_bg' => 'F1F5F9',
            'white' => 'FFFFFF',
            'dark' => '1E293B',
            'indigo' => '4F46E5',
            'gray' => '6B7280',
            'summary_bg' => 'E2E8F0',
        ];

        $numFmt = '#,##0 "đ"';

        $thin = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ];

        $fillStyle = fn($hex) => [
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FF' . $hex]],
        ];

        $fontStyle = fn($hex, $bold = false, $size = 10) => [
            'font' => ['name' => 'Arial', 'color' => ['argb' => 'FF' . $hex], 'bold' => $bold, 'size' => $size],
        ];

        $merge = fn($range) => $ws->mergeCells($range);
        $style = fn($range) => $ws->getStyle($range);
        $setVal = fn($cell, $val) => $ws->setCellValue($cell, $val);
        $height = fn($row, $h) => $ws->getRowDimension($row)->setRowHeight($h);

        // ── Tiêu đề ──────────────────────────────────────
        $merge('A1:J1');
        $setVal('A1', "DANH SÁCH LƯƠNG THÁNG {$this->thang}/{$this->nam}");
        $style('A1')->applyFromArray(array_merge(
            $fillStyle($C['green_dark']),
            ['font' => ['name' => 'Arial', 'bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']]],
            ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
        ));
        $height(1, 34);

        $merge('A2:J2');
        $setVal('A2', \App\Models\SystemConfig::getValue('company_name') . "  |  Kỳ lương: Tháng {$this->thang}/{$this->nam}");
        $style('A2')->applyFromArray(array_merge(
            $fillStyle($C['header_bg']),
            ['font' => ['name' => 'Arial', 'size' => 10, 'italic' => true, 'color' => ['argb' => 'FF' . $C['gray']]]],
            ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ));
        $height(2, 18);
        $height(3, 4);

        // ── Header cột ──────────────────────────────────
        $headers = [
            'A' => ['KỲ LƯƠNG', 12, $C['header_bg'], $C['dark']],
            'B' => ['NHÂN VIÊN', 30, $C['header_bg'], $C['dark']],
            'C' => ['LƯƠNG CƠ BẢN', 20, $C['header_bg'], $C['dark']],
            'D' => ['PHỤ CẤP', 17, $C['blue_light'], $C['blue_text']],
            'E' => ['KHẤU TRỪ', 17, $C['red_light'], $C['red_text']],
            'F' => ['BHXH', 15, $C['ins_bg'], $C['ins_text']],
            'G' => ['BHYT', 13, $C['ins_bg'], $C['ins_text']],
            'H' => ['BHTN', 13, $C['ins_bg'], $C['ins_text']],
            'I' => ['THUẾ TNCN', 15, $C['ins_bg'], $C['ins_text']],
            'J' => ['THỰC NHẬN', 20, $C['green_light'], $C['green_text']],
        ];

        foreach ($headers as $col => [$label, $width, $bg, $fg]) {
            $cell = "{$col}4";
            $setVal($cell, $label);
            $style($cell)->applyFromArray(array_merge(
                $fillStyle($bg),
                $fontStyle($fg, true),
                ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
                $thin,
            ));
            $ws->getColumnDimension($col)->setWidth($width);
        }
        $height(4, 22);

        // ── Dữ liệu ─────────────────────────────────────
        $grouped = $this->luongs
            ->sortBy(fn($l) => $l->nhanVien?->ttCongViec?->chucVu?->Ten ?? '—')
            ->groupBy(fn($l) => $l->nhanVien?->ttCongViec?->chucVu?->Ten ?? '—');

        $row = 5;

        foreach ($grouped as $chucVu => $items) {
            // Group header
            $merge("A{$row}:J{$row}");
            $setVal("A{$row}", "  ■  " . strtoupper($chucVu));
            $style("A{$row}")->applyFromArray(array_merge(
                $fillStyle($C['group_bg']),
                $fontStyle($C['dark'], true),
                ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER]],
                $thin,
            ));
            $height($row, 20);
            $row++;

            foreach ($items as $luong) {
                $nv = $luong->nhanVien;

                // Insurance breakdown
                $ins = $this->insuranceDetails[$nv?->id] ?? [];
                $bhxh = $bhyt = $bhtn = 0;
                foreach ($ins as $d) {
                    $t = strtoupper($d['ten'] ?? '');
                    if (str_contains($t, 'BHXH'))
                        $bhxh = $d['so_tien'];
                    if (str_contains($t, 'BHYT'))
                        $bhyt = $d['so_tien'];
                    if (str_contains($t, 'BHTN'))
                        $bhtn = $d['so_tien'];
                }
                $khauTru = -(($luong->KhauTruBaoHiem ?? 0) + ($luong->ThueTNCN ?? 0));

                // Kỳ lương
                $setVal("A{$row}", "{$this->thang}/{$this->nam}");
                $style("A{$row}")->applyFromArray(array_merge(
                    $fillStyle($C['white']),
                    $fontStyle($C['indigo'], true),
                    ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
                    $thin,
                ));

                // Nhân viên
                $setVal("B{$row}", ($nv?->Ten ?? '—') . "\n" . ($nv?->Ma ?? ''));
                $style("B{$row}")->applyFromArray(array_merge(
                    $fillStyle($C['white']),
                    $fontStyle($C['dark']),
                    ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true]],
                    $thin,
                ));

                // Số liệu
                $numCols = [
                    'C' => [$luong->LuongCoBan, $C['white'], $C['dark'], false, 10],
                    'D' => [$luong->PhuCap, $C['blue_light'], $C['blue_text'], false, 10],
                    'E' => [$khauTru, $C['red_light'], $C['red_text'], true, 10],
                    'F' => [$bhxh, $C['ins_bg'], $C['ins_text'], false, 9],
                    'G' => [$bhyt, $C['ins_bg'], $C['ins_text'], false, 9],
                    'H' => [$bhtn, $C['ins_bg'], $C['ins_text'], false, 9],
                    'I' => [$luong->ThueTNCN ?? 0, $C['ins_bg'], $C['ins_text'], false, 9],
                    'J' => [$luong->Luong, $C['green_light'], $C['green_text'], true, 11],
                ];

                foreach ($numCols as $col => [$val, $bg, $fg, $bold, $sz]) {
                    $cell = "{$col}{$row}";
                    $ws->setCellValue($cell, $val);
                    $ws->getStyle($cell)->getNumberFormat()->setFormatCode($numFmt);
                    $style($cell)->applyFromArray(array_merge(
                        $fillStyle($bg),
                        $fontStyle($fg, $bold, $sz),
                        ['alignment' => ['horizontal' => $col === 'J' ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER]],
                        $thin,
                    ));
                }

                $height($row, 26);
                $row++;
            }
        }

        // ── Tổng cộng ────────────────────────────────────
        $row++;
        $dataEnd = $row - 2;
        $merge("A{$row}:B{$row}");
        $setVal("A{$row}", 'TỔNG CỘNG');
        $style("A{$row}")->applyFromArray(array_merge(
            $fillStyle($C['green_dark']),
            ['font' => ['name' => 'Arial', 'bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']]],
            ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]],
            $thin,
        ));

        foreach (['C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
            $cell = "{$col}{$row}";
            $ws->setCellValue($cell, "=SUM({$col}5:{$col}{$dataEnd})");
            $ws->getStyle($cell)->getNumberFormat()->setFormatCode($numFmt);
            $isLast = $col === 'J';
            $style($cell)->applyFromArray(array_merge(
                $fillStyle($isLast ? $C['green_dark'] : $C['summary_bg']),
                $fontStyle($isLast ? $C['white'] : $C['dark'], true, 10),
                ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER]],
                $thin,
            ));
        }
        $height($row, 24);

        // ── Footer ───────────────────────────────────────
        $row += 2;
        $ws->setCellValue("A{$row}", 'Xuất ngày: ' . now()->format('d/m/Y H:i'));
        $ws->getStyle("A{$row}")->applyFromArray(
            $fontStyle($C['gray'], false, 9)
        );

        $ws->freezePane('A5');

        return $ss;
    }
}