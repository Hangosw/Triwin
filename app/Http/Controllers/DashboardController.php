<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\DmPhongBan;
use App\Models\HopDong;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        return view('dashboard.dashboard', compact(
            'totalEmployees',
            'totalDepartments',
            'totalContracts',
            'expiringContractsCount',
            'missingAttendanceCount',
            'pendingLeaveCount',
            'pendingOvertimeCount'
        ));
    }
}
