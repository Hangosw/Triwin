<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkFromHome;
use App\Models\NhanVien;
use App\Models\DmPhongBan;
use Carbon\Carbon;

class WorkFromHomeController extends Controller
{
    /**
     * View danh sách WFH cho Admin
     */
    public function DanhSachView(Request $request)
    {
        $phongBanId = $request->phong_ban_id;
        $trangThai = $request->trang_thai;
        $query = WorkFromHome::with(['nhanVien.ttCongViec.phongBan']);

        if ($phongBanId) {
            $query->whereHas('nhanVien.ttCongViec', function ($q) use ($phongBanId) {
                $q->where('PhongBanId', $phongBanId);
            });
        }

        if ($trangThai) {
            $query->where('TrangThai', $trangThai);
        }

        $wfhRequests = $query->orderBy('created_at', 'desc')->get();

        $pendingCount = WorkFromHome::where('TrangThai', 'dang_cho')->count();
        $approvedCount = WorkFromHome::where('TrangThai', 'da_duyet')->count();
        $rejectedCount = WorkFromHome::where('TrangThai', 'tu_choi')->count();

        $phongBans = DmPhongBan::with('ttNhanVienCongViec.nhanVien')->get();

        return view('wfh.index', compact(
            'wfhRequests',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'phongBans'
        ));
    }

    /**
     * View cho cá nhân nhân viên đăng ký WFH
     */
    public function CaNhanView()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $nhanVien = $user->nhanVien;

        if (!$nhanVien) {
            return view('wfh.self', ['error' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên.']);
        }

        $myWfhs = WorkFromHome::where('NhanVienId', $nhanVien->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('wfh.self', compact('nhanVien', 'myWfhs'));
    }

    /**
     * Xử lý gửi đơn đăng ký WFH
     */
    public function TaoMoi(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.']);
        }

        $nhanVienId = $request->NhanVienId ?: ($user->nhanVien ? $user->nhanVien->id : null);

        if (!$nhanVienId) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin nhân viên.']);
        }

        // Chuẩn hóa ngày
        $batDauStr = $request->NgayBatDau;
        $ketThucStr = $request->NgayKetThuc;

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $batDauStr)) {
            $batDau = Carbon::createFromFormat('d/m/Y', $batDauStr)->startOfDay();
        } else {
            $batDau = Carbon::parse($batDauStr)->startOfDay();
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $ketThucStr)) {
            $ketThuc = Carbon::createFromFormat('d/m/Y', $ketThucStr)->startOfDay();
        } else {
            $ketThuc = Carbon::parse($ketThucStr)->startOfDay();
        }

        if ($batDau > $ketThuc) {
            return response()->json(['success' => false, 'message' => 'Ngày bắt đầu không được sau ngày kết thúc.']);
        }

        // Tính số ngày (bao gồm cả ngày bắt đầu và kết thúc)
        $soNgay = $batDau->diffInDays($ketThuc) + 1;

        try {
            WorkFromHome::create([
                'NhanVienId' => $nhanVienId,
                'NgayBatDau' => $batDau->format('Y-m-d'),
                'NgayKetThuc' => $ketThuc->format('Y-m-d'),
                'Ngay' => $soNgay,
                'LyDo' => $request->LyDo,
                'GhiChu' => $request->GhiChu,
                'TrangThai' => 'dang_cho'
            ]);

            return response()->json(['success' => true, 'message' => 'Đăng ký WFH thành công!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Phê duyệt đơn WFH
     */
    public function Duyet(Request $request, $id)
    {
        $nhanVien = auth()->user()?->nhanVien;
        
        $wfh = WorkFromHome::findOrFail($id);
        $wfh->update([
            'TrangThai' => 'da_duyet',
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null,
            'NgayDuyet' => now()->format('Y-m-d'),
            'GhiChu' => $request->GhiChu // Cập nhật ghi chú lãnh đạo nếu có
        ]);

        return response()->json(['success' => true, 'message' => 'Đã phê duyệt đơn WFH.']);
    }

    /**
     * Từ chối đơn WFH
     */
    public function TuChoi(Request $request, $id)
    {
        $nhanVien = auth()->user()?->nhanVien;
        
        $wfh = WorkFromHome::findOrFail($id);
        $wfh->update([
            'TrangThai' => 'tu_choi',
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null,
            'NgayDuyet' => now()->format('Y-m-d'),
            'GhiChu' => $request->GhiChu
        ]);

        return response()->json(['success' => true, 'message' => 'Đã từ chối đơn WFH.']);
    }

    /**
     * Phê duyệt nhiều đơn WFH
     */
    public function DuyetNhieu(Request $request)
    {
        $nhanVien = auth()->user()?->nhanVien;
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một phiếu.']);
        }

        WorkFromHome::whereIn('id', $ids)->where('TrangThai', 'dang_cho')->update([
            'TrangThai' => 'da_duyet',
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null,
            'NgayDuyet' => now()->format('Y-m-d')
        ]);

        return response()->json(['success' => true, 'message' => 'Đã phê duyệt các phiếu đã chọn.']);
    }

    /**
     * Từ chối nhiều đơn WFH
     */
    public function TuChoiNhieu(Request $request)
    {
        $nhanVien = auth()->user()?->nhanVien;
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một phiếu.']);
        }

        WorkFromHome::whereIn('id', $ids)->where('TrangThai', 'dang_cho')->update([
            'TrangThai' => 'tu_choi',
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null,
            'NgayDuyet' => now()->format('Y-m-d')
        ]);

        return response()->json(['success' => true, 'message' => 'Đã từ chối các phiếu đã chọn.']);
    }
}
