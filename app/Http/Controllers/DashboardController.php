<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\DmPhongBan;
use App\Models\HopDong;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayMail;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = NhanVien::count();
        $totalDepartments = DmPhongBan::count();
        $totalContracts = HopDong::where('TrangThai', 1)->count();

        // Hợp đồng sắp hết hạn (trong vòng 25 ngày kể từ hôm nay)
        $today = Carbon::today();
        $twentyFiveDaysLater = Carbon::today()->addDays(25);
        
        $expiringContractsCount = HopDong::where('TrangThai', 1)
            ->whereNotNull('NgayKetThuc')
            ->where('NgayKetThuc', '>=', $today)
            ->where('NgayKetThuc', '<=', $twentyFiveDaysLater)
            ->count();

        // Nhắc nhở chấm công (nhân viên chưa chấm công hôm nay)
        $clockedInTodayIds = \App\Models\ChamCong::whereDate('Vao', $today)
            ->pluck('NhanVienId')
            ->unique();
        $missingAttendanceCount = NhanVien::whereNotIn('id', $clockedInTodayIds)->count();

        // Đơn nghỉ phép chờ duyệt (TrangThai = 2)
        $pendingLeaveCount = \App\Models\DangKyNghiPhep::where('TrangThai', 2)->count();

        // Phiếu tăng ca chờ duyệt (TrangThai = 'dang_cho')
        $pendingOvertimeCount = \App\Models\TangCa::where('TrangThai', 'dang_cho')->count();

        // Sinh nhật hôm nay
        $todayMonth = $today->month;
        $todayDay   = $today->day;
        $birthdayEmployees = NhanVien::whereNotNull('NgaySinh')
            ->whereMonth('NgaySinh', $todayMonth)
            ->whereDay('NgaySinh', $todayDay)
            ->get();

        // Nếu là Admin, hiển thị Dashboard tổng quát
        if (auth()->user()->hasRole('Super Admin')) {
            return view('dashboard.dashboard', compact(
                'totalEmployees',
                'totalDepartments',
                'totalContracts',
                'expiringContractsCount',
                'missingAttendanceCount',
                'pendingLeaveCount',
                'pendingOvertimeCount',
                'birthdayEmployees'
            ));
        }

        // Nếu là Nhân viên, hiển thị Dashboard cá nhân
        $nv = auth()->user()->nhanVien;
        if (!$nv) {
            // Mặc định ném về màn hình nào đó nếu tài khoản không liên kết nhân viên (VD: admin mới)
            return view('dashboard.dashboard', compact(
                'totalEmployees', 'totalDepartments', 'totalContracts', 'expiringContractsCount',
                'missingAttendanceCount', 'pendingLeaveCount', 'pendingOvertimeCount', 'birthdayEmployees'
            ));
        }

        $nvId = $nv->id;
        
        // Đơn xin nghỉ phép đang chờ
        $myPendingLeaveCount = \App\Models\DangKyNghiPhep::where('NhanVienId', $nvId)
                            ->where('TrangThai', 2)
                            ->count();
                            
        // Bảng tăng ca đang chờ
        $myPendingOvertimeCount = \App\Models\TangCa::where('NhanVienId', $nvId)
                            ->where('TrangThai', 'dang_cho')
                            ->count();
                            
        // Giờ tăng ca đã duyệt trong tháng này
        $myOtHoursThisMonth = \App\Models\TangCa::where('NhanVienId', $nvId)
                            ->where('TrangThai', 'da_duyet')
                            ->whereMonth('Ngay', $today->month)
                            ->whereYear('Ngay', $today->year)
                            ->sum('Tong');
                            
        // Lịch sử lương (6 tháng gần nhất)
        $latestSalaries = \App\Models\Luong::where('NhanVienId', $nvId)
                            ->orderBy('ThoiGian', 'desc')
                            ->take(6)
                            ->get();
                            
        // Đơn từ gần đây (Lấy 5 đơn nghỉ phép và 5 đơn tăng ca mới nhất để gộp)
        $recentLeaves = \App\Models\DangKyNghiPhep::where('NhanVienId', $nvId)->orderBy('created_at', 'desc')->take(5)->get();
        $recentOTs = \App\Models\TangCa::where('NhanVienId', $nvId)->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.employee', compact(
            'nv',
            'myPendingLeaveCount',
            'myPendingOvertimeCount',
            'myOtHoursThisMonth',
            'latestSalaries',
            'recentLeaves',
            'recentOTs'
        ));
    }

    /**
     * Gửi email chúc mừng sinh nhật cho nhân viên
     */
    public function GuiBirthdayMail(Request $request, $id)
    {
        $nv = NhanVien::find($id);

        if (!$nv) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy nhân viên.']);
        }

        if (empty($nv->Email)) {
            return response()->json(['success' => false, 'message' => "Nhân viên {$nv->Ten} chưa có địa chỉ email."]);
        }

        try {
            Mail::to($nv->Email)->queue(new BirthdayMail($nv));
            \Log::info("Birthday email queued for {$nv->Ten} ({$nv->Email})");
            return response()->json(['success' => true, 'message' => "Đã gửi email chúc mừng sinh nhật đến {$nv->Ten}!"]);
        } catch (\Exception $e) {
            \Log::error("Failed to send birthday email to {$nv->Email}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi gửi email: ' . $e->getMessage()]);
        }
    }
}
