<?php

namespace App\Http\Controllers;

use App\Models\Luong;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Models\CauHinhBaoHiem;
use App\Services\LuongService;
use App\Services\SystemLogService;
use App\Mail\SalarySlipMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LuongController extends Controller
{
    /**
     * Danh sách lương tất cả nhân viên (tháng hiện tại).
     * Tính lương thực nhận bằng LuongService để đồng nhất với trang chi tiết.
     */
    public function IndexView(Request $request)
    {
        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));

        $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-01');

        // Lấy dữ liệu từ bảng luongs theo tháng/năm
        $luongs = \App\Models\Luong::with([
            'nhanVien.ttCongViec.chucVu',
            'nhanVien.ttCongViec.phongBan',
            'nhanVien.hopDongs',
            'nhanVien.thanNhans',
        ])
            ->where('ThoiGian', $thoiGian)
            ->get();

        // Tính sẵn insurance details để tránh gọi service trong blade
        $insuranceDetails = [];
        foreach ($luongs as $luong) {
            try {
                $detail = LuongService::tinhLuong($luong->nhanVien, $thang, $nam);
                $insuranceDetails[$luong->nhanVien->id] = $detail['bao_hiems_detail'] ?? [];
            } catch (\Exception $e) {
                $insuranceDetails[$luong->nhanVien->id] = [];
            }
        }

        return view('salary.index', compact('luongs', 'insuranceDetails', 'thang', 'nam'));
    }

    public function MonthlyView()
    {
        return view('salary.monthly');
    }



    /**
     * Chi tiết lương một nhân viên.
     * Tính lương đầy đủ bằng LuongService.
     */
    public function DetailView(Request $request, $id = null)
    {
        if (!$id) {
            return redirect()->route('salary.index');
        }

        // Ownership / Permission check
        if (!auth()->user()->can('Xem Danh Sách Lương')) {
            $checkEmp = NhanVien::findOrFail($id);
            if ($checkEmp->NguoiDungId != auth()->id()) {
                abort(403, 'Bạn không có quyền xem phiếu lương của nhân viên này.');
            }
        }

        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));
        $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-d');

        // KIỂM TRA: Lấy bản ghi lương đã chốt
        $luongRecord = \App\Models\Luong::where('NhanVienId', $id)
            ->where('ThoiGian', $thoiGian)
            ->first();

        $nhanVien = NhanVien::with([
            'ttCongViec.chucVu',
            'ttCongViec.phongBan',
            'hopDongs' => function ($query) {
                $query->where('TrangThai', 1)->latest();
            },
            'thanNhans',
            'ttCongViec',
        ])->findOrFail($id);

        $luong = null;
        $hopDong = null;
        $baoHiems = null;
        $thanNhans = null;

        if ($luongRecord) {
            // Chỉ tính chi tiết nếu đã có dữ liệu chốt
            $luong = LuongService::tinhLuong($nhanVien, $thang, $nam);
            $hopDong = $luong['hop_dong'];
            $baoHiems = $luong['bao_hiems'];
            $thanNhans = $nhanVien->thanNhans;
        }

        return view('salary.detail', compact(
            'nhanVien',
            'hopDong',
            'baoHiems',
            'thanNhans',
            'luong',
            'luongRecord',
            'thang',
            'nam'
        ));
    }

    /**
     * Tính lại và cập nhật lương cho một nhân viên cụ thể.
     */
    public function UpdateSingleSalary(Request $request, $id)
    {
        try {
            $thang = (int) $request->get('thang', date('n'));
            $nam = (int) $request->get('nam', date('Y'));
            $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-d');

            $nv = NhanVien::with([
                'hopDongs' => fn($q) => $q->where('TrangThai', 1)->latest(),
                'thanNhans',
                'ttCongViec',
            ])->findOrFail($id);

            $ketQua = LuongService::tinhLuong($nv, $thang, $nam);

            \App\Models\Luong::updateOrCreate(
                [
                    'NhanVienId' => $nv->id,
                    'ThoiGian' => $thoiGian,
                ],
                [
                    'LoaiLuong' => $ketQua['loai_nhan_vien'] ?? 0,
                    'LuongCoBan' => $ketQua['luong_co_ban'],
                    'PhuCap' => $ketQua['tong_phu_cap'],
                    'KhauTruBaoHiem' => $ketQua['tong_khau_tru_bh'],
                    'ThueTNCN' => $ketQua['thue_tncn'],
                    'SoNguoiPhuThuoc' => $ketQua['so_nguoi_phu_thuoc'],
                    'SoNgayCong' => $ketQua['ngay_cong_thuc_te'],
                    'Luong' => $ketQua['luong_thuc_nhan'],
                    'TamUng' => $ketQua['tam_ung'] ?? 0,
                    'GhiChu' => "Cập nhật thủ công ngày " . now()->format('d/m/Y H:i'),
                ]
            );

            return redirect()->back()->with('success', "Đã cập nhật lại lương tháng {$thang}/{$nam} cho nhân viên {$nv->Ten} thành công.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Lỗi khi tính lại lương: " . $e->getMessage());
        }
    }

    /**
     * Phiếu lương partial — trả về HTML fragment cho modal AJAX.
     */
    public function SlipView(Request $request, $id)
    {
        // Ownership / Permission check
        if (!auth()->user()->can('Xem Danh Sách Lương')) {
            $checkEmp = NhanVien::findOrFail($id);
            if ($checkEmp->NguoiDungId != auth()->id()) {
                abort(403, 'Bạn không có quyền xem phiếu lương của nhân viên này.');
            }
        }
        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));

        $nhanVien = NhanVien::with([
            'ttCongViec.chucVu',
            'ttCongViec.phongBan',
            'hopDongs' => fn($q) => $q->where('TrangThai', 1)->latest(),
            'thanNhans',
        ])->findOrFail($id);

        $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-d');
        $luongRecord = \App\Models\Luong::where('NhanVienId', $id)
            ->where('ThoiGian', $thoiGian)
            ->first();

        $luong = LuongService::tinhLuong($nhanVien, $thang, $nam);
        $hopDong = $luong['hop_dong'];
        $baoHiems = $luong['bao_hiems'];

        return view('salary.slip_partial', compact(
            'nhanVien',
            'hopDong',
            'baoHiems',
            'luong',
            'luongRecord',
            'thang',
            'nam'
        ));
    }

    /**
     * API: Tính lương tự động cho một nhân viên (AJAX).
     */
    public function TinhLuong(Request $request, $id)
    {
        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));

        $nhanVien = NhanVien::with([
            'ttCongViec',
            'hopDongs' => function ($query) {
                $query->where('TrangThai', 1)->latest();
            },
            'thanNhans',
        ])->findOrFail($id);

        $ketQua = LuongService::tinhLuong($nhanVien, $thang, $nam);

        // Loại bỏ models khỏi response JSON
        unset($ketQua['hop_dong'], $ketQua['bao_hiems']);

        return response()->json([
            'success' => true,
            'data' => $ketQua,
        ]);
    }

    /**
     * Tính lương hàng loạt cho toàn bộ nhân viên có hợp đồng active.
     * Dùng updateOrCreate để không tạo bản ghi trùng theo (NhanVienId, tháng/năm).
     */
    public function TinhLuongHangLoat(Request $request)
    {
        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));

        $nhanViens = NhanVien::with([
            'hopDongs' => function ($q) {
                $q->where('TrangThai', 1)->latest();
            },
            'thanNhans',
            'ttCongViec',
        ])
            ->whereHas('hopDongs', function ($q) {
                $q->where('TrangThai', 1);
            })->get();

        $thanhCong = 0;
        $boQua = 0;
        $errors = [];
        $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-d');

        foreach ($nhanViens as $nv) {
            try {
                $ketQua = LuongService::tinhLuong($nv, $thang, $nam);

                \App\Models\Luong::updateOrCreate(
                    [
                        'NhanVienId' => $nv->id,
                        'ThoiGian' => $thoiGian,
                    ],
                    [
                        'LoaiLuong' => $ketQua['loai_nhan_vien'] ?? 0,
                        'LuongCoBan' => $ketQua['luong_co_ban'],
                        'PhuCap' => $ketQua['tong_phu_cap'],
                        'KhauTruBaoHiem' => $ketQua['tong_khau_tru_bh'],
                        'ThueTNCN' => $ketQua['thue_tncn'],
                        'SoNguoiPhuThuoc' => $ketQua['so_nguoi_phu_thuoc'],
                        'SoNgayCong' => $ketQua['ngay_cong_thuc_te'],
                        'Luong' => $ketQua['luong_thuc_nhan'],
                        'TamUng' => $ketQua['tam_ung'] ?? 0,
                        'TrangThai' => 0,
                        'GhiChu' => ($ketQua['loai_nhan_vien_text'] ?? 'Bản ghi') .
                            " – " . number_format($ketQua['ngay_cong_thuc_te'], 2) . "/" .
                            $ketQua['ngay_cong_chuan'] . " ngày – Tính tự động " . now()->format('d/m/Y H:i'),
                    ]
                );
                $thanhCong++;
            } catch (\Throwable $e) {
                $boQua++;
                $errors[] = "NV#{$nv->id} ({$nv->Ten}): " . $e->getMessage()
                    . ' [' . class_basename($e) . ' @ ' . basename($e->getFile()) . ':' . $e->getLine() . ']';
                \Log::warning("TinhLuongHangLoat: NV#{$nv->id} – " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'thanh_cong' => $thanhCong,
            'bo_qua' => $boQua,
            'errors' => $errors,
            'thang' => $thang,
            'nam' => $nam,
            'message' => "Đã tính lương {$thang}/{$nam} cho {$thanhCong} nhân viên" .
                ($boQua > 0 ? ", bỏ qua {$boQua} lỗi" : '') . '.',
        ]);
    }

    /**
     * Lịch sử lương (Danh sách tất cả các tháng của 1 nhân viên)
     */
    public function HistoryView(Request $request, $id = null)
    {
        if (!$id) {
            $nv_auth = auth()->user()->nhanVien;
            if (!$nv_auth) {
                abort(404, 'Tài khoản này chưa được liên kết với hồ sơ nhân viên.');
            }
            $id = $nv_auth->id;
        }

        // Ownership / Permission check
        if (!auth()->user()->can('Xem Danh Sách Lương')) {
            $checkEmp = NhanVien::findOrFail($id);
            if ($checkEmp->NguoiDungId != auth()->id()) {
                abort(403, 'Bạn không có quyền xem lịch sử lương của nhân viên này.');
            }
        }

        $nv = NhanVien::with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])->findOrFail($id);
        $luongs = \App\Models\Luong::where('NhanVienId', $id)
            ->orderBy('ThoiGian', 'desc')
            ->get();

        return view('salary.history', compact('nv', 'luongs'));
    }

    /**
     * Gửi email phiếu lương hàng loạt.
     */
    public function GuiMailLuong(Request $request)
    {
        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));

        $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-01');

        $luongs = \App\Models\Luong::with(['nhanVien'])->where('ThoiGian', $thoiGian)->get();

        if ($luongs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Không tìm thấy dữ liệu lương tháng {$thang}/{$nam} để gửi mail."
            ]);
        }

        $guiThanhCong = 0;
        $guiLoi = 0;
        $errors = [];

        foreach ($luongs as $luong) {
            /** @var \App\Models\NhanVien $nv */
            $nv = $luong->nhanVien;
            if (!$nv || empty($nv->Email)) {
                $guiLoi++;
                if ($nv) {
                    $errors[] = "Nhân viên {$nv->Ten} không có địa chỉ email.";
                }
                continue;
            }

            try {
                // Tính lại lương đầy đủ để có data cho email mailable (giống trang slip)
                $dataLuong = LuongService::tinhLuong($nv, $thang, $nam);

                \Log::info("Sending salary email for {$nv->Ten} to {$nv->Email} (Month: {$thang}/{$nam})");

                // Gửi mail (nên dùng queue để tránh timeout nếu gửi hàng loạt nhiều)
                Mail::to($nv->Email)->queue(new SalarySlipMail($nv, $dataLuong, $thang, $nam, $luong));

                $guiThanhCong++;
            } catch (\Exception $e) {
                $guiLoi++;
                $errors[] = "Lỗi khi gửi cho {$nv->Ten}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'gui_thanh_cong' => $guiThanhCong,
            'gui_loi' => $guiLoi,
            'errors' => $errors,
            'message' => "Đã đưa vào hàng chờ gửi {$guiThanhCong} email phiếu lương." .
                ($guiLoi > 0 ? " Thất bại: {$guiLoi}." : "")
        ]);
    }



    public function export(Request $request)
    {
        $thang = (int) ($request->thang ?? now()->month);
        $nam   = (int) ($request->nam  ?? now()->year);

        $thoiGian = \Carbon\Carbon::createFromDate($nam, $thang, 1)->format('Y-m-01');

        $luongs = Luong::with([
            'nhanVien.ttCongViec.chucVu',
            'nhanVien.hopDongs',
            'nhanVien.thanNhans',
        ])
            ->where('ThoiGian', $thoiGian)
            ->get();

        // Tính insurance details giống IndexView
        $insuranceDetails = [];
        foreach ($luongs as $luong) {
            $nv = $luong->nhanVien;
            if ($nv) {
                try {
                    $detail = \App\Services\LuongService::tinhLuong($nv, $thang, $nam);
                    $insuranceDetails[$nv->id] = $detail['bao_hiems_detail'] ?? [];
                } catch (\Exception $e) {
                    $insuranceDetails[$nv->id] = [];
                }
            }
        }

        return (new \App\Exports\SalaryExport($luongs, $insuranceDetails, $thang, $nam))->download();
    }

    /**
     * View tính lương thủ công (Điều chỉnh tay)
     */
    public function ManualEditView($id)
    {
        $luong = Luong::with(['nhanVien.ttCongViec.chucVu', 'nhanVien.ttCongViec.phongBan', 'nhanVien.hopDongs'])->findOrFail($id);
        $nv = $luong->nhanVien;

        $dt = \Carbon\Carbon::parse($luong->ThoiGian);
        $thang = $dt->month;
        $nam = $dt->year;

        // Lấy con số tự động chốt để so sánh
        try {
            $auto = LuongService::tinhLuong($nv, $thang, $nam);
        } catch (\Exception $e) {
            $auto = null;
        }

        // Lấy lịch sử điều chỉnh
        $logs = DB::table('lich_sus')
            ->leftJoin('nguoi_dungs', 'lich_sus.NhanVienId', '=', 'nguoi_dungs.id')
            ->where('DoiTuongLoai', 'Luong')
            ->where('DoiTuongId', $id)
            ->select('lich_sus.*', 'nguoi_dungs.Ten as TenNguoiDung')
            ->orderByDesc('lich_sus.Id')
            ->get();

        return view('salary.edit_manual', compact('luong', 'nv', 'auto', 'thang', 'nam', 'logs'));
    }

    /**
     * Xử lý cập nhật lương thủ công
     */
    public function UpdateManual(Request $request, $id)
    {
        $request->validate([
            'LuongCoBan' => 'required|numeric|min:0',
            'PhuCap' => 'required|numeric|min:0',
            'KhauTruBaoHiem' => 'required|numeric|min:0',
            'ThueTNCN' => 'required|numeric|min:0',
            'TamUng' => 'required|numeric|min:0',
            'KhenThuong' => 'nullable|numeric|min:0',
            'KyLuat' => 'nullable|numeric|min:0',
            'LyDoKhenThuong' => 'required_if:KhenThuong,>0|nullable|string',
            'LyDoKyLuat' => 'required_if:KyLuat,>0|nullable|string',
            'GhiChu' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $luong = Luong::lockForUpdate()->findOrFail($id);

            // Lưu log cũ để so sánh
            $oldData = [
                'LuongCoBan' => (float)$luong->LuongCoBan,
                'PhuCap' => (float)$luong->PhuCap,
                'KhauTruBaoHiem' => (float)$luong->KhauTruBaoHiem,
                'ThueTNCN' => (float)$luong->ThueTNCN,
                'TamUng' => (float)$luong->TamUng,
                'KhenThuong' => (float)$luong->KhenThuong,
                'KyLuat' => (float)$luong->KyLuat,
            ];

            // Dữ liệu mới từ request
            $newData = [
                'LuongCoBan' => (float)$request->LuongCoBan,
                'PhuCap' => (float)$request->PhuCap,
                'KhauTruBaoHiem' => (float)$request->KhauTruBaoHiem,
                'ThueTNCN' => (float)$request->ThueTNCN,
                'TamUng' => (float)$request->TamUng,
                'KhenThuong' => (float)($request->KhenThuong ?? 0),
                'KyLuat' => (float)($request->KyLuat ?? 0),
            ];

            // Kiểm tra xem có gì thay đổi không
            $changedFieldsOld = [];
            $changedFieldsNew = [];
            foreach ($newData as $key => $value) {
                if (abs($value - $oldData[$key]) > 0.01) { // Sử dụng abs để tránh sai số float
                    $changedFieldsOld[$key] = $oldData[$key];
                    $changedFieldsNew[$key] = $value;
                }
            }

            if (!empty($changedFieldsNew)) {
                $logMoTa = $request->GhiChu ?? 'Cập nhật các khoản thu nhập/khấu trừ thủ công';
                
                // Bổ sung lý do thưởng/phạt vào mô tả log nếu có
                if ($request->KhenThuong > 0 && $request->LyDoKhenThuong) {
                    $logMoTa .= " | Lý do thưởng: " . $request->LyDoKhenThuong;
                }
                if ($request->KyLuat > 0 && $request->LyDoKyLuat) {
                    $logMoTa .= " | Lý do kỷ luật: " . $request->LyDoKyLuat;
                }

                SystemLogService::log(
                    'Điều chỉnh lương thủ công',
                    'Luong',
                    $luong->id,
                    $logMoTa,
                    $changedFieldsOld,
                    $changedFieldsNew
                );
            }

            // Cập nhật các trường
            $luong->LuongCoBan = $newData['LuongCoBan'];
            $luong->PhuCap = $newData['PhuCap'];
            $luong->KhauTruBaoHiem = $newData['KhauTruBaoHiem'];
            $luong->ThueTNCN = $newData['ThueTNCN'];
            $luong->TamUng = $newData['TamUng'];
            $luong->KhenThuong = $newData['KhenThuong'];
            $luong->KyLuat = $newData['KyLuat'];
            $luong->GhiChu = $request->GhiChu;

            // Recalculate Net
            $earnings = $luong->LuongCoBan + $luong->PhuCap + $luong->KhenThuong;
            $deductions = $luong->KhauTruBaoHiem + $luong->ThueTNCN + $luong->TamUng + $luong->KyLuat;
            $luong->Luong = max(0, $earnings - $deductions);

            $luong->save();

            return redirect()->route('salary.index', [
                'thang' => \Carbon\Carbon::parse($luong->ThoiGian)->month,
                'nam' => \Carbon\Carbon::parse($luong->ThoiGian)->year
            ])->with('success', "Đã điều chỉnh lương thủ công cho nhân viên {$luong->nhanVien->Ten} thành công.");
        });
    }
}
