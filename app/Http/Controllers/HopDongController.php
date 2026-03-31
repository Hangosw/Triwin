<?php

namespace App\Http\Controllers;

use App\Models\DienBienLuong;
use App\Models\DmChucVu;
use App\Models\DmPhongBan;

use App\Models\NhanVien;
use App\Models\NgachLuong;
use App\Models\PhuLucHopDong;
use Illuminate\Http\Request;

class HopDongController extends Controller
{
    public function DanhSachView()
    {
        return view('contracts.index');
    }

    public function DataHopDong(Request $request)
    {
        $query = \App\Models\HopDong::with(['nhanVien'])
            ->where('Loai', 'not like', 'nda%');

        // Filters
        if ($request->filled('loai')) {
            $query->where('Loai', $request->loai);
        }
        if ($request->filled('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }
        if ($request->get('expiring_soon') == '1') {
            $today = now()->toDateString();
            $query->where('TrangThai', 1)
                ->whereNotNull('NgayKetThuc')
                ->whereRaw("DATEDIFF(NgayKetThuc, ?) BETWEEN 0 AND 25", [$today]);
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
        $totalRecords = \App\Models\HopDong::count();
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
            'phongBan',
            'chucVu',
            'loaiHopDong',
            'phuCaps', // Load dynamic allowances
            'phuLucs' => function($query) {
                $query->with('dieuKhoans')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Tìm tài liệu liên quan của nhân viên này
        $laborContract = \App\Models\HopDong::where('NhanVienId', $hopDong->NhanVienId)
            ->where('Loai', 'not like', 'nda%')
            ->where('TrangThai', 1)
            ->first();

        $ndaContract = \App\Models\HopDong::where('NhanVienId', $hopDong->NhanVienId)
            ->where('Loai', 'like', 'nda%')
            ->where('TrangThai', 1)
            ->first();

        // Lấy tất cả danh mục phụ cấp
        $allAllowances = \App\Models\DmPlHopDong::active()->get();

        $this->checkAuthority($hopDong);

        return view('contracts.show', compact('hopDong', 'allAllowances', 'laborContract', 'ndaContract'));
    }

    public function XoaNhieu(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            \App\Models\HopDong::whereIn('id', $ids)->delete();
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
        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();
        
        // Lấy danh sách nhân viên có vai trò 'Nhân viên'
        $nhanvien = NhanVien::whereHas('nguoiDung', function($q) {
            $q->whereHas('roles', function($rq) {
                 $rq->where('name', 'Nhân viên');
            });
        })->with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])->get();

        // Lấy danh sách người ký có vai trò 'System Admin'
        $nguoiKyList = NhanVien::whereHas('nguoiDung', function($q) {
            $q->whereHas('roles', function($rq) {
                 $rq->where('name', 'System Admin');
            });
        })->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $ngachLuongs = NgachLuong::with('bacLuongs')->orderBy('Ma')->get();

        $loaiHopDongs = \App\Models\DmLoaiHopDong::all();
        $dmAllowances = \App\Models\DmPlHopDong::active()->get();

        // Mặc định là System Admin đầu tiên (Diệp Thế Chinh)
        $defaultNguoiKyId = $nguoiKyList->first()?->id;

        return view('contracts.create', compact('nhanvien', 'phongban', 'chucvu', 'mucLuongCoSo', 'ngachLuongs', 'loaiHopDongs', 'dmAllowances', 'defaultNguoiKyId', 'nguoiKyList'));
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
            // Process dynamic allowances
            $phu_cap_bhxh = 0;
            $phu_cap_ngoai_bhxh = 0;
            $allowanceData = [];
            
            if ($request->has('allowances')) {
                $allDmAllowances = \App\Models\DmPlHopDong::whereIn('id', array_keys($request->allowances))->get()->keyBy('id');
                foreach ($request->allowances as $id => $val) {
                    $amount = (float) str_replace(['.', ','], '', $val);
                    if ($amount > 0) {
                        $dm = $allDmAllowances->get($id);
                        if ($dm) {
                            if ($dm->is_bhxh) {
                                $phu_cap_bhxh += $amount;
                            } else {
                                $phu_cap_ngoai_bhxh += $amount;
                            }
                            $allowanceData[$id] = ['so_tien' => $amount];
                        }
                    }
                }
            }

            // Map snake_case form inputs to PascalCase database columns
            $data = [
                'NhanVienId' => $validated['nhan_vien_id'],
                'NguoiKyId' => $validated['NguoiKyId'],
                'SoHopDong' => $validated['so_hop_dong'],
                'Loai' => $validated['loai'],
                'loai_hop_dong_id' => $validated['loai_hop_dong_id'],
                'PhongBanId' => $validated['phong_ban_id'],
                'ChucVuId' => $validated['chuc_vu_id'],
                'TrangThai' => $validated['trang_thai'],
                // Salary fields
                'LuongCoBan' => (float) str_replace(['.', ','], '', $request->luong_co_ban),
                'PhuCapChucVu' => 0,
                'PhuCapTrachNhiem' => 0,
                'PhuCapDocHai' => 0,
                'PhuCapThamNien' => 0,
                'PhuCapKhuVuc' => 0,
                'PhuCapAnTrua' => 0,
                'PhuCapXangXe' => 0,
                'PhuCapDienThoai' => 0,
                'PhuCapKhac' => 0,
                'phu_cap_bhxh' => $phu_cap_bhxh,
                'phu_cap_ngoai_bhxh' => $phu_cap_ngoai_bhxh,
                'TongLuong' => (float) str_replace(['.', ','], '', $request->luong_co_ban) + $phu_cap_bhxh + $phu_cap_ngoai_bhxh,
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
            $ngachLuongId = $request->filled('ngach_luong_id') ? (int) $request->ngach_luong_id : null;
            $bacLuongId = $request->filled('bac_luong_id') ? (int) $request->bac_luong_id : null;

            $hopDong = \DB::transaction(function () use ($data, $ngachLuongId, $bacLuongId, $allowanceData) {
                // Nếu hợp đồng mới là đang hoạt động (TrangThai == 1)
                if ($data['TrangThai'] == 1) {
                    $isNewNDA = str_starts_with($data['Loai'] ?? '', 'nda');
                    // Tìm và vô hiệu hóa các hợp đồng cùng loại (Labor hoặc NDA) đang hoạt động cũ
                    \App\Models\HopDong::where('NhanVienId', $data['NhanVienId'])
                        ->where('TrangThai', 1)
                        ->where(function ($query) use ($isNewNDA) {
                            if ($isNewNDA) {
                                $query->where('Loai', 'like', 'nda%');
                            } else {
                                $query->where('Loai', 'not like', 'nda%');
                            }
                        })
                        ->update(['TrangThai' => 0]);
                }

                $hopDong = \App\Models\HopDong::create($data);

                // Save dynamic allowances
                if (!empty($allowanceData)) {
                    $hopDong->phuCaps()->sync($allowanceData);
                }

                // Tự động tạo bản NDA nếu hợp đồng vừa tạo không phải là NDA
                $isNewNDA = str_starts_with($data['Loai'] ?? '', 'nda');
                if (!$isNewNDA) {
                    $currentYear = date('Y');
                    
                    // Tìm số thứ tự tiếp theo cho hợp đồng NDA trong năm nay
                    $latestNDASo = \App\Models\HopDong::where('Loai', 'like', 'nda%')
                        ->where(\DB::raw('YEAR(created_at)'), $currentYear)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    $nextSTT = 1;
                    if ($latestNDASo && preg_match('/^(\d{3})\//', $latestNDASo->SoHopDong, $matches)) {
                        $nextSTT = intval($matches[1]) + 1;
                    }
                    
                    $ndaSoHopDong = str_pad($nextSTT, 3, '0', STR_PAD_LEFT) . "/{$currentYear}/NDA";

                    \App\Models\HopDong::create([
                        'NhanVienId' => $data['NhanVienId'],
                        'NguoiKyId' => $data['NguoiKyId'],
                        'SoHopDong' => $ndaSoHopDong,
                        'Loai' => 'nda',
                        'loai_hop_dong_id' => 7,
                        'PhongBanId' => $data['PhongBanId'],
                        'ChucVuId' => $data['ChucVuId'],
                        'NgayBatDau' => $data['NgayBatDau'],
                        'TrangThai' => 1,
                        'LuongCoBan' => 0,
                        'phu_cap_bhxh' => 0,
                        'phu_cap_ngoai_bhxh' => 0,
                        'TongLuong' => 0,
                    ]);
                }

                // Tự động tạo Phụ lục hợp đồng nếu có phụ cấp
                if (!empty($allowanceData)) {
                    $phuLuc = \App\Models\PhuLucHopDong::create([
                        'HopDongId' => $hopDong->id,
                        'ten_phu_luc' => 'Phụ lục điều chỉnh phụ cấp',
                        'ngay_ky' => $data['NgayBatDau'],
                    ]);
                    
                    // Liên kết các điều khoản phụ cấp vào phụ lục
                    $phuLuc->dieuKhoans()->sync($allowanceData);
                }

                // Ghi diễn biến lương nếu có chọn ngạch/bậc
                if ($ngachLuongId && $bacLuongId) {
                    \App\Models\DienBienLuong::create([
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
        $hopDong = \App\Models\HopDong::with('nhanVien')->findOrFail($id);
        $this->checkAuthority($hopDong);

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
        
        // Sanitize filename (remove / and \)
        $fileName = str_replace(['/', '\\'], '-', $fileName);

        $tempPath = storage_path('app/contracts/temp_' . uniqid() . '.docx');
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function saveSignature(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:hop_dong,phu_luc',
            'position' => 'required|in:employee,company',
            'signature' => 'required|string', // base64 string
        ]);

        $modelClass = $request->type === 'hop_dong' ? \App\Models\HopDong::class : \App\Models\PhuLucHopDong::class;
        $model = $modelClass::findOrFail($request->id);

        // Security check for signature
        $user = auth()->user();
        $isHN = $request->position === 'employee';
        
        if (!$user->can('Xem Danh Sách Hợp Đồng') && !$user->hasRole('Admin')) {
            if ($isHN) {
                // Employee signing their own contract
                $nhanVienId = ($request->type === 'hop_dong') 
                    ? $model->NhanVienId 
                    : ($model->hopDong ? $model->hopDong->NhanVienId : null);
                
                if (!$user->nhanVien || $nhanVienId != $user->nhanVien->id) {
                    return response()->json(['success' => false, 'message' => 'Bạn không thể ký hợp đồng này.'], 403);
                }
            } else {
                // Employee trying to sign as Company
                $nguoiKyId = ($request->type === 'hop_dong')
                    ? $model->NguoiKyId
                    : ($model->hopDong ? $model->hopDong->NguoiKyId : null);

                if ($user->id != $nguoiKyId) {
                    return response()->json(['success' => false, 'message' => 'Bạn không có quyền ký với tư cách đại diện công ty.'], 403);
                }
            }
        }

        // Get or create signature record
        $kySo = \App\Models\HopDongKySo::firstOrCreate([
            'signable_id' => $model->id,
            'signable_type' => $modelClass,
        ]);

        // Prevent re-signing for employee
        if ($request->position === 'employee' && $kySo->chu_ky_nhan_vien) {
            return response()->json(['success' => false, 'message' => 'Bạn đã ký tên rồi, không thể ký lại.'], 400);
        }

        // Process signature image
        $signatureData = $request->signature;
        $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureData = str_replace(' ', '+', $signatureData);
        $imageBinary = base64_decode($signatureData);

        $fileName = 'sig_' . $request->type . '_' . $model->id . '_' . $request->position . '_' . time() . '.png';
        $filePath = 'signatures/' . $fileName;
        \Illuminate\Support\Facades\Storage::disk('public')->put($filePath, $imageBinary);

        // Update database
        if ($request->position === 'employee') {
            $kySo->update([
                'nhan_vien_id' => $model->NhanVienId ?? ($model->HopDongId ? $model->hopDong->NhanVienId : null),
                'chu_ky_nhan_vien' => $filePath,
                'ngay_ky_nhan_vien' => now(),
            ]);
        } else {
            $kySo->update([
                'nguoi_dai_dien_id' => $model->NguoiKyId ?? ($model->HopDongId ? $model->hopDong->NguoiKyId : null),
                'chu_ky_dai_dien' => $filePath,
                'ngay_ky_dai_dien' => now(),
            ]);
        }

        return response()->json([
            'success' => true, 
            'image_url' => asset('storage/' . $filePath)
        ]);
    }

    public function downloadNDAWord($id)
    {
        $hopDong = \App\Models\HopDong::with(['nhanVien', 'nguoiKy.ttCongViec.chucVu', 'chucVu', 'phongBan', 'kySo'])->findOrFail($id);
        $this->checkAuthority($hopDong);
        $nv = $hopDong->nhanVien;
        $nguoiKy = $hopDong->nguoiKy;

        $templatePath = storage_path('app/contracts/template_nda.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Không tìm thấy file mẫu NDA. Bạn cần chạy lệnh tạo mẫu trước.');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $ngayBatDau = \Carbon\Carbon::parse($hopDong->NgayBatDau);
        $now = \Carbon\Carbon::now();

        // Common Placeholders
        $templateProcessor->setValue('TenNhanVien', $nv ? strtoupper($nv->Ten) : '');
        $templateProcessor->setValue('NgaySinh', $nv && $nv->NgaySinh ? \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') : '');
        $templateProcessor->setValue('DiaChiHK', $nv ? ($nv->DiaChi ?? '') : '');
        $templateProcessor->setValue('CHHT', $nv ? ($nv->DiaChi ?? '') : '');
        $templateProcessor->setValue('SoCCCD', $nv ? ($nv->SoCCCD ?? '') : '');
        $templateProcessor->setValue('NgayCapCCCD', $nv && $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d/m/Y') : '');
        
        $templateProcessor->setValue('SoHopDong', $hopDong->SoHopDong ?? '');
        $templateProcessor->setValue('NgayBatDau', $ngayBatDau->day);
        $templateProcessor->setValue('ThangBatDau', $ngayBatDau->month);
        $templateProcessor->setValue('NamBatDau', $ngayBatDau->year);

        $templateProcessor->setValue('NgayPL', $now->day);
        $templateProcessor->setValue('ThangPL', $now->month);
        $templateProcessor->setValue('NamPL', $now->year);

        $templateProcessor->setValue('PhongBan', $hopDong->phongBan ? $hopDong->phongBan->TenPhongBan : ($nv->ttCongViec?->phongBan?->TenPhongBan ?? ''));

        // Embed Signatures
        $kySo = $hopDong->kySo;
        if ($kySo) {
            if ($kySo->chu_ky_nhan_vien && \Illuminate\Support\Facades\Storage::disk('public')->exists($kySo->chu_ky_nhan_vien)) {
                $templateProcessor->setImageValue('ChuKyNhanVien', [
                    'path' => storage_path('app/public/' . $kySo->chu_ky_nhan_vien),
                    'width' => 120,
                    'height' => 60,
                    'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue('ChuKyNhanVien', '');
            }
        } else {
            $templateProcessor->setValue('ChuKyNhanVien', '');
        }

        // File download handling
        // Filename format: {SO_HOP_DONG}_Non-Disclosure Agreement -{TEN NHAN VIEN}
        $soHopDong = $hopDong->SoHopDong ?? 'NDA';
        $tenNhanVien = $nv ? $nv->Ten : $hopDong->id;
        $fileName = $soHopDong . '_Non-Disclosure Agreement -' . $tenNhanVien . '.docx';
        
        // Sanitize filename (remove / and \)
        $fileName = str_replace(['/', '\\'], '-', $fileName);

        $tempPath = storage_path('app/contracts/temp_nda_' . uniqid() . '.docx');
        $templateProcessor->saveAs($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function downloadPhuLucWord($id)
    {
        $hopDong = \App\Models\HopDong::with(['nhanVien', 'nguoiKy.ttCongViec.chucVu'])->findOrFail($id);
        $this->checkAuthority($hopDong);
        $phuLuc = \App\Models\PhuLucHopDong::where('HopDongId', $id)->with(['dieuKhoans', 'kySo'])->latest()->first();

        if (!$phuLuc) {
            return redirect()->back()->with('error', 'Không tìm thấy phụ lục cho hợp đồng này.');
        }

        $nv = $hopDong->nhanVien;
        $nguoiKy = $hopDong->nguoiKy;

        $templatePath = storage_path('app/contracts/template_phu_luc.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Không tìm thấy file mẫu Phụ lục.');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
        $ngayKyPL = \Carbon\Carbon::parse($phuLuc->ngay_ky);
        $ngayKyHD = \Carbon\Carbon::parse($hopDong->NgayKy);

        // Core Placeholders
        $templateProcessor->setValue('SoHopDong', $hopDong->SoHopDong ?? '');
        $templateProcessor->setValue('NgayKy', $ngayKyPL->day);
        $templateProcessor->setValue('ThangKy', $ngayKyPL->month);
        $templateProcessor->setValue('NamKy', $ngayKyPL->year);
        
        $templateProcessor->setValue('TenDaiDien', $nguoiKy ? $nguoiKy->Ten : 'DIỆP THẾ CHINH');
        $templateProcessor->setValue('ChucVuDaiDien', $nguoiKy ? ($nguoiKy->ttCongViec?->chucVu?->TenChucVu ?? 'Giám đốc') : 'Giám đốc');

        $templateProcessor->setValue('TenNhanVien', $nv ? strtoupper($nv->Ten) : '');
        $templateProcessor->setValue('NgaySinh', $nv && $nv->NgaySinh ? \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') : '');
        $templateProcessor->setValue('DiaChi', $nv ? ($nv->DiaChi ?? '') : '');
        $templateProcessor->setValue('SoCCCD', $nv ? ($nv->SoCCCD ?? '') : '');
        $templateProcessor->setValue('NgayCap', $nv && $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d/m/Y') : '');
        
        $templateProcessor->setValue('NgayKyHD', $ngayKyHD->format('d/m/Y'));
        $templateProcessor->setValue('NgayBatDau', \Carbon\Carbon::parse($phuLuc->ngay_hieu_luc)->format('d/m/Y'));
        
        $tongPhuCap = $phuLuc->dieuKhoans->sum('pivot.so_tien');
        $templateProcessor->setValue('TongPhuCap', number_format($tongPhuCap, 0, ',', '.'));
        $templateProcessor->setValue('TongPhuCapChu', self::docSoThanhChu($tongPhuCap));

        // Phụ cấp breakdown (Single line layout)
        $dieuKhoans = $phuLuc->dieuKhoans;
        $templateProcessor->cloneRow('dong_phu_cap', count($dieuKhoans));
        foreach($dieuKhoans as $i => $dk) {
            $rowNum = $i + 1;
            // Format: 1.2.1 Tiền ăn trưa: 770.000 đồng/ tháng
            $dongPhuCap = "1.2.{$rowNum} " . $dk->noi_dung . ": " . number_format($dk->pivot->so_tien, 0, ',', '.') . " đồng/ tháng";
            $templateProcessor->setValue('dong_phu_cap#'.$rowNum, $dongPhuCap);
        }

        // Embed Signatures
        $kySo = $phuLuc->kySo;
        if ($kySo) {
            if ($kySo->chu_ky_nhan_vien && \Illuminate\Support\Facades\Storage::disk('public')->exists($kySo->chu_ky_nhan_vien)) {
                $templateProcessor->setImageValue('ChuKyNhanVien', [
                    'path' => storage_path('app/public/' . $kySo->chu_ky_nhan_vien),
                    'width' => 120,
                    'height' => 60,
                    'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue('ChuKyNhanVien', '');
            }

            if ($kySo->chu_ky_dai_dien && \Illuminate\Support\Facades\Storage::disk('public')->exists($kySo->chu_ky_dai_dien)) {
                $templateProcessor->setImageValue('ChuKyDaiDien', [
                    'path' => storage_path('app/public/' . $kySo->chu_ky_dai_dien),
                    'width' => 120,
                    'height' => 60,
                    'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue('ChuKyDaiDien', '');
            }
        } else {
            $templateProcessor->setValue('ChuKyNhanVien', '');
            $templateProcessor->setValue('ChuKyDaiDien', '');
        }

        // Embed Signatures
        $kySo = $phuLuc->kySo;
        if ($kySo) {
            if ($kySo->chu_ky_nhan_vien && \Illuminate\Support\Facades\Storage::disk('public')->exists($kySo->chu_ky_nhan_vien)) {
                $templateProcessor->setImageValue('ChuKyNhanVien', [
                    'path' => storage_path('app/public/' . $kySo->chu_ky_nhan_vien),
                    'width' => 120,
                    'height' => 60,
                    'ratio' => true
                ]);
            } else { $templateProcessor->setValue('ChuKyNhanVien', ''); }

            if ($kySo->chu_ky_dai_dien && \Illuminate\Support\Facades\Storage::disk('public')->exists($kySo->chu_ky_dai_dien)) {
                $templateProcessor->setImageValue('ChuKyDaiDien', [
                    'path' => storage_path('app/public/' . $kySo->chu_ky_dai_dien),
                    'width' => 120,
                    'height' => 60,
                    'ratio' => true
                ]);
            } else { $templateProcessor->setValue('ChuKyDaiDien', ''); }
        } else {
            $templateProcessor->setValue('ChuKyNhanVien', '');
            $templateProcessor->setValue('ChuKyDaiDien', '');
        }

        // File download handling
        $fileName = 'PLHD Lao Dong_' . ($nv ? $nv->Ten : $id) . '.docx';
        $fileName = str_replace(['/', '\\'], '-', $fileName);

        $tempPath = storage_path('app/contracts/temp_pl_' . uniqid() . '.docx');
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
            'nguoiKy',
            'kySo'
        ])->findOrFail($id);

        $this->checkAuthority($hopDong);

        // Chọn template phù hợp với loại hợp đồng
        $view = match(true) {
            str_starts_with($hopDong->Loai ?? '', 'nda') => 'contracts.template.nda',
            default => 'contracts.template', // Hợp đồng lao động mặc định
        };

        if ($view === 'contracts.template.nda') {
            $nv = $hopDong->nhanVien;
            $ngayBatDau = \Carbon\Carbon::parse($hopDong->NgayBatDau);
            $now = \Carbon\Carbon::now();

            $data = [
                'hopDong' => $hopDong,
                'employee_name' => $nv ? strtoupper($nv->Ten) : '',
                'employee_dob' => $nv && $nv->NgaySinh ? \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') : '',
                'employee_permanent_address' => $nv ? ($nv->DiaChi ?? '') : '',
                'employee_current_address' => $nv ? ($nv->DiaChi ?? '') : '',
                'employee_cccd' => $nv ? ($nv->SoCCCD ?? '') : '',
                'employee_cccd_date' => $nv && $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d/m/Y') : '',
                'contract_number' => $hopDong->SoHopDong ?? '',
                'contract_date' => $ngayBatDau->format('d/m/Y'),
                'sign_day' => $now->day,
                'sign_month' => $now->month,
                'sign_year' => $now->year,
                'employee_position' => $hopDong->chucVu ? $hopDong->chucVu->TenChucVu : ($nv->ttCongViec?->chucVu?->TenChucVu ?? ''),
                'employee_department' => $hopDong->phongBan ? $hopDong->phongBan->TenPhongBan : ($nv->ttCongViec?->phongBan?->TenPhongBan ?? ''),
                'kySo' => $hopDong->kySo,
                'isAdmin' => auth()->user() && (auth()->user()->hasRole('Admin') || auth()->user()->id == $hopDong->NguoiKyId),
                'canSign' => true,
            ];
            
            return view($view, $data);
        }

        return view($view, compact('hopDong'));
    }

    public function printPhuLuc($id)
    {
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'chucVu',
            'phongBan',
            'nguoiKy',
            'phuLucs.dieuKhoans',
            'phuLucs.kySo'
        ])->findOrFail($id);

        $this->checkAuthority($hopDong);

        $phuLuc = $hopDong->phuLucs()->latest()->first();
        
        if (!$phuLuc) {
            return redirect()->back()->with('error', 'Hợp đồng này chưa có phụ lục.');
        }

        $kySo = $phuLuc->kySo;
        $isAdmin = auth()->user() && (auth()->user()->hasRole('Admin') || auth()->user()->id == $hopDong->NguoiKyId);
        $canSign = true;

        return view('contracts.template.pluc', compact('hopDong', 'phuLuc', 'kySo', 'isAdmin', 'canSign'));
    }


    public function SuaView($id)
    {
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'nguoiKy',
            'phongBan',
            'chucVu',
            'loaiHopDong',
            'phuCaps',
            'dienBienLuong'
        ])->findOrFail($id);

        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();
        
        // Lấy danh sách nhân viên có vai trò 'Nhân viên'
        $nhanvien = NhanVien::whereHas('nguoiDung', function($q) {
            $q->whereHas('roles', function($rq) {
                 $rq->where('name', 'Nhân viên');
            });
        })->with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])->get();

        // Lấy danh sách người ký có vai trò 'System Admin'
        $nguoiKyList = NhanVien::whereHas('nguoiDung', function($q) {
            $q->whereHas('roles', function($rq) {
                 $rq->where('name', 'System Admin');
            });
        })->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $ngachLuongs = NgachLuong::with('bacLuongs')->orderBy('Ma')->get();

        $loaiHopDongs = \App\Models\DmLoaiHopDong::all();
        $dmAllowances = \App\Models\DmPlHopDong::active()->get();

        return view('contracts.edit', compact('hopDong', 'nhanvien', 'phongban', 'chucvu', 'mucLuongCoSo', 'ngachLuongs', 'loaiHopDongs', 'dmAllowances', 'nguoiKyList'));
    }

    public function CapNhat(Request $request, $id)
    {
        $hopDong = \App\Models\HopDong::findOrFail($id);

        // Validate incoming request
        $validated = $request->validate([
            'nhan_vien_id' => 'required|exists:nhan_viens,id',
            'NguoiKyId' => 'required|exists:nhan_viens,id',
            'so_hop_dong' => 'required|string|max:255',
            'loai_hop_dong_id' => 'required|integer',
            'loai' => 'required|string|max:50',

            'phong_ban_id' => 'required|exists:dm_phong_bans,id',
            'chuc_vu_id' => 'required|exists:dm_chuc_vus,id',
            'NgayBatDau' => 'required|date_format:d/m/Y',
            'NgayKetThuc' => 'nullable|date_format:d/m/Y',
            'trang_thai' => 'required|in:0,1,2',
            // Salary fields
            'luong_co_ban' => 'required|numeric|min:5310000',
            'tong_luong' => 'required|numeric|min:0',
            // Dynamic allowances
            'allowances' => 'nullable|array',
            'allowances.*' => 'nullable|string',
            // Ngạch & bậc lương
            'ngach_luong_id' => 'nullable|exists:ngach_luongs,id',
            'bac_luong_id' => 'nullable|exists:bac_luongs,id',
            // File upload
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            // Process dynamic allowances
            $phu_cap_bhxh = 0;
            $phu_cap_ngoai_bhxh = 0;
            $allowanceData = [];
            
            if ($request->has('allowances')) {
                $allDmAllowances = \App\Models\DmPlHopDong::all()->keyBy('id');
                foreach ($request->allowances as $id => $val) {
                    $amount = (float) str_replace(['.', ','], '', $val);
                    if ($amount > 0) {
                        $dm = $allDmAllowances->get($id);
                        if ($dm) {
                            if ($dm->is_bhxh) {
                                $phu_cap_bhxh += $amount;
                            } else {
                                $phu_cap_ngoai_bhxh += $amount;
                            }
                            $allowanceData[$id] = ['so_tien' => $amount];
                        }
                    }
                }
            }

            $data = [
                'NhanVienId' => $validated['nhan_vien_id'],
                'NguoiKyId' => $validated['NguoiKyId'],
                'SoHopDong' => $validated['so_hop_dong'],
                'Loai' => $validated['loai'],
                'loai_hop_dong_id' => $validated['loai_hop_dong_id'],
                'PhongBanId' => $validated['phong_ban_id'],
                'ChucVuId' => $validated['chuc_vu_id'],
                'TrangThai' => $validated['trang_thai'],
                // Salary fields
                'LuongCoBan' => (float) str_replace(['.', ','], '', $request->luong_co_ban),
                'PhuCapChucVu' => 0,
                'PhuCapTrachNhiem' => 0,
                'PhuCapDocHai' => 0,
                'PhuCapThamNien' => 0,
                'PhuCapKhuVuc' => 0,
                'PhuCapAnTrua' => 0,
                'PhuCapXangXe' => 0,
                'PhuCapDienThoai' => 0,
                'PhuCapKhac' => 0,
                'phu_cap_bhxh' => $phu_cap_bhxh,
                'phu_cap_ngoai_bhxh' => $phu_cap_ngoai_bhxh,
                'TongLuong' => (float) str_replace(['.', ','], '', $request->luong_co_ban) + $phu_cap_bhxh + $phu_cap_ngoai_bhxh,
            ];

            $data['NgayBatDau'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['NgayBatDau'])->format('Y-m-d');
            if (!empty($validated['NgayKetThuc'])) {
                $data['NgayKetThuc'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['NgayKetThuc'])->format('Y-m-d');
            } else {
                $data['NgayKetThuc'] = null;
            }

            if ($request->hasFile('file')) {
                // Remove old file if exists
                if ($hopDong->File && file_exists(public_path($hopDong->File))) {
                    @unlink(public_path($hopDong->File));
                }
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/contracts'), $filename);
                $data['File'] = 'uploads/contracts/' . $filename;
            }

            \DB::transaction(function () use ($hopDong, $data, $request, $allowanceData) {
                // 1. Detect financial changes
                $oldLuongCoBan = (float) $hopDong->LuongCoBan;
                $newLuongCoBan = (float) $data['LuongCoBan'];
                
                $oldAllowances = $hopDong->phuCaps->pluck('pivot.so_tien', 'id')->toArray();
                $newAllowances = collect($allowanceData)->map(fn($item) => (float) $item['so_tien'])->toArray();

                // Sort keys to ensure consistent comparison
                ksort($oldAllowances);
                ksort($newAllowances);

                $financialChanged = ($oldLuongCoBan !== $newLuongCoBan) || ($oldAllowances != $newAllowances);

                // 2. Update current state (always update the main contract's allowances)
                $hopDong->update($data);
                $hopDong->phuCaps()->sync($allowanceData);

                // 3. Handle Addendum History
                if ($financialChanged) {
                    // Deactivate old addendums for this contract
                    \App\Models\PhuLucHopDong::where('HopDongId', $hopDong->id)
                        ->update(['TrangThai' => 0]);

                    // Create a NEW addendum as the active one
                    $phuLuc = \App\Models\PhuLucHopDong::create([
                        'HopDongId' => $hopDong->id,
                        'ten_phu_luc' => 'Phụ lục điều chỉnh tiền lương & phụ cấp',
                        'ngay_ky' => $data['NgayBatDau'],
                        'TrangThai' => 1
                    ]);
                    
                    // Link the new allowances to this specific addendum
                    $phuLuc->dieuKhoans()->sync($allowanceData);
                }

                // Update DienBienLuong if ngach/bac changed
                if ($request->filled('ngach_luong_id') && $request->filled('bac_luong_id')) {
                    \App\Models\DienBienLuong::updateOrCreate(
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
            'phongBan',
            'chucVu',
            'loaiHopDong',
            'phuCaps'
        ])->findOrFail($id);

        // Fetch related salary progression (Ngạch/Bậc)
        $oldDienBien = \App\Models\DienBienLuong::where('HopDongId', $id)->first();

        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();

        // Lấy danh sách nhân viên có vai trò 'Nhân viên'
        $nhanvien = NhanVien::whereHas('nguoiDung', function($q) {
            $q->whereHas('roles', function($rq) {
                 $rq->where('name', 'Nhân viên');
            });
        })->with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])->get();

        // Lấy danh sách người ký có vai trò 'System Admin'
        $nguoiKyList = NhanVien::whereHas('nguoiDung', function($q) {
            $q->whereHas('roles', function($rq) {
                 $rq->where('name', 'System Admin');
            });
        })->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $ngachLuongs = NgachLuong::with('bacLuongs')->orderBy('Ma')->get();

        $isRenew = true;
        $loaiHopDongs = \App\Models\DmLoaiHopDong::all();
        $dmAllowances = \App\Models\DmPlHopDong::active()->get();

        // Mặc định là System Admin đầu tiên (Diệp Thế Chinh)
        $defaultNguoiKyId = $nguoiKyList->first()?->id;

        return view('contracts.create', compact('oldContract', 'oldDienBien', 'isRenew', 'nhanvien', 'phongban', 'chucvu', 'mucLuongCoSo', 'ngachLuongs', 'loaiHopDongs', 'dmAllowances', 'defaultNguoiKyId', 'nguoiKyList'));
    }

    /**
     * Chuyển số thành chữ (Tiếng Việt)
     */
    public static function docSoThanhChu($number)
    {
        $dictionary = [
            0 => 'không', 1 => 'một', 2 => 'hai', 3 => 'ba', 4 => 'bốn', 5 => 'năm',
            6 => 'sáu', 7 => 'bảy', 8 => 'tám', 9 => 'chín', 10 => 'mười',
            11 => 'mười một', 12 => 'mười hai', 13 => 'mười ba', 14 => 'mười bốn',
            15 => 'mười lăm', 16 => 'mười sáu', 17 => 'mười bảy', 18 => 'mười tám',
            19 => 'mười chín', 20 => 'hai mươi', 30 => 'ba mươi', 40 => 'bốn mươi',
            50 => 'năm mươi', 60 => 'sáu mươi', 70 => 'bảy mươi', 80 => 'tám mươi',
            90 => 'chín mươi', 100 => 'trăm', 1000 => 'nghìn', 1000000 => 'triệu',
            1000000000 => 'tỷ'
        ];

        if (!is_numeric($number)) return false;
        if ($number == 0) return $dictionary[0];

        $string = "";
        
        // Tỷ
        if ($number >= 1000000000) {
            $billions = floor($number / 1000000000);
            $string .= self::docSoThanhChu($billions) . ' tỷ ';
            $number %= 1000000000;
        }

        // Triệu
        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $string .= self::docSoThanhChu($millions) . ' triệu ';
            $number %= 1000000;
        }

        // Nghìn
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $string .= self::docSoThanhChu($thousands) . ' nghìn ';
            $number %= 1000;
        }

        // Trăm
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $string .= $dictionary[$hundreds] . ' trăm ';
            $number %= 100;
            if ($number > 0 && $number < 10) $string .= 'lẻ ';
        }

        // Chục & Đơn vị
        if ($number > 0) {
            if ($number < 20) {
                $string .= $dictionary[$number];
            } else {
                $tens = floor($number / 10) * 10;
                $units = $number % 10;
                $string .= $dictionary[$tens];
                if ($units) {
                    if ($units == 1) $string .= ' mốt';
                    elseif ($units == 5) $string .= ' lăm';
                    else $string .= ' ' . $dictionary[$units];
                }
            }
        }

        return mb_convert_case(trim($string), MB_CASE_TITLE, "UTF-8");
    }

    private function checkAuthority($hopDong)
    {
        $user = auth()->user();
        if ($user->can('Xem Danh Sách Hợp Đồng') || $user->hasRole('Admin')) {
            return true;
        }

        if ($user->can('Xem Hợp Đồng Cá Nhân')) {
            if ($user->nhanVien && $hopDong->NhanVienId == $user->nhanVien->id) {
                return true;
            }
        }

        abort(403, 'Bạn không có quyền truy cập thông tin này.');
    }
}
