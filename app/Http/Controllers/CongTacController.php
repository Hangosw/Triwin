<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuaTrinhCongTac;

use App\Models\DmChucVu;
use App\Models\DmPhongBan;
use App\Models\NhanVien;
use Illuminate\Support\Facades\Auth;

class CongTacController extends Controller
{
    public function index()
    {
        $quatrinhs = QuaTrinhCongTac::with(['nhanVien', 'chucVu', 'phongBan'])
            ->orderBy('TuNgay', 'desc')
            ->get();

        return view('cong-tac.index', compact('quatrinhs'));
    }

    public function taoView()
    {
        $chucVus = DmChucVu::all();
        $phongBans = DmPhongBan::all();
        $nhanViens = NhanVien::select('id', 'Ten', 'Ma', 'SoCCCD')->get();
        return view('cong-tac.add', compact('chucVus', 'nhanViens', 'phongBans'));
    }

    public function store(Request $request)
    {
        $messages = [
            'type.required' => 'Loại thao tác không hợp lệ.',
            'NhanVienId.required_if' => 'Vui lòng chọn nhân viên được phân công.',

            'PhongBanId.required' => 'Vui lòng chọn Phòng ban công tác.',
            'ChucVuId.required' => 'Vui lòng chọn Chức vụ công tác.',
            'TuNgay.required' => 'Vui lòng chọn Ngày bắt đầu.',
            'DenNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng Ngày bắt đầu.'
        ];

        $request->validate([
            'type' => 'required',
            'NhanVienId' => 'required_if:type,top_down',

            'PhongBanId' => 'required',
            'ChucVuId' => 'required',
            'TuNgay' => 'required',
            'DenNgay' => 'nullable|after_or_equal:TuNgay',
        ], $messages);

        try {
            // Xác định Nhân viên ID
            $nhanVienId = null;
            if ($request->type === 'top_down') {
                // Kiểm tra User có quyển phân công (Top-down) không
                if (!Auth::user()->can('Quản lý công tác')) {
                    abort(403, 'Bạn không có quyền phân công công tác cho người khác.');
                }
                $nhanVienId = $request->NhanVienId;
            } else {
                // Bottom-up: Tự khai báo cho bản thân
                $authUser = Auth::user();
                if (!$authUser->nhanVien) {
                    return back()->with('error', 'Tài khoản của bạn chưa được liên kết với hồ sơ Nội bộ nhân viên nào!');
                }
                $nhanVienId = $authUser->nhanVien->id;
            }

            QuaTrinhCongTac::create([
                'NhanVienId' => $nhanVienId,

                'PhongBanId' => $request->PhongBanId,
                'ChucVuId' => $request->ChucVuId,
                'TuNgay' => $request->TuNgay,
                'DenNgay' => $request->DenNgay,
            ]);

            return redirect()->route('cong-tac.danh-sach')->with('success', 'Đã ghi nhận Quá trình công tác thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi khi lưu dữ liệu: ' . $e->getMessage());
        }
    }
}
