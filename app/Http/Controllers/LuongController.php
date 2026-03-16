<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhanVien;
use App\Models\CauHinhBaoHiem;
use App\Services\LuongService;

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
        ])
            
            ->where('ThoiGian', $thoiGian)
            ->get();

        return view('salary.index', compact('luongs', 'thang', 'nam'));
    }

    public function MonthlyView()
    {
        return view('salary.monthly');
    }

    public function ConfigView($nhanVienId)
    {
        $mucLuongCoSo = \App\Models\ThamSoLuong::getCurrentBaseSalary()?->MucLuongCoSo ?? 2340000;

        $nhanVien = NhanVien::with([
            'ttCongViec.chucVu',
            'ttCongViec.phongBan',
            'dienBienLuongs' => function ($q) {
                $q->with(['ngachLuong', 'bacLuong'])->orderBy('NgayHuong', 'desc');
            },
        ])->findOrFail($nhanVienId);

        $dienBienLuongs = $nhanVien->dienBienLuongs;
        $dienBienHienTai = $dienBienLuongs->first();

        return view('salary.config', compact(
            'nhanVien',
            'dienBienLuongs',
            'dienBienHienTai',
            'mucLuongCoSo'
        ));
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

        $thang = (int) $request->get('thang', date('n'));
        $nam = (int) $request->get('nam', date('Y'));

        $nhanVien = NhanVien::with([
            'ttCongViec.chucVu',
            'ttCongViec.phongBan',
            'hopDongs' => function ($query) {
                $query->where('TrangThai', 1)->latest();
            },
            'thanNhans',
            'ttCongViec',
        ])->findOrFail($id);

        // Tính lương đầy đủ
        $luong = LuongService::tinhLuong($nhanVien, $thang, $nam);

        // Lấy các biến cần thiết cho view
        $hopDong = $luong['hop_dong'];
        $baoHiems = $luong['bao_hiems'];
        $thanNhans = $nhanVien->thanNhans;

        return view('salary.detail', compact(
            'nhanVien',
            'hopDong',
            'baoHiems',
            'thanNhans',
            'luong',
            'thang',
            'nam'
        ));
    }

    /**
     * Phiếu lương partial — trả về HTML fragment cho modal AJAX.
     */
    public function SlipView(Request $request, $id)
    {
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
                        'LuongTangCa' => $ketQua['tong_tang_ca'],
                        'KhauTruBaoHiem' => $ketQua['tong_khau_tru_bh'],
                        'ThueTNCN' => $ketQua['thue_tncn'],
                        'SoNguoiPhuThuoc' => $ketQua['so_nguoi_phu_thuoc'],
                        'SoNgayCong' => $ketQua['ngay_cong_thuc_te'],
                        'Luong' => $ketQua['luong_thuc_nhan'],
                        'TrangThai' => 0,
                        'GhiChu' => ($ketQua['loai_nhan_vien'] === 0
                            ? "Công nhân – {$ketQua['ngay_cong_thuc_te']}/{$ketQua['ngay_cong_chuan']} ngày – "
                            : 'Văn phòng – ') . 'Tính tự động ' . now()->format('d/m/Y H:i'),
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
}
