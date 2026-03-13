<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChamCong;
use App\Models\NhanVien;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ChamCongImport;

class ChamCongController extends Controller
{
    public function DanhSachView(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $user = auth()->user();
        $isEmployeeOnly = $user->hasAnyRole(['Employee', 'Nhân viên']) && !$user->hasAnyRole(['Super Admin', 'Admin Đơn Vị', 'CEO', 'Supervisor', 'HR Manager', 'System Admin', 'Factory Supervisor', 'Line Manager']);
        $nhanVienId = $user->nhanVien?->id;

        // Stats for the month
        $totalEmployeesQuery = NhanVien::byUnit();
        $onTimeQuery = ChamCong::byUnit()->whereYear('Vao', $year)->whereMonth('Vao', $month)->where('TrangThai', 'dung_gio');
        $lateQuery = ChamCong::byUnit()->whereYear('Vao', $year)->whereMonth('Vao', $month)->where('TrangThai', 'tre');
        $attendancesQuery = ChamCong::byUnit()->with('nhanVien.ttCongViec.phongBan')->whereYear('Vao', $year)->whereMonth('Vao', $month)->orderBy('Vao', 'desc');

        if ($isEmployeeOnly && $nhanVienId) {
            $totalEmployeesQuery->where('id', $nhanVienId);
            $onTimeQuery->where('NhanVienId', $nhanVienId);
            $lateQuery->where('NhanVienId', $nhanVienId);
            $attendancesQuery->where('NhanVienId', $nhanVienId);
        }

        $totalEmployees = $totalEmployeesQuery->count();
        $onTimeCount = $onTimeQuery->count();
        $lateCount = $lateQuery->count();
        $attendances = $attendancesQuery->get();

        return view('attendance.index', compact('attendances', 'totalEmployees', 'onTimeCount', 'lateCount', 'month', 'year'));
    }

    /**
     * View for clocking in/out
     */
    public function TaoView()
    {
        // Get all employees for the dropdown
        $nhanViens = NhanVien::byUnit()->orderBy('Ten')->get();

        // Get today's attendance records to show recent activity
        $todayAttendances = ChamCong::whereDate('Vao', Carbon::today())
            ->with('nhanVien')
            ->orderBy('Vao', 'desc')
            ->get();

        return view('attendance.create', compact('nhanViens', 'todayAttendances'));
    }

    /**
     * Handle clock-in/out logic
     */
    public function Tao(Request $request)
    {
        $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
        ]);

        $now = Carbon::now();
        $today = $now->toDateString();
        $nhanVienId = $request->nhan_vien_id;

        // Check if employee has a schedule today
        $lichLamViec = \App\Models\LichLamViec::with('caLamViec')
            ->where('NhanVienId', $nhanVienId)
            ->whereDate('NgayLamViec', $today)
            ->first();

        if (!$lichLamViec || !$lichLamViec->caLamViec) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có ca nào hôm nay để chấm công.'
            ]);
        }

        $caLamViec = $lichLamViec->caLamViec;
        $gioVao = Carbon::parse($caLamViec->GioVao);
        $gioRa = Carbon::parse($caLamViec->GioRa);

        // Adjust for overnight shift
        if ($caLamViec->LaCaQuaDem) {
            if ($gioRa->lessThan($gioVao)) {
                $gioRa->addDay();
            }
        }

        // Validate if current time is within shift limits (allow 60 mins before and after)
        $allowedStart = (clone $gioVao)->subMinutes(60);
        $allowedEnd = (clone $gioRa)->addMinutes(60);

        // Ensure $now has the same date context for comparison
        $currentTime = Carbon::parse($now->format('H:i:s'));

        // For overnight shifts, if the current time is very early morning (e.g. 01:00 AM) and shift started yesterday,
        // we need to add a day to the $currentTime for proper comparison
        if ($caLamViec->LaCaQuaDem && $currentTime->hour < 12) {
            $currentTime->addDay();
        }

        if (!$currentTime->between($allowedStart, $allowedEnd)) {
            return response()->json([
                'success' => false,
                'message' => 'Hiện tại không nằm trong thời gian cho phép chấm công của ca làm việc (' . $caLamViec->TenCa . ').'
            ]);
        }

        // Setup work times based on the shift
        $startWorkTime = $caLamViec->GioVao;
        $endWorkTime = $caLamViec->GioRa;

        // Check if there is already a record for today
        $attendance = ChamCong::where('NhanVienId', $nhanVienId)
            ->whereDate('Vao', $today)
            ->first();

        if (!$attendance) {
            // CLOCK IN
            $status = 'dung_gio';
            if ($now->toTimeString() > $startWorkTime) {
                $status = 'tre';
            }

            ChamCong::create([
                'NhanVienId' => $nhanVienId,
                'Vao' => $now,
                'TrangThai' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bạn đã ghi nhận Vào làm lúc ' . $now->format('H:i:s'),
                'type' => 'vao'
            ]);
        } else {
            // CLOCK OUT
            if ($attendance->Ra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã hoàn thành chấm công ngày hôm nay!'
                ]);
            }

            $currentStatus = $attendance->TrangThai; // Keep if already 'tre'

            // If they clocked in on time but are leaving early
            if ($currentStatus === 'dung_gio' && $now->toTimeString() < $endWorkTime) {
                $currentStatus = 've_som';
            }

            $attendance->update([
                'Ra' => $now,
                'TrangThai' => $currentStatus
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bạn đã ghi nhận Ra về lúc ' . $now->format('H:i:s'),
                'type' => 'ra'
            ]);
        }
    }

    /**
     * View cho cá nhân tự chấm công
     */
    public function CaNhanTaoView()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $nhanVien = $user->nhanVien;

        if (!$nhanVien) {
            return view('attendance.self', ['error' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên.']);
        }

        // Lấy thông tin chấm công hôm nay của nhân viên này
        $todayAttendance = ChamCong::where('NhanVienId', $nhanVien->id)
            ->whereDate('Vao', Carbon::today())
            ->first();

        return view('attendance.self', compact('nhanVien', 'todayAttendance'));
    }

    /**
     * Xử lý cá nhân tự chấm công
     */
    public function CaNhanTao(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện chức năng này.'
            ]);
        }
        $nhanVien = $user->nhanVien;

        if (!$nhanVien) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên.'
            ]);
        }

        // Ghi đè nhan_vien_id trong request để sử dụng logic của hàm Tao
        $request->merge(['nhan_vien_id' => $nhanVien->id]);

        return $this->Tao($request);
    }

    /**
     * View Lịch làm việc (Schedule Matrix for Teams)
     */
    public function schedule(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        // Lấy danh sách Tổ đội kèm phòng ban
        $toDois = \App\Models\DmToDoi::with('phongBan')->orderBy('Ten')->get();

        // Lấy danh sách tất cả các ca làm việc
        $caLamViecs = \App\Models\DmCaLamViec::all();

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Lấy danh sách lịch làm việc đã lưu của tháng này
        $schedules = \App\Models\LichLamViec::with('caLamViec')
            ->whereYear('NgayLamViec', $year)
            ->whereMonth('NgayLamViec', $month)
            ->get();

        $scheduleMap = [];
        foreach ($schedules as $schedule) {
            if ($schedule->ToDoiId) {
                // Formatting date to group by team and date
                $dateStr = Carbon::parse($schedule->NgayLamViec)->format('Y-m-d');
                $scheduleMap[$schedule->ToDoiId][$dateStr] = $schedule->caLamViec ? $schedule->caLamViec->MaCa : '';
            }
        }

        return view('attendance.schedule', compact('toDois', 'caLamViecs', 'month', 'year', 'daysInMonth', 'scheduleMap'));
    }

    /**
     * Lưu lịch làm việc via AJAX (Tổ đội mass save)
     */
    public function saveSchedule(Request $request)
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.ToDoiId' => 'required|exists:dm_to_dois,id',
            'schedules.*.NgayLamViec' => 'required|date',
            'schedules.*.CaId' => 'nullable|string',
        ]);

        foreach ($request->schedules as $item) {
            $toDoiId = $item['ToDoiId'];
            $ngayLamViec = $item['NgayLamViec'];
            $caId = $item['CaId'];

            if (empty($caId) || strtoupper($caId) === 'OFF') {
                // Delete records if empty selection or OFF
                \App\Models\LichLamViec::where('ToDoiId', $toDoiId)
                    ->whereDate('NgayLamViec', $ngayLamViec)
                    ->delete();
            } else {
                $dbCaId = \App\Models\DmCaLamViec::where('MaCa', $caId)->value('id');
                if ($dbCaId) {
                    // Update or Create the schedule cho Tổ Đội (bỏ qua NhanVienId)
                    \App\Models\LichLamViec::updateOrCreate(
                        [
                            'ToDoiId' => $toDoiId,
                            'NgayLamViec' => $ngayLamViec,
                        ],
                        [
                            'CaId' => $dbCaId,
                            'NhanVienId' => null,
                        ]
                    );
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Đã lưu lịch làm việc thành công']);
    }
    public function importView()
    {
        return view('attendance.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Vui lòng chọn file Excel.',
            'file.mimes' => 'File phải có định dạng xlsx, xls hoặc csv.',
            'file.max' => 'Dung lượng file không được vượt quá 2MB.',
        ]);

        try {
            $import = new ChamCongImport();
            Excel::import($import, $request->file('file'));

            if (count($import->errors) > 0) {
                return back()->with('import_errors', $import->errors)
                    ->with('import_success_count', $import->successCount);
            }

            return redirect()->route('cham-cong.danh-sach')
                ->with('success', "Đã import thành công {$import->successCount} bản ghi chấm công!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi import file: ' . $e->getMessage());
        }
    }
}
