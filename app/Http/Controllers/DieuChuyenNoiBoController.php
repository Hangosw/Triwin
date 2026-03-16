<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;

use App\Models\DmPhongBan;
use App\Models\DmChucVu;
use App\Models\PhieuDieuChuyenNoiBo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DieuChuyenNoiBoController extends Controller
{
    public function index()
    {
        $phieus = PhieuDieuChuyenNoiBo::with(['nhanVien', 'phongBanMoi', 'chucVuMoi'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('Transfer.index', compact('phieus'));
    }

    public function create()
    {
        $nhanViens = NhanVien::with('ttCongViec.phongBan', 'ttCongViec.chucVu')->get();
        $phongBans = DmPhongBan::all();
        $chucVus = DmChucVu::all();

        return view('employees.departmentChange', compact('nhanViens', 'phongBans', 'chucVus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NhanVienId' => 'required|exists:nhan_viens,id',
            'NgayDuKien' => 'required|date_format:d/m/Y',
            'LyDo' => 'required|string',

            'PhongBanMoiId' => 'nullable|exists:dm_phong_bans,id',
            'ChucVuMoiId' => 'nullable|exists:dm_chuc_vus,id',
            'CoThayDoiLuong' => 'nullable|in:0,1',
        ], [
            'NgayDuKien.date_format' => 'Ngày dự kiến không đúng định dạng d/m/Y',
        ]);

        // Get the authenticated user's employee record to set as NguoiYeuCauId
        $nguoiYeuCau = NhanVien::where('NguoiDungId', Auth::id())->first();

        // Get the current info of the employee being transferred
        $nhanVien = NhanVien::with('ttCongViec')->find($request->NhanVienId);
        $ttCongViec = $nhanVien->ttCongViec;

        // If not found, use current employee being transfered or fallback to random/system
        $requestedById = $nguoiYeuCau ? $nguoiYeuCau->id : $request->NhanVienId;

        PhieuDieuChuyenNoiBo::create([
            'NhanVienId' => $request->NhanVienId,
            'NguoiYeuCauId' => $requestedById,
            'PhongBanMoiId' => $request->PhongBanMoiId ?: ($ttCongViec ? $ttCongViec->PhongBanId : null),
            'ChucVuMoiId' => $request->ChucVuMoiId ?: ($ttCongViec ? $ttCongViec->ChucVuId : null),
            'NgayDuKien' => Carbon::createFromFormat('d/m/Y', $request->NgayDuKien),
            'LyDo' => $request->LyDo,
            'CoThayDoiLuong' => $request->CoThayDoiLuong ?? 0,
            'TrangThai' => 'cho_duyet'
        ]);

        return redirect()->route('dieu-chuyen.index')->with('success', 'Đã tạo phiếu điều chuyển nội bộ thành công.');
    }

    public function duyet(Request $request, $id)
    {
        $phieu = PhieuDieuChuyenNoiBo::findOrFail($id);

        // Cập nhật trạng thái phiếu
        $phieu->update([
            'TrangThai' => 'da_duyet',
            'GhiChuLanhDao' => $request->GhiChuLanhDao
        ]);

        // Cập nhật thông tin công việc của nhân viên
        \App\Models\TtNhanVienCongViec::updateOrCreate(
            ['NhanVienId' => $phieu->NhanVienId],
            [
                'PhongBanId' => $phieu->PhongBanMoiId,
                'ChucVuId' => $phieu->ChucVuMoiId,
            ]
        );

        $responseData = [
            'success' => true,
            'message' => 'Đã phê duyệt phiếu điều chuyển.'
        ];

        // Nếu có thay đổi lương, trả về URL chuyển hướng đến trang tạo hợp đồng
        if ($phieu->CoThayDoiLuong == 1) {
            $responseData['redirect_url'] = route('hop-dong.taoView', [
                'nhan_vien_id' => $phieu->NhanVienId,
                'phieu_dieu_chuyen_id' => $phieu->id
            ]);
        }

        return response()->json($responseData);
    }

    public function tuChoi(Request $request, $id)
    {
        $phieu = PhieuDieuChuyenNoiBo::findOrFail($id);
        $phieu->update([
            'TrangThai' => 'tu_choi',
            'GhiChuLanhDao' => $request->GhiChuLanhDao
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối phiếu điều chuyển.'
        ]);
    }
}
