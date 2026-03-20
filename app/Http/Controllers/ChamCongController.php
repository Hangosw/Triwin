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
        $totalEmployeesQuery = NhanVien::query();
        $onTimeQuery = ChamCong::whereYear('Vao', $year)->whereMonth('Vao', $month)->where('TrangThai', 'dung_gio');
        $lateQuery = ChamCong::whereYear('Vao', $year)->whereMonth('Vao', $month)->where('TrangThai', 'tre');
        $attendancesQuery = ChamCong::with('nhanVien.ttCongViec.phongBan')->whereYear('Vao', $year)->whereMonth('Vao', $month)->orderBy('Vao', 'desc');

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
        ]);

        $now = Carbon::now();
        $today = $now->toDateString();
        $nhanVienId = $request->nhan_vien_id;

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
            ->first();

        // Check if there is already a record for today
        $attendance = ChamCong::where('NhanVienId', $nhanVienId)
            ->whereDate('Vao', $today)
            ->orderBy('Vao', 'desc') // Lấy bản ghi mới nhất
            ->first();

        // Kiểm tra xem nhân viên có phiếu tăng ca được duyệt hôm nay không
        $approvedOT = \App\Models\TangCa::where('NhanVienId', $nhanVienId)
            ->where('Ngay', $today)
            ->where('TrangThai', 'da_duyet')
            ->first();

        if (!$attendance || ($attendance->Ra && $approvedOT && $attendance->Loai == 0)) {
            // CLOCK IN (Lần đầu hoặc bắt đầu ca tăng ca sau khi đã kết thúc ca HC)
            $status = 'dung_gio';
            $loai = 0; // Mặc định là hành chính
            $tangCaId = null;

            // Nếu đã có ca HC rồi thì ca này mặc định là tăng ca
            if ($attendance && $attendance->Ra) {
                $loai = 1;
                $tangCaId = $approvedOT->id;
            } else {
                // Kiểm tra trễ giờ hành chính
                if ($now->toTimeString() > $startWorkTime) {
                    $status = 'tre';
                }
            }

            ChamCong::create([
                'NhanVienId' => $nhanVienId,
                'Vao' => $now,
                'Loai' => $loai,
                'TangCaId' => $tangCaId,
                'TrangThai' => $status
            ]);

            $msg = $loai == 1 ? 'Vào ca TĂNG CA' : 'Vào làm';
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

            // XỬ LÝ SPLIT CA (Case 2: Làm một mạch từ sáng đến hết ca tăng ca)
            // Nếu bản ghi hiện tại là HC (Loai=0) và có approvedOT
            if ($attendance->Loai == 0 && $approvedOT) {
                $otStartTime = Carbon::parse($today . ' ' . $approvedOT->BatDau);
                
                // Nếu giờ ra hiện tại sau giờ bắt đầu tăng ca -> Thực hiện tách ca
                if ($now->greaterThan($otStartTime)) {
                    // 1. Kết thúc ca HC tại thời điểm bắt đầu tăng ca
                    $currentStatus = $attendance->TrangThai;
                    if ($currentStatus !== 'tre') {
                        // Ca HC coi như đúng giờ nếu làm đến tận lúc tăng ca
                        $currentStatus = 'dung_gio';
                    }

                    $attendance->update([
                        'Ra' => $otStartTime,
                        'TrangThai' => $currentStatus
                    ]);

                    // 2. Tạo bản ghi mới cho ca Tăng ca (từ otStartTime đến bây giờ)
                    ChamCong::create([
                        'NhanVienId' => $nhanVienId,
                        'Vao' => $otStartTime,
                        'Ra' => $now,
                        'Loai' => 1,
                        'TangCaId' => $approvedOT->id,
                        'TrangThai' => 'dung_gio'
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Hệ thống đã tự động ghi nhận 2 ca (Hành chính & Tăng ca). Giờ về cuối cùng: ' . $now->format('H:i:s'),
                        'type' => 'ra'
                    ]);
                }
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

        // Lấy danh sách chấm công hôm nay của nhân viên này
        $todayAttendances = ChamCong::where('NhanVienId', $nhanVien->id)
            ->whereDate('Vao', Carbon::today())
            ->orderBy('Vao', 'desc')
            ->get();

        $latestAttendance = $todayAttendances->first();

        // Kiểm tra xem nhân viên có phiếu tăng ca được duyệt hôm nay không
        $approvedOT = \App\Models\TangCa::where('NhanVienId', $nhanVien->id)
            ->where('Ngay', Carbon::today())
            ->where('TrangThai', 'da_duyet')
            ->first();

        return view('attendance.self', compact('nhanVien', 'todayAttendances', 'latestAttendance', 'approvedOT'));
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
}
