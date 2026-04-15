<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IncrementAvailableLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:increment-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động cộng thêm 1 ngày phép khả dụng cho nhân viên vào đầu tháng';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentYear = now()->year;
        
        // Lấy tất cả các bản ghi quản lý phép năm của nhân viên đang làm việc trong năm hiện tại
        $records = \App\Models\QuanLyPhepNam::where('Nam', $currentYear)
            ->whereHas('nhanVien', function ($query) {
                // Chỉ cộng cho nhân viên đang làm việc (TrangThai = 'dang_lam')
                $query->where('TrangThai', 'dang_lam');
            })
            ->get();

        $count = 0;
        foreach ($records as $record) {
            $record->KhaDung += 1.0;
            // Đảm bảo không vượt quá tổng phép năm (tùy chọn, nhưng thường là có giới hạn)
            if ($record->KhaDung > $record->ConLai) {
                // $record->KhaDung = $record->ConLai; // Có thể giới hạn nếu muốn
            }
            $record->save();
            $count++;
        }

        $this->info("Đã cộng thêm 1 ngày phép cho {$count} nhân viên.");
    }
}
