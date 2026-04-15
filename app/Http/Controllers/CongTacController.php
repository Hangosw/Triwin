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
        $user = Auth::user();
        $query = QuaTrinhCongTac::with(['nhanVien', 'chucVu', 'phongBan'])
            ->orderBy('TuNgay', 'desc');

        // Nếu là Nhân viên và không có quyền quản lý hệ thống, chỉ xem của chính mình
        if ($user->hasRole('Nhân Viên') && !$user->can('Quản lý hệ thống')) {
            if ($user->nhanVien) {
                $query->where('NhanVienId', $user->nhanVien->id);
            } else {
                $query->whereRaw('1=0');
            }
        }

        $quatrinhs = $query->get();
        $chucVus = DmChucVu::all();
        $phongBans = DmPhongBan::all();
        $nhanViens = NhanVien::select('id', 'Ten', 'Ma', 'SoCCCD')->get();

        return view('cong-tac.index', compact('quatrinhs', 'chucVus', 'nhanViens', 'phongBans'));
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
            'NhanVienId.required' => 'Vui lòng chọn nhân viên được phân công.',
            'PhongBanId.required' => 'Vui lòng chọn Phòng ban công tác.',
            'ChucVuId.required' => 'Vui lòng chọn Chức vụ công tác.',
            'TuNgay.required' => 'Vui lòng chọn Ngày bắt đầu.',
            'TuNgay.date_format' => 'Ngày bắt đầu không đúng định dạng DD/MM/YYYY.',
            'DenNgay.date_format' => 'Ngày kết thúc không đúng định dạng DD/MM/YYYY.',
            'DenNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng Ngày bắt đầu.'
        ];

        $request->validate([
            'NhanVienId' => 'required',
            'PhongBanId' => 'required',
            'ChucVuId' => 'required',
            'TuNgay' => 'required|date_format:d/m/Y',
            'DenNgay' => 'nullable|date_format:d/m/Y|after_or_equal:TuNgay',
            'DiaDiem' => 'nullable|string|max:255',
            'GhiChu' => 'nullable|string',
        ], $messages);

        try {
            // Kiểm tra User có quyển phân công không
            if (!Auth::user()->can('Tạo Yêu Cầu Công Tác')) {
                abort(403, 'Bạn không có quyền phân công công tác cho người khác.');
            }

            // Chuyển đổi định dạng ngày sang Y-m-d để lưu database
            $tuNgay = \Carbon\Carbon::createFromFormat('d/m/Y', $request->TuNgay)->format('Y-m-d');
            $denNgay = $request->DenNgay ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->DenNgay)->format('Y-m-d') : null;

            QuaTrinhCongTac::create([
                'NhanVienId' => $request->NhanVienId,
                'PhongBanId' => $request->PhongBanId,
                'ChucVuId' => $request->ChucVuId,
                'TuNgay' => $tuNgay,
                'DenNgay' => $denNgay,
                'DiaDiem' => $request->DiaDiem,
                'GhiChu' => $request->GhiChu,
            ]);

            return back()->with('success', 'Đã ghi nhận Quá trình công tác thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi khi lưu dữ liệu: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $messages = [
            'NhanVienId.required' => 'Vui lòng chọn nhân viên được phân công.',
            'PhongBanId.required' => 'Vui lòng chọn Phòng ban công tác.',
            'ChucVuId.required' => 'Vui lòng chọn Chức vụ công tác.',
            'TuNgay.required' => 'Vui lòng chọn Ngày bắt đầu.',
            'TuNgay.date_format' => 'Ngày bắt đầu không đúng định dạng DD/MM/YYYY.',
            'DenNgay.date_format' => 'Ngày kết thúc không đúng định dạng DD/MM/YYYY.',
            'DenNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng Ngày bắt đầu.'
        ];

        $request->validate([
            'NhanVienId' => 'required',
            'PhongBanId' => 'required',
            'ChucVuId' => 'required',
            'TuNgay' => 'required|date_format:d/m/Y',
            'DenNgay' => 'nullable|date_format:d/m/Y|after_or_equal:TuNgay',
            'DiaDiem' => 'nullable|string|max:255',
            'GhiChu' => 'nullable|string',
        ], $messages);

        try {
            // Kiểm tra User có quyển phân công không
            if (!Auth::user()->can('Tạo Yêu Cầu Công Tác')) {
                abort(403, 'Bạn không có quyền sửa phân công công tác cho người khác.');
            }

            $qt = QuaTrinhCongTac::findOrFail($id);

            // Chuyển đổi định dạng ngày sang Y-m-d để lưu database
            $tuNgay = \Carbon\Carbon::createFromFormat('d/m/Y', $request->TuNgay)->format('Y-m-d');
            $denNgay = $request->DenNgay ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->DenNgay)->format('Y-m-d') : null;

            $qt->update([
                'NhanVienId' => $request->NhanVienId,
                'PhongBanId' => $request->PhongBanId,
                'ChucVuId' => $request->ChucVuId,
                'TuNgay' => $tuNgay,
                'DenNgay' => $denNgay,
                'DiaDiem' => $request->DiaDiem,
                'GhiChu' => $request->GhiChu,
            ]);

            return back()->with('success', 'Đã cập nhật Quá trình công tác thành công!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi khi cập nhật dữ liệu: ' . $e->getMessage());
        }
    }
}
