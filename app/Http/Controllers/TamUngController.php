<?php

namespace App\Http\Controllers;

use App\Models\TamUng;
use App\Models\NhanVien;
use App\Models\HopDong;
use App\Models\PhuLucHopDong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TamUngController extends Controller
{
    /**
     * Danh sách yêu cầu tạm ứng
     */
    public function index(Request $request)
    {
        $query = TamUng::with(['nhanVien.ttCongViec.phongBan', 'nguoiDuyet'])->latest();

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai !== '') {
            $query->where('TrangThai', $request->trang_thai);
        }

        // Search by employee name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('nhanVien', function($q) use ($search) {
                $q->where('Ten', 'like', "%{$search}%")
                  ->orWhere('Ma', 'like', "%{$search}%");
            });
        }

        $tamUngs = $query->paginate(20);

        return view('tam_ung.index', compact('tamUngs'));
    }

    /**
     * Mở form tạo yêu cầu tạm ứng
     */
    public function create()
    {
        // Danh sách nhân viên đang có hợp đồng hiệu lực (Trạng thái = 1)
        $nhanViens = NhanVien::whereHas('hopDongs', function($q) {
            $q->conHieuLuc();
        })->get();

        return view('tam_ung.create', compact('nhanViens'));
    }

    /**
     * Lưu yêu cầu tạm ứng
     */
    public function store(Request $request)
    {
        $request->validate([
            'NhanVienId' => 'required|exists:nhan_viens,id',
            'SoTien' => 'required|numeric|min:1',
            'Lydo' => 'required|string|max:255',
        ], [
            'NhanVienId.required' => 'Vui lòng chọn nhân viên.',
            'SoTien.required' => 'Vui lòng nhập số tiền tạm ứng.',
            'SoTien.min' => 'Số tiền tạm ứng tối thiểu là 1 đồng.',
            'Lydo.required' => 'Vui lòng nhập lý do tạm ứng.',
        ]);

        $maxLimit = $this->getRemainingAdvanceLimit($request->NhanVienId);

        if ($request->SoTien > $maxLimit) {
            return redirect()->back()->withInput()->with('error', 'Số tiền tạm ứng (' . number_format($request->SoTien) . 'đ) vượt quá hạn mức còn lại cho phép trong tháng là ' . number_format($maxLimit) . 'đ.');
        }

        TamUng::create([
            'NhanVienId' => $request->NhanVienId,
            'SoTien' => $request->SoTien,
            'HanMuc' => $maxLimit,
            'TrangThai' => 0, // Chờ duyệt
            'Lydo' => $request->Lydo,
            'GhiChu' => $request->GhiChu,
        ]);

        return redirect()->route('tam-ung.index')->with('success', 'Đã tạo yêu cầu tạm ứng lương thành công.');
    }

    /**
     * Duyệt / Từ chối yêu cầu tạm ứng (Dành cho Admin/Người có quyền)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'TrangThai' => 'required|in:1,2',
            'GhiChu' => 'nullable|string'
        ]);

        $tamUng = TamUng::findOrFail($id);
        
        $tamUng->TrangThai = $request->TrangThai;
        $tamUng->GhiChu = $request->GhiChu;
        $tamUng->NguoiDuyetId = Auth::user()->nhanVien->id ?? null;
        $tamUng->save();

        $statusName = $request->TrangThai == 1 ? 'duyệt' : 'từ chối';

        return redirect()->route('tam-ung.index')->with('success', "Đã $statusName yêu cầu tạm ứng.");
    }

    /**
     * API: Lấy hạn mức tối đa cho 1 nhân viên
     */
    public function getMaxAdvanceAPI(Request $request)
    {
        $nhanVienId = $request->nhan_vien_id;
        
        if (!$nhanVienId) {
            return response()->json(['success' => false, 'message' => 'Missing nhan_vien_id'], 400);
        }

        $limit = $this->getRemainingAdvanceLimit($nhanVienId);

        return response()->json([
            'success' => true,
            'max_amount' => $limit,
            'formatted_amount' => number_format($limit, 0, ',', '.') . ' VNĐ'
        ]);
    }

    /**
     * Lấy hạn mức "thô" dựa trên hợp đồng (Mức lương tối đa được ứng)
     */
    private function getRawSalaryLimit($nhanVienId)
    {
        // 1. Lấy hợp đồng LĐ (không phải nda) gần nhất ĐANG CÓ HIỆU LỰC
        $activeContract = HopDong::where('NhanVienId', $nhanVienId)
            ->where('TrangThai', 1)
            ->where('Loai', 'not like', 'nda%')
            ->orderBy('NgayBatDau', 'desc')
            ->first();

        if (!$activeContract) {
            return 0;
        }

        // 2. Lấy phụ lục mới nhất có hiệu lực
        $latestAppendixLink = PhuLucHopDong::where('phu_luc_hop_dongs.HopDongGocId', $activeContract->id)
            ->join('hop_dongs', 'phu_luc_hop_dongs.HopDongPLId', '=', 'hop_dongs.id')
            ->where('hop_dongs.TrangThai', 1)
            ->orderBy('hop_dongs.NgayBatDau', 'desc')
            ->select('hop_dongs.TongLuong')
            ->first();

        if ($latestAppendixLink && $latestAppendixLink->TongLuong > 0) {
            return (float) $latestAppendixLink->TongLuong;
        }

        return (float) $activeContract->TongLuong;
    }

    /**
     * Tính hạn mức còn lại (Raw Limit - Các khoản đã ứng/đang chờ duyệt trong tháng)
     */
    private function getRemainingAdvanceLimit($nhanVienId)
    {
        // 1. Lấy hạn mức lương gốc
        $rawLimit = $this->getRawSalaryLimit($nhanVienId);
        
        if ($rawLimit <= 0) return 0;

        // 2. Tính tổng các khoản đã ứng trong tháng hiện tại (Chờ duyệt: 0, Đã duyệt: 1)
        // Loại bỏ các khoản bị Từ chối (2)
        $usedInMonth = TamUng::where('NhanVienId', $nhanVienId)
            ->whereIn('TrangThai', [0, 1])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('SoTien');

        $remaining = $rawLimit - $usedInMonth;

        return $remaining > 0 ? $remaining : 0;
    }
}
