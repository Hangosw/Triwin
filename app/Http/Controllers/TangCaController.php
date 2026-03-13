<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TangCa;
use App\Models\NhanVien;
use Carbon\Carbon;

class TangCaController extends Controller
{
    /**
     * View danh sách tăng ca cho Admin
     */
    public function DanhSachView(Request $request)
    {
        $phongBanId = $request->phong_ban_id;
        $trangThai = $request->trang_thai;
        $query = TangCa::with(['nhanVien.ttCongViec.phongBan'])->byUnit();

        if ($phongBanId) {
            $query->whereHas('nhanVien.ttCongViec', function ($q) use ($phongBanId) {
                $q->where('PhongBanId', $phongBanId);
            });
        }

        if ($trangThai) {
            $query->where('TrangThai', $trangThai);
        }

        $tangCas = $query->orderBy('Ngay', 'desc')->get();

        // Stats
        $now = Carbon::now();
        $totalHoursThisMonth = TangCa::byUnit()->whereYear('Ngay', $now->year)
            ->whereMonth('Ngay', $now->month)
            ->where('TrangThai', 'da_duyet')
            ->sum('Tong');

        $pendingCount = TangCa::byUnit()->where('TrangThai', 'dang_cho')->count();
        $approvedCount = TangCa::byUnit()->where('TrangThai', 'da_duyet')->count();
        $rejectedCount = TangCa::byUnit()->where('TrangThai', 'tu_choi')->count();

        $phongBans = \App\Models\DmPhongBan::with('ttNhanVienCongViec.nhanVien')->get();

        return view('overtime.index', compact(
            'tangCas',
            'totalHoursThisMonth',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'phongBans'
        ));
    }

    /**
     * View cho cá nhân nhân viên đăng ký tăng ca
     */
    public function CaNhanView()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $nhanVien = $user->nhanVien;

        if (!$nhanVien) {
            return view('overtime.self', ['error' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên.']);
        }

        // Lấy danh sách tăng ca của cá nhân
        $myOvertimes = TangCa::where('NhanVienId', $nhanVien->id)
            ->orderBy('Ngay', 'desc')
            ->orderBy('BatDau', 'desc')
            ->get();

        return view('overtime.self', compact('nhanVien', 'myOvertimes'));
    }

    /**
     * Xử lý gửi đơn đăng ký tăng ca
     */
    public function TaoMoi(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để thực hiện chức năng này.'
            ]);
        }
        // Xác định NhanVienId: ưu tiên từ request (Admin), nếu không có dùng của User hiện tại
        $nhanVienId = $request->NhanVienId;
        if (!$nhanVienId && $user->nhanVien) {
            $nhanVienId = $user->nhanVien->id;
        }

        if (!$nhanVienId) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin nhân viên để đăng ký.'
            ]);
        }

        // Chuẩn hóa định dạng ngày từ d/m/Y sang Y-m-d nếu cần
        if ($request->has('Ngay')) {
            try {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->Ngay)) {
                    $request->merge([
                        'Ngay' => Carbon::createFromFormat('d/m/Y', $request->Ngay)->format('Y-m-d')
                    ]);
                }
            } catch (\Exception $e) {
            }
        }

        $validated = $request->validate([
            'Ngay' => 'required|date',
            'BatDau' => 'required',
            'KetThuc' => 'required',
            'LyDo' => 'required|string',
        ], [
            'Ngay.required' => 'Vui lòng chọn ngày tăng ca.',
            'Ngay.date' => 'Ngày không đúng định dạng.',
            'BatDau.required' => 'Vui lòng chọn giờ bắt đầu.',
            'KetThuc.required' => 'Vui lòng chọn giờ kết thúc.',
            'LyDo.required' => 'Vui lòng nhập lý do tăng ca.',
        ]);

        try {
            $dateOnly = Carbon::parse($request->Ngay)->format('Y-m-d');
            $start = Carbon::parse($dateOnly . ' ' . $request->BatDau);
            $end = Carbon::parse($dateOnly . ' ' . $request->KetThuc);

            // Giờ kết thúc phải sau giờ bắt đầu
            if ($start >= $end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giờ kết thúc phải sau giờ bắt đầu!'
                ]);
            }

            // Tính số giờ tăng ca của phiếu này
            $duration = round($start->diffInMinutes($end) / 60, 2);

            // 1. Kiểm tra giới hạn 4h/ngày
            $dailyTotal = TangCa::where('NhanVienId', $nhanVienId)
                ->where('Ngay', $dateOnly)
                ->where('TrangThai', 'da_duyet')
                ->sum('Tong');

            if (($dailyTotal + $duration) > 4) {
                return response()->json([
                    'success' => false,
                    'message' => "Tổng giờ tăng ca trong ngày ({$dateOnly}) không được quá 4 tiếng. Hiện tại bạn đã duyệt {$dailyTotal}h."
                ]);
            }

            // 2. Kiểm tra giới hạn 40h/tháng
            $dt = Carbon::parse($dateOnly);
            $month = $dt->month;
            $year = $dt->year;
            $monthlyTotal = TangCa::where('NhanVienId', $nhanVienId)
                ->whereMonth('Ngay', $month)
                ->whereYear('Ngay', $year)
                ->where('TrangThai', 'da_duyet')
                ->sum('Tong');

            if (($monthlyTotal + $duration) > 40) {
                return response()->json([
                    'success' => false,
                    'message' => "Tổng giờ tăng ca trong tháng {$month}/{$year} không được quá 40 tiếng. Hiện tại bạn đã duyệt {$monthlyTotal}h."
                ]);
            }

            // 3. Kiểm tra giới hạn 200h/năm
            $yearlyTotal = TangCa::where('NhanVienId', $nhanVienId)
                ->whereYear('Ngay', $year)
                ->where('TrangThai', 'da_duyet')
                ->sum('Tong');

            if (($yearlyTotal + $duration) > 200) {
                return response()->json([
                    'success' => false,
                    'message' => "Tổng giờ tăng ca trong năm {$year} không được quá 200 tiếng. Hiện tại bạn đã duyệt {$yearlyTotal}h."
                ]);
            }

            TangCa::create([
                'NhanVienId' => $nhanVienId,
                'Ngay' => $dateOnly,
                'BatDau' => $request->BatDau,
                'KetThuc' => $request->KetThuc,
                'Tong' => $duration,
                'LyDo' => $request->LyDo,
                'TrangThai' => 'dang_cho',
                'Dem' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đơn đăng ký tăng ca đã được gửi thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Nhân viên yêu cầu lại đơn tăng ca bị từ chối (tối đa 3 lần)
     */
    public function YeuCauLai(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập.']);
        }

        $overtime = TangCa::byUnit()->findOrFail($id);

        // Chỉ chủ đơn mới được yêu cầu lại
        $nhanVien = $user->nhanVien;
        if ($nhanVien && $overtime->NhanVienId !== $nhanVien->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này.']);
        }

        // Phải đang bị từ chối
        if ($overtime->TrangThai !== 'tu_choi') {
            return response()->json(['success' => false, 'message' => 'Chỉ có thể yêu cầu lại đối với đơn bị từ chối.']);
        }

        // Kiểm tra giới hạn 3 lần
        $soLanHienTai = $overtime->Dem ?? 1;
        if ($soLanHienTai >= 3) {
            return response()->json(['success' => false, 'message' => 'Đơn đã bị từ chối 3 lần, không thể yêu cầu lại.']);
        }

        // Nối lý do mới vào GhiChuLanhDao (giữ nguyên lịch sử)
        $lyDoMoi = trim($request->input('LyDo', ''));
        $ghiChuCu = $overtime->GhiChuLanhDao ?? '';
        $lanYeuCauLai = $soLanHienTai + 1;

        $ghiChuMoi = $ghiChuCu;
        if ($lyDoMoi) {
            $ghiChuMoi .= ($ghiChuCu ? '<br>' : '')
                . "<strong>Yêu cầu lại lần {$lanYeuCauLai}:</strong> {$lyDoMoi}";
        }

        $overtime->update([
            'TrangThai' => 'dang_cho',
            'Dem' => $soLanHienTai + 1,
            'GhiChuLanhDao' => $ghiChuMoi,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã gửi yêu cầu lại lần {$lanYeuCauLai}. Vui lòng chờ phê duyệt.",
        ]);
    }

    /**
     * Phê duyệt đơn tăng ca
     */
    public function Duyet(Request $request, $id)
    {
        $nhanVien = auth()->user()?->nhanVien;
        $nguoiDuyetId = $nhanVien?->id;

        $overtime = TangCa::byUnit()->findOrFail($id);
        $overtime->update([
            'TrangThai' => 'da_duyet',
            'NguoiDuyetId' => $nguoiDuyetId,
            'GhiChuLanhDao' => $request->GhiChuLanhDao,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã phê duyệt đơn tăng ca.']);
    }

    /**
     * Từ chối đơn tăng ca — GhiChuLanhDao được cộng dồn, không ghi đè
     */
    public function TuChoi(Request $request, $id)
    {
        $nhanVien = auth()->user()?->nhanVien;
        $nguoiDuyetId = $nhanVien?->id;
        $lyDoMoi = trim($request->GhiChuLanhDao ?? '');

        $overtime = TangCa::byUnit()->findOrFail($id);
        $ghiChuCu = $overtime->GhiChuLanhDao ?? '';
        $soLan = $overtime->Dem ?? 1;

        // Cộng dồn lý do từ chối theo từng lần
        $ghiChuMoi = $ghiChuCu;
        if ($lyDoMoi) {
            $ghiChuMoi .= ($ghiChuCu ? '<br>' : '')
                . "<strong>Từ chối (Lần {$soLan}):</strong> {$lyDoMoi}";
        }

        $overtime->update([
            'TrangThai' => 'tu_choi',
            'NguoiDuyetId' => $nguoiDuyetId,
            'GhiChuLanhDao' => $ghiChuMoi,
        ]);

        return response()->json(['success' => true, 'message' => 'Đã từ chối đơn tăng ca.']);
    }

    /**
     * Phê duyệt nhiều đơn tăng ca
     */
    public function DuyetNhieu(Request $request)
    {
        $nhanVien = auth()->user()?->nhanVien;
        $nguoiDuyetId = $nhanVien?->id;

        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một phiếu.']);
        }

        TangCa::byUnit()->whereIn('id', $ids)->where('TrangThai', 'dang_cho')->update([
            'TrangThai' => 'da_duyet',
            'NguoiDuyetId' => $nguoiDuyetId,
            'GhiChuLanhDao' => $request->GhiChuLanhDao ?? 'Phê duyệt hàng loạt',
        ]);

        return response()->json(['success' => true, 'message' => 'Đã phê duyệt các phiếu đã chọn.']);
    }

    /**
     * Từ chối nhiều đơn tăng ca — cộng dồn GhiChuLanhDao cho từng record
     */
    public function TuChoiNhieu(Request $request)
    {
        $nhanVien = auth()->user()?->nhanVien;
        $nguoiDuyetId = $nhanVien?->id;
        $lyDoMoi = trim($request->GhiChuLanhDao ?? 'Từ chối hàng loạt');

        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một phiếu.']);
        }

        // Xử lý từng record để cộng dồn GhiChuLanhDao đúng
        $overtimes = TangCa::byUnit()->whereIn('id', $ids)->where('TrangThai', 'dang_cho')->get();
        foreach ($overtimes as $ot) {
            $ghiChuCu = $ot->GhiChuLanhDao ?? '';
            $soLan = $ot->Dem ?? 1;
            $ghiChuMoi = $ghiChuCu . ($ghiChuCu ? '<br>' : '')
                . "<strong>Từ chối (Lần {$soLan}):</strong> {$lyDoMoi}";
            $ot->update([
                'TrangThai' => 'tu_choi',
                'NguoiDuyetId' => $nguoiDuyetId,
                'GhiChuLanhDao' => $ghiChuMoi,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Đã từ chối các phiếu đã chọn.']);
    }
}
