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
    public function ChamCongData(Request $request, $id)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;

        $attendances = ChamCong::where('NhanVienId', $id)
            ->whereYear('Vao', $year)
            ->whereMonth('Vao', $month)
            ->orderBy('Vao', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'attendances' => $attendances
        ]);
    }

    public function DanhSachView(Request $request)
    {
        $month = $request->month ?? Carbon::now()->month;
        $year = $request->year ?? Carbon::now()->year;
        $day = $request->day ?? '';      // '' = tất cả ngày
        $status = $request->status ?? '';      // '' = tất cả trạng thái
        $tab = $request->get('tab', 'nhan_vien'); // 'nhan_vien' hoặc 'khach'

        $user = auth()->user();
        $isEmployeeOnly = $user->hasAnyRole(['Employee', 'Nhân viên']) && !$user->hasAnyRole(['Super Admin', 'Admin Đơn Vị', 'CEO', 'Supervisor', 'HR Manager', 'System Admin', 'Factory Supervisor', 'Line Manager']);
        $nhanVienId = $user->nhanVien?->id;

        // Stats for the month (luôn theo tháng/năm, không lọc thêm ngày/trạng thái)
        $totalEmployeesQuery = NhanVien::query();
        $onTimeQuery = ChamCong::whereYear('Vao', $year)->whereMonth('Vao', $month)->where('TrangThai', 'dung_gio');
        $lateQuery = ChamCong::whereYear('Vao', $year)->whereMonth('Vao', $month)->where('TrangThai', 'tre');

        // Tab Counts Query
        $baseQuery = ChamCong::whereYear('Vao', $year)
            ->whereMonth('Vao', $month);
        
        if ($day !== '' && $day > 0) {
            $baseQuery->whereDay('Vao', $day);
        }

        if ($isEmployeeOnly && $nhanVienId) {
            $baseQuery->where('NhanVienId', $nhanVienId);
        }

        $employeeTabCount = (clone $baseQuery)->whereNotNull('NhanVienId')->count();
        $guestTabCount = (clone $baseQuery)->whereNull('NhanVienId')->count();

        // Main query
        $attendancesQuery = ChamCong::with('nhanVien.ttCongViec.phongBan')
            ->whereYear('Vao', $year)
            ->whereMonth('Vao', $month);

        // Lọc theo ngày cụ thể
        if ($day !== '' && $day > 0) {
            $attendancesQuery->whereDay('Vao', $day);
        }

        // Lọc theo Tab
        if ($tab === 'khach') {
            $attendancesQuery->whereNull('NhanVienId');
        } else {
            $attendancesQuery->whereNotNull('NhanVienId');
            // Lọc theo trạng thái (chỉ áp dụng cho nhân viên)
            if ($status !== '') {
                $attendancesQuery->where('TrangThai', $status);
            }
        }

        $attendancesQuery->orderBy('Vao', 'desc');

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

        return view('attendance.index', compact(
            'attendances',
            'totalEmployees',
            'onTimeCount',
            'lateCount',
            'month',
            'year',
            'day',
            'status',
            'tab',
            'employeeTabCount',
            'guestTabCount'
        ));
    }

    /**
     * View for clocking in/out
     */
    public function TaoView()
    {
        // Get all employees for the dropdown
        $nhanViens = NhanVien::orderBy('Ten')->get();

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
            'anh_cham_cong' => 'nullable|string', // Base64 string
        ]);

        $now = Carbon::now();
        $today = $now->toDateString();
        $nhanVienId = $request->nhan_vien_id;

        // Process Image if present
        $imagePath = null;
        if ($request->anh_cham_cong) {
            $imageData = $request->anh_cham_cong;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('Kiểu file ảnh không hợp lệ.');
                }

                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    throw new \Exception('Dữ liệu ảnh không hợp lệ.');
                }
            } else {
                throw new \Exception('Định dạng ảnh không đúng.');
            }

            $fileName = 'attendance_' . $nhanVienId . '_' . time() . '.' . $type;
            $imagePath = 'uploads/attendances/' . $fileName;

            if (!file_exists(public_path('uploads/attendances'))) {
                mkdir(public_path('uploads/attendances'), 0777, true);
            }

            file_put_contents(public_path($imagePath), $imageData);
        }

        // Lấy ca làm việc từ danh mục ca làm việc (không phụ thuộc lịch làm việc)
        $caLamViec = \App\Models\DmCaLamViec::first();

        if (!$caLamViec) {
            return response()->json([
                'success' => false,
                'message' => 'Hệ thống chưa thiết lập ca làm việc nào trong danh mục.'
            ]);
        }
        $gioVao = Carbon::parse($caLamViec->GioVao);
        $gioRa = Carbon::parse($caLamViec->GioRa);

        // Adjust for overnight shift
        if ($caLamViec->LaCaQuaDem) {
            if ($gioRa->lessThan($gioVao)) {
                $gioRa->addDay();
            }
        }

        // Setup work times based on the shift
        $startWorkTime = $caLamViec->GioVao;
        $endWorkTime = $caLamViec->GioRa;

        // Check if there is already a record for today
        $attendance = ChamCong::where('NhanVienId', $nhanVienId)
            ->whereDate('Vao', $today)
            ->orderBy('Vao', 'desc') // Lấy bản ghi mới nhất
            ->first();



        if (!$attendance) {
            // CLOCK IN (Lần đầu hoặc bắt đầu ca tăng ca sau khi đã kết thúc ca HC)
            $status = 'dung_gio';
            $loai = 0; // Mặc định là hành chính
            // Kiểm tra trễ giờ hành chính
            if ($now->toTimeString() > $startWorkTime) {
                $status = 'tre';
            }

            ChamCong::create([
                'NhanVienId' => $nhanVienId,
                'Vao' => $now,
                'Loai' => 0,
                'TrangThai' => $status,
                'AnhChamCong' => $imagePath // Lưu ảnh vào đây
            ]);

            $msg = 'vào làm';
            return response()->json([
                'success' => true,
                'message' => "Bạn đã ghi nhận {$msg} lúc " . $now->format('H:i:s'),
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



            // Clock out bình thường (Hoặc kết thúc ca tăng ca riêng biệt)
            $currentStatus = $attendance->TrangThai;
            if ($attendance->Loai == 0) {
                if ($currentStatus !== 'tre') {
                    if ($now->toTimeString() >= $endWorkTime) {
                        $currentStatus = 'dung_gio';
                    } else {
                        $currentStatus = 've_som';
                    }
                }
            } else {
                $currentStatus = 'dung_gio'; // Mặc định tăng ca là đúng giờ
            }

            $attendance->update([
                'Ra' => $now,
                'TrangThai' => $currentStatus,
                'AnhChamCong' => $imagePath // Cập nhật ảnh (thường là ghi đè hoặc giữ nguyên tùy theo logic check-out)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bạn đã ghi nhận ra về lúc ' . $now->format('H:i:s'),
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

        // Lấy danh sách chấm công hôm nay của nhân viên này
        $todayAttendances = ChamCong::where('NhanVienId', $nhanVien->id)
            ->whereDate('Vao', Carbon::today())
            ->orderBy('Vao', 'desc')
            ->get();

        $latestAttendance = $todayAttendances->first();



        return view('attendance.self', compact('nhanVien', 'todayAttendances', 'latestAttendance'));
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

    public function TongQuanNgayView(Request $request)
    {
        $date = $request->date ?? Carbon::today()->toDateString();
        $dateObj = Carbon::parse($date);

        $user = auth()->user();
        $isEmployeeOnly = $user->hasAnyRole(['Employee', 'Nhân viên']) && !$user->hasAnyRole(['Super Admin', 'Admin Đơn Vị', 'CEO', 'Supervisor', 'HR Manager', 'System Admin', 'Factory Supervisor', 'Line Manager']);

        // 1. Lấy ca làm việc chuẩn để tính đi sớm/trễ (08:30 - 17:30)
        $caLamViec = \App\Models\DmCaLamViec::first();
        $gioVaoChuan = $caLamViec ? $caLamViec->GioVao : '08:30:00';
        $gioRaChuan = $caLamViec ? $caLamViec->GioRa : '17:30:00';

        // 2. Lấy toàn bộ nhân viên (hoặc theo quyền hạn)
        $nhanVienQuery = NhanVien::query();
        if ($isEmployeeOnly) {
            $nhanVienQuery->where('id', $user->nhanVien?->id);
        }
        $allEmployees = $nhanVienQuery->get();
        $totalEmployeeCount = $allEmployees->count();

        // 3. Lấy dữ liệu chấm công của ngày chọn
        $attendances = ChamCong::with('nhanVien')
            ->whereDate('Vao', $date)
            ->get();

        // 4. Phân loại nhân viên
        $checkedInIds = $attendances->pluck('NhanVienId')->filter()->unique()->toArray();

        // - Đi sớm: Vao < giờ vào chuẩn
        $diSom = $attendances->filter(function ($att) use ($gioVaoChuan) {
            return $att->Vao && $att->Vao->toTimeString() < $gioVaoChuan && $att->NhanVienId;
        })->unique('NhanVienId');

        // - Đi trễ: Vao > giờ vào chuẩn
        $diTre = $attendances->filter(function ($att) use ($gioVaoChuan) {
            return $att->Vao && $att->Vao->toTimeString() > $gioVaoChuan && $att->NhanVienId;
        })->unique('NhanVienId');

        // - Chưa Checkin: Nhân viên không có trong bản ghi chấm công ngày đó
        $chuaCheckin = $allEmployees->whereNotIn('id', $checkedInIds);

        // - Về trễ: Ra > giờ ra chuẩn
        $veTre = $attendances->filter(function ($att) use ($gioRaChuan) {
            return $att->Ra && $att->Ra->toTimeString() > $gioRaChuan && $att->NhanVienId;
        })->unique('NhanVienId');

        // - Khách / Lạ (không có NhanVienId)
        $khachLa = $attendances->whereNull('NhanVienId');

        // Thống kê thẻ tóm tắt
        $checkedInCount = count($checkedInIds);

        return view('attendance.daily_overview', compact(
            'date',
            'dateObj',
            'totalEmployeeCount',
            'checkedInCount',
            'diSom',
            'diTre',
            'chuaCheckin',
            'veTre',
            'khachLa',
            'gioVaoChuan',
            'gioRaChuan'
        ));
    }
}
