<?php

namespace App\Http\Controllers;

use App\Models\DienBienLuong;
use App\Models\DmChucVu;
use App\Models\DmPhongBan;
use App\Models\DonVi;
use App\Models\NhanVien;
use App\Models\NgachLuong;
use Illuminate\Http\Request;

class HopDongController extends Controller
{
    public function DanhSachView()
    {
        return view('contracts.index');
    }

    public function DataHopDong(Request $request)
    {
        $query = \App\Models\HopDong::with(['nhanVien'])->byUnit();

        // Filters
        if ($request->filled('loai')) {
            $query->where('Loai', $request->loai);
        }
        if ($request->filled('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }

        // Search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('SoHopDong', 'like', "%{$searchValue}%")
                    ->orWhereHas('nhanVien', function ($sq) use ($searchValue) {
                        $sq->where('Ten', 'like', "%{$searchValue}%");
                    });
            });
        }

        // Server-side processing
        $totalRecords = \App\Models\HopDong::byUnit()->count();
        $filteredRecords = $query->count();

        // Priority Sort: Expiring Soon First (within 25 days)
        $today = now()->toDateString();
        $query->orderByRaw("
            CASE 
                WHEN TrangThai = 1 
                     AND NgayKetThuc IS NOT NULL 
                     AND DATEDIFF(NgayKetThuc, '$today') BETWEEN 0 AND 25 
                THEN 0 
                ELSE 1 
            END ASC
        ");

        // Sorting (DataTables order)
        if ($request->has('order') && $request->order[0]['column'] != 0) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = ['id', 'NhanVienId', 'Loai', 'NgayBatDau', 'TongLuong', 'TrangThai'];
            if (isset($columns[$columnIndex])) {
                $query->orderBy($columns[$columnIndex], $columnDir);
            }
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $data = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function Info($id)
    {
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'nguoiKy',
            'donVi',
            'phongBan',
            'chucVu',
            'loaiHopDong'
        ])->byUnit()->findOrFail($id);

        return view('contracts.show', compact('hopDong'));
    }

    public function XoaNhieu(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            \App\Models\HopDong::byUnit()->whereIn('id', $ids)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($ids) . ' hợp đồng thành công!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vui lòng chọn hợp đồng để xóa'
        ], 400);
    }

    public function TaoView()
    {
        $donvi = DonVi::all();
        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();
        $nhanvien = NhanVien::byUnit()->with(['ttCongViec.chucVu', 'ttCongViec.phongBan', 'ttCongViec.donVi'])->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $ngachLuongs = NgachLuong::with('bacLuongs')->orderBy('Ma')->get();

        return view('contracts.create', compact('nhanvien', 'donvi', 'phongban', 'chucvu', 'mucLuongCoSo', 'ngachLuongs'));
    }

    public function Tao(Request $request)
    {
        // Debug: Log request data
        \Log::info('Contract Creation Request:', $request->all());

        // Validate incoming request
        $validated = $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
            'NguoiKyId' => 'required|exists:nhan_viens,id',
            'so_hop_dong' => 'required|string|max:255',
            'loai_hop_dong_id' => 'required|integer',
            'loai' => 'required|string|max:50',
            'don_vi_id' => 'required|exists:don_vis,id',
            'phong_ban_id' => 'required|exists:dm_phong_bans,id',
            'chuc_vu_id' => 'required|exists:dm_chuc_vus,id',
            'NgayBatDau' => 'required|date_format:d/m/Y',
            'NgayKetThuc' => 'nullable|date_format:d/m/Y',
            'trang_thai' => 'required|in:0,1,2',
            // Salary fields
            'luong_co_ban' => 'required|numeric|min:5310000',
            'phu_cap_chuc_vu' => 'nullable|numeric|min:0',
            'phu_cap_trach_nhiem' => 'nullable|numeric|min:0',
            'phu_cap_doc_hai' => 'nullable|numeric|min:0',
            'phu_cap_tham_nien' => 'nullable|numeric|min:0',
            'phu_cap_khu_vuc' => 'nullable|numeric|min:0',
            'phu_cap_an_trua' => 'nullable|numeric|min:0',
            'phu_cap_xang_xe' => 'nullable|numeric|min:0',
            'phu_cap_dien_thoai' => 'nullable|numeric|min:0',
            'phu_cap_nha_o' => 'nullable|numeric|min:0',
            'phu_cap_khac' => 'nullable|numeric|min:0',
            'tong_luong' => 'required|numeric|min:0',
            // Ngạch & bậc lương
            'ngach_luong_id' => 'nullable|exists:ngach_luongs,id',
            'bac_luong_id' => 'nullable|exists:bac_luongs,id',
            // File upload
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            // Map snake_case form inputs to PascalCase database columns
            $data = [
                'NhanVienId' => $validated['nhan_vien_id'],
                'NguoiKyId' => $validated['NguoiKyId'], // Use validated data
                'SoHopDong' => $validated['so_hop_dong'],
                'Loai' => $validated['loai'],
                'DonViId' => $validated['don_vi_id'],
                'PhongBanId' => $validated['phong_ban_id'],
                'ChucVuId' => $validated['chuc_vu_id'],
                'TrangThai' => $validated['trang_thai'],
                // Salary fields
                'LuongCoBan' => $validated['luong_co_ban'],
                'PhuCapChucVu' => $validated['phu_cap_chuc_vu'] ?? 0,
                'PhuCapTrachNhiem' => $validated['phu_cap_trach_nhiem'] ?? 0,
                'PhuCapDocHai' => $validated['phu_cap_doc_hai'] ?? 0,
                'PhuCapThamNien' => $validated['phu_cap_tham_nien'] ?? 0,
                'PhuCapKhuVuc' => $validated['phu_cap_khu_vuc'] ?? 0,
                'PhuCapAnTrua' => $validated['phu_cap_an_trua'] ?? 0,
                'PhuCapXangXe' => $validated['phu_cap_xang_xe'] ?? 0,
                'PhuCapDienThoai' => $validated['phu_cap_dien_thoai'] ?? 0,
                'PhuCapKhac' => ($validated['phu_cap_khac'] ?? 0) + ($validated['phu_cap_nha_o'] ?? 0),
                'TongLuong' => $validated['tong_luong'],
            ];

            // Convert date format from dd/mm/yyyy to yyyy-mm-dd
            $data['NgayBatDau'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['NgayBatDau'])->format('Y-m-d');
            if (!empty($validated['NgayKetThuc'])) {
                $data['NgayKetThuc'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['NgayKetThuc'])->format('Y-m-d');
            }

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/contracts'), $filename);
                $data['File'] = 'uploads/contracts/' . $filename;
            }

            // Create contract and deactivate old ones in a transaction
            // Lấy ngạch/bậc lương từ request (trước transaction)
            $ngachLuongId = $request->filled('ngach_luong_id') ? (int) $request->ngach_luong_id : null;
            $bacLuongId = $request->filled('bac_luong_id') ? (int) $request->bac_luong_id : null;

            $hopDong = \DB::transaction(function () use ($data, $ngachLuongId, $bacLuongId) {
                // Nếu hợp đồng mới là đang hoạt động (TrangThai == 1)
                if ($data['TrangThai'] == 1) {
                    // Tìm và vô hiệu hóa các hợp đồng đang hoạt động cũ của nhân viên này
                    \App\Models\HopDong::where('NhanVienId', $data['NhanVienId'])
                        ->where('TrangThai', 1)
                        ->update(['TrangThai' => 0]);
                }

                $hopDong = \App\Models\HopDong::create($data);

                // Ghi diễn biến lương nếu có chọn ngạch/bậc
                if ($ngachLuongId && $bacLuongId) {
                    DienBienLuong::create([
                        'NhanVienId' => $data['NhanVienId'],
                        'HopDongId' => $hopDong->id,
                        'NgachLuongId' => $ngachLuongId,
                        'BacLuongId' => $bacLuongId,
                        'NgayHuong' => $data['NgayBatDau'],
                    ]);
                }

                return $hopDong;
            });

            // Update or create employee's department and position in tt_nhan_vien_cong_viecs
            \App\Models\TtNhanVienCongViec::updateOrCreate(
                ['NhanVienId' => $data['NhanVienId']], // Find by NhanVienId
                [
                    'PhongBanId' => $data['PhongBanId'],
                    'ChucVuId' => $data['ChucVuId'],
                    'DonViId' => $data['DonViId'],
                ]
            );

            // Mark internal transfer as contract created if ID is provided
            if ($request->has('phieu_dieu_chuyen_id')) {
                \App\Models\PhieuDieuChuyenNoiBo::where('id', $request->phieu_dieu_chuyen_id)
                    ->update(['DaTaoHopDong' => 1]);
            }


            // Return JSON response for AJAX requests
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hợp đồng đã được tạo thành công và thông tin nhân viên đã được cập nhật!'
                ]);
            }

            return redirect()->route('contracts.index')->with('success', 'Hợp đồng đã được tạo thành công và thông tin nhân viên đã được cập nhật!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            // Return general errors as JSON
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    public function downloadWord($id)
    {
        $hopDong = \App\Models\HopDong::byUnit()->with('nhanVien')->findOrFail($id);

        $templatePath = storage_path('app/contracts/template_hop_dong.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Không tìm thấy file mẫu hợp đồng lao động.');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Variables from NhanVien
        $nv = $hopDong->nhanVien;
        $templateProcessor->setValue('TenCongTy', 'CÔNG TY TNHH PHẦN MỀM');
        $templateProcessor->setValue('DienThoaiCongTy', '0123456789');
        $templateProcessor->setValue('DiaChiCongTy', '123 Đường X, Phường Y, Quận Z, TP. HCM');
        $templateProcessor->setValue('TenGiamDoc', 'Nguyễn Văn Giám Đốc');

        $templateProcessor->setValue('SoHopDong', $hopDong->SoHopDong ?? '');
        $templateProcessor->setValue('TenNhanVien', $nv ? $nv->Ten : '');
        $templateProcessor->setValue('NgaySinh', $nv && $nv->NgaySinh ? \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') : '');
        $templateProcessor->setValue('NoiSinh', $nv ? $nv->QueQuan : '');
        $templateProcessor->setValue('DiaChiThuongTru', $nv ? $nv->DiaChi : '');
        $templateProcessor->setValue('NgheNghiep', 'Nhân viên');
        $templateProcessor->setValue('SoCMND', $nv ? $nv->SoCCCD : '');
        $templateProcessor->setValue('NgayCap', $nv && $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d-m-Y') : '');
        $templateProcessor->setValue('NoiCap', $nv ? $nv->NoiCap : '');

        // Variables from HopDong
        $loaiHopDongStr = $hopDong->loaiHopDong ? $hopDong->loaiHopDong->TenLoai : 'Hợp đồng lao động';
        $templateProcessor->setValue('LoaiHopDong', $loaiHopDongStr);
        $templateProcessor->setValue('TuNgay', $hopDong->NgayBatDau ? \Carbon\Carbon::parse($hopDong->NgayBatDau)->format('d/m/Y') : '');
        $templateProcessor->setValue('DiaDiemLamViec', 'Trụ sở công ty');
        $templateProcessor->setValue('ChucVu', $hopDong->chucVu ? $hopDong->chucVu->TenChucVu : '');

        $mucLuong = $hopDong->MucLuong ? number_format($hopDong->MucLuong, 0, ',', '.') : '0';
        $templateProcessor->setValue('MucLuong', $mucLuong);
        $templateProcessor->setValue('NgayKy', date('d/m/Y'));

        // Save file locally temporarily
        $fileName = 'HopDong_' . ($nv ? \Illuminate\Support\Str::slug($nv->Ten) : $hopDong->id) . '.docx';
        $tempPath = storage_path('app/contracts/temp_' . uniqid() . '.docx');
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function print($id)
    {
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'chucVu',
            'phongBan',
            'loaiHopDong',
            'donVi',
            'nguoiKy'
        ])->byUnit()->findOrFail($id);

        return view('contracts.template', compact('hopDong'));
    }

    public function SuaView($id)
    {
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'nguoiKy',
            'donVi',
            'phongBan',
            'chucVu',
            'loaiHopDong'
        ])->byUnit()->findOrFail($id);

        $donvi = DonVi::all();
        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();
        $nhanvien = NhanVien::byUnit()->with(['ttCongViec.chucVu', 'ttCongViec.phongBan', 'ttCongViec.donVi'])->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $ngachLuongs = NgachLuong::with('bacLuongs')->orderBy('Ma')->get();

        return view('contracts.edit', compact('hopDong', 'nhanvien', 'donvi', 'phongban', 'chucvu', 'mucLuongCoSo', 'ngachLuongs'));
    }

    public function CapNhat(Request $request, $id)
    {
        $hopDong = \App\Models\HopDong::byUnit()->findOrFail($id);

        // Validate incoming request
        $validated = $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
            'NguoiKyId' => 'required|exists:nhan_viens,id',
            'so_hop_dong' => 'required|string|max:255',
            'loai_hop_dong_id' => 'required|integer',
            'loai' => 'required|string|max:50',
            'don_vi_id' => 'required|exists:don_vis,id',
            'phong_ban_id' => 'required|exists:dm_phong_bans,id',
            'chuc_vu_id' => 'required|exists:dm_chuc_vus,id',
            'NgayBatDau' => 'required|date_format:d/m/Y',
            'NgayKetThuc' => 'nullable|date_format:d/m/Y',
            'trang_thai' => 'required|in:0,1,2',
            // Salary fields
            'luong_co_ban' => 'required|numeric|min:5310000',
            'phu_cap_chuc_vu' => 'nullable|numeric|min:0',
            'phu_cap_trach_nhiem' => 'nullable|numeric|min:0',
            'phu_cap_doc_hai' => 'nullable|numeric|min:0',
            'phu_cap_tham_nien' => 'nullable|numeric|min:0',
            'phu_cap_khu_vuc' => 'nullable|numeric|min:0',
            'phu_cap_an_trua' => 'nullable|numeric|min:0',
            'phu_cap_xang_xe' => 'nullable|numeric|min:0',
            'phu_cap_dien_thoai' => 'nullable|numeric|min:0',
            'phu_cap_nha_o' => 'nullable|numeric|min:0',
            'phu_cap_khac' => 'nullable|numeric|min:0',
            'tong_luong' => 'required|numeric|min:0',
            // Ngạch & bậc lương
            'ngach_luong_id' => 'nullable|exists:ngach_luongs,id',
            'bac_luong_id' => 'nullable|exists:bac_luongs,id',
            // File upload
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            $data = [
                'NhanVienId' => $validated['nhan_vien_id'],
                'NguoiKyId' => $validated['NguoiKyId'],
                'SoHopDong' => $validated['so_hop_dong'],
                'Loai' => $validated['loai'],
                'DonViId' => $validated['don_vi_id'],
                'PhongBanId' => $validated['phong_ban_id'],
                'ChucVuId' => $validated['chuc_vu_id'],
                'TrangThai' => $validated['trang_thai'],
                // Salary fields
                'LuongCoBan' => $validated['luong_co_ban'],
                'PhuCapChucVu' => $validated['phu_cap_chuc_vu'] ?? 0,
                'PhuCapTrachNhiem' => $validated['phu_cap_trach_nhiem'] ?? 0,
                'PhuCapDocHai' => $validated['phu_cap_doc_hai'] ?? 0,
                'PhuCapThamNien' => $validated['phu_cap_tham_nien'] ?? 0,
                'PhuCapKhuVuc' => $validated['phu_cap_khu_vuc'] ?? 0,
                'PhuCapAnTrua' => $validated['phu_cap_an_trua'] ?? 0,
                'PhuCapXangXe' => $validated['phu_cap_xang_xe'] ?? 0,
                'PhuCapDienThoai' => $validated['phu_cap_dien_thoai'] ?? 0,
                'PhuCapKhac' => ($validated['phu_cap_khac'] ?? 0) + ($validated['phu_cap_nha_o'] ?? 0),
                'TongLuong' => $validated['tong_luong'],
            ];

            $data['NgayBatDau'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['NgayBatDau'])->format('Y-m-d');
            if (!empty($validated['NgayKetThuc'])) {
                $data['NgayKetThuc'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['NgayKetThuc'])->format('Y-m-d');
            } else {
                $data['NgayKetThuc'] = null;
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/contracts'), $filename);
                $data['File'] = 'uploads/contracts/' . $filename;
            }

            \DB::transaction(function () use ($hopDong, $data, $request) {
                $hopDong->update($data);

                // Update DienBienLuong if ngach/bac changed
                if ($request->filled('ngach_luong_id') && $request->filled('bac_luong_id')) {
                    DienBienLuong::updateOrCreate(
                        ['HopDongId' => $hopDong->id],
                        [
                            'NhanVienId' => $hopDong->NhanVienId,
                            'NgachLuongId' => (int) $request->ngach_luong_id,
                            'BacLuongId' => (int) $request->bac_luong_id,
                            'NgayHuong' => $data['NgayBatDau'],
                        ]
                    );
                }
            });

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hợp đồng đã được cập nhật thành công!'
                ]);
            }

            return redirect()->route('hop-dong.info', $id)->with('success', 'Hợp đồng đã được cập nhật thành công!');

        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function RenewView($id)
    {
        $oldContract = \App\Models\HopDong::with([
            'nhanVien',
            'nguoiKy',
            'donVi',
            'phongBan',
            'chucVu',
            'loaiHopDong'
        ])->byUnit()->findOrFail($id);

        // Fetch related salary progression (Ngạch/Bậc)
        $oldDienBien = \App\Models\DienBienLuong::where('HopDongId', $id)->first();

        $donvi = DonVi::all();
        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();
        $nhanvien = NhanVien::byUnit()->with(['ttCongViec.chucVu', 'ttCongViec.phongBan', 'ttCongViec.donVi'])->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $ngachLuongs = NgachLuong::with('bacLuongs')->orderBy('Ma')->get();

        $isRenew = true;

        return view('contracts.create', compact('oldContract', 'oldDienBien', 'isRenew', 'nhanvien', 'donvi', 'phongban', 'chucvu', 'mucLuongCoSo', 'ngachLuongs'));
    }
}
