<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Models\CauHinhBaoHiem;
use App\Services\LuongService;
use App\Mail\SalarySlipMail;
use Illuminate\Support\Facades\Mail;

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

        $luong = LuongService::tinhLuong($nhanVien, $thang, $nam);
        $hopDong = $luong['hop_dong'];
        $baoHiems = $luong['bao_hiems'];

        return view('salary.slip_partial', compact(
            'nhanVien',
            'hopDong',
            'baoHiems',
            'luong',
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
                Mail::to($nv->Email)->queue(new SalarySlipMail($nv, $dataLuong, $thang, $nam));

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

    public function ConfigGlobalView()
    {
        $salaryCalculationType = \App\Models\SystemConfig::getValue('salary_calculation_type', 'contract');
        $thamSoLuongs = \App\Models\ThamSoLuong::orderBy('NgayApDung', 'desc')->get();
        return view('salary.config_global', compact('salaryCalculationType', 'thamSoLuongs'));
    }

    public function SaveThamSoLuong(Request $request)
    {
        $request->validate([
            'NgayApDung' => 'required|date',
            'MucLuongCoSo' => 'required|numeric|min:0',
        ], [
            'NgayApDung.required' => 'Ngày áp dụng không được để trống',
            'NgayApDung.date' => 'Ngày áp dụng không hợp lệ',
            'MucLuongCoSo.required' => 'Mức lương cơ sở không được để trống',
            'MucLuongCoSo.numeric' => 'Mức lương cơ sở phải là số',
        ]);

        try {
            \App\Models\ThamSoLuong::create($request->all());
            return redirect()->back()->with('success', 'Đã thêm tham số lương mới thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function SaveConfigGlobal(Request $request)
    {
        $request->validate([
            'salary_calculation_type' => 'required|in:contract,attendance',
        ]);

        \App\Models\SystemConfig::updateOrCreate(
            ['key' => 'salary_calculation_type'],
            [
                'value' => $request->salary_calculation_type,
                'group' => 'salary',
                'description' => 'Hình thức tính lương: contract (theo hợp đồng) hoặc attendance (theo chấm công)'
            ]
        );

        return redirect()->back()->with('success', 'Đã lưu cấu hình lương thành công!');
    }
}
