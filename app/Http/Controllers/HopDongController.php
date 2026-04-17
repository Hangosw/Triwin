<?php

namespace App\Http\Controllers;

use App\Models\DienBienLuong;
use App\Models\DmChucVu;
use App\Models\DmPhongBan;

use App\Models\NhanVien;

use App\Models\PhuLucHopDong;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HopDongController extends Controller
{
    public function DanhSachView()
    {
        return view('contracts.index');
    }

    public function DataHopDong(Request $request)
    {
        $query = \App\Models\HopDong::with(['nhanVien', 'loaiHopDong'])
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
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = ['id', 'NhanVienId', 'Loai', 'NgayBatDau', 'TongLuong', 'TrangThai'];
            if (isset($columns[$columnIndex])) {
                $query->orderBy($columns[$columnIndex], $columnDir);
            }
        }

        // Secondary sort to ensure newest first
        $query->orderBy('id', 'desc');

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
            'appendices.hopDongPL',
            'parentLink'
        ])->findOrFail($id);

        $this->checkAuthority($hopDong);

        // 1. Determine the Root Contract to show the full tree
        // The tree always starts from the original contract, regardless of which version we are viewing.
        $rootHopDong = $hopDong;
        if ($hopDong->parentLink) {
            $rootHopDong = \App\Models\HopDong::with(['appendices.hopDongPL'])->find($hopDong->parentLink->HopDongGocId);
        }

        // Determine category (Labor vs NDA)
        $isNDA = str_starts_with($hopDong->Loai ?? '', 'nda');

        // Fetch ALL related historical contracts for this employee of the same category
        // (This might be redundant now with the tree, but keeping for compatibility)
        $historyContracts = \App\Models\HopDong::where('NhanVienId', $hopDong->NhanVienId)
            ->where(function ($q) use ($isNDA) {
                if ($isNDA) {
                    $q->where('Loai', 'like', 'nda%');
                } else {
                    $q->where('Loai', 'not like', 'nda%');
                }
            })
            ->with(['appendices.hopDongPL', 'loaiHopDong'])
            ->orderBy('created_at', 'desc')
            ->get();

        $allContractIds = $historyContracts->pluck('id')->toArray();

        // Fetch activity logs related to these contracts
        $activityLogs = \App\Models\LichSu::where('DoiTuongLoai', 'HopDong')
            ->whereIn('DoiTuongId', $allContractIds)
            ->with('nguoiDung')
            ->orderBy('CreatedAt', 'desc')
            ->get();

        // Tìm tài liệu liên quan của nhân viên này
        $laborContract = \App\Models\HopDong::where('NhanVienId', $hopDong->NhanVienId)
            ->where('Loai', 'not like', 'nda%')
            ->where('TrangThai', 1)
            ->first();

        $ndaContract = \App\Models\HopDong::where('NhanVienId', $hopDong->NhanVienId)
            ->where('Loai', 'like', 'nda%')
            ->where('TrangThai', 1)
            ->first();

        return view('contracts.show', compact(
            'hopDong',
            'rootHopDong',
            'laborContract',
            'ndaContract',
            'historyContracts',
            'activityLogs'
        ));
    }

    public function XoaNhieu(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            try {
                \Illuminate\Support\Facades\DB::beginTransaction();

                $allRelatedIds = [];
                $selectedContracts = \App\Models\HopDong::with(['parentLink'])->whereIn('id', $ids)->get();

                foreach ($selectedContracts as $hd) {
                    // Xác định ID của hợp đồng gốc
                    $rootId = $hd->parentLink ? $hd->parentLink->HopDongGocId : $hd->id;
                    $allRelatedIds[] = $rootId;

                    // Lấy tất cả ID của các phụ lục thuộc hợp đồng gốc này
                    $appendixIds = \App\Models\PhuLucHopDong::where('HopDongGocId', $rootId)->pluck('HopDongPLId')->toArray();
                    $allRelatedIds = array_merge($allRelatedIds, $appendixIds);
                }

                $allRelatedIds = array_unique($allRelatedIds);

                // Cập nhật trạng thái thành 2 (Bị hủy/Thanh lý) thay vì xóa cứng
                \App\Models\HopDong::whereIn('id', $allRelatedIds)->update(['TrangThai' => 2]);

                \Illuminate\Support\Facades\DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đã hủy ' . count($allRelatedIds) . ' hợp đồng và phụ lục liên quan thành công!'
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi khi hủy hợp đồng: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Vui lòng chọn hợp đồng để hủy'
        ], 400);
    }

    public function TaoView()
    {
        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();

        // Lấy danh sách nhân viên có vai trò 'Nhân viên'
        $nhanvien = NhanVien::whereHas('nguoiDung', function ($q) {
            $q->whereHas('roles', function ($rq) {
                $rq->where('name', 'Nhân viên');
            });
        })->with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])
            ->withCount([
                'thanNhans as phu_thuoc_count' => function ($query) {
                    $query->where('TrangThai', 1);
                }
            ])->get();

        // Lấy danh sách toàn bộ nhân viên để ký tên (đã gỡ bỏ giới hạn System Admin)
        $nguoiKyList = NhanVien::orderBy('Ten')->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $loaiHopDongs = \App\Models\DmLoaiHopDong::where('TrangThai', 'mo')->get();

        // Lấy người ký từ cấu hình signer_id (trả về null nếu chưa cấu hình)
        $defaultNguoiKyId = \App\Models\SystemConfig::getValue('signer_id');

        return view('contracts.create', compact('nhanvien', 'phongban', 'chucvu', 'mucLuongCoSo', 'loaiHopDongs', 'defaultNguoiKyId', 'nguoiKyList'));
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
            'NgayKetThuc' => 'required_unless:loai,KXDH|nullable|date_format:d/m/Y',
            'trang_thai' => 'required|in:0,1,2',
            'ngay_phep_nam' => 'nullable|integer|min:0',
            'ngay_phep_kha_dung' => 'nullable|numeric|min:0',
            // Salary fields
            'luong_co_ban' => 'required|numeric|min:0',
            'phu_cap' => 'nullable|array',
            'phu_cap.*.name' => 'required_with:phu_cap|string',
            'phu_cap.*.amount' => 'required_with:phu_cap|numeric|min:0',
            // File upload
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            // Process dynamic allowances
            $tong_phu_cap = 0;
            $phuCapData = [];

            if ($request->has('phu_cap')) {
                foreach ($request->phu_cap as $pc) {
                    if (!empty($pc['name']) && isset($pc['amount'])) {
                        $amount = (float) $pc['amount'];
                        $tong_phu_cap += $amount;
                        $phuCapData[] = [
                            'name' => $pc['name'],
                            'amount' => $amount
                        ];
                    }
                }
            }

            $nv = \App\Models\NhanVien::findOrFail($validated['nhan_vien_id']);
            $loaiHD = \App\Models\DmLoaiHopDong::find($validated['loai_hop_dong_id']);
            $currentYear = now()->year;
            $maLoai = $loaiHD ? $loaiHD->MaLoai : 'HD';

            // New format: {MaNV}/Year/MaLoai (No prefix #)
            $soHopDong = "{$nv->Ma}/{$currentYear}/{$maLoai}";

            // Map snake_case form inputs to PascalCase database columns
            $data = [
                'NhanVienId' => $validated['nhan_vien_id'],
                'NguoiKyId' => $validated['NguoiKyId'],
                'SoHopDong' => $soHopDong,
                'Loai' => $validated['loai'],
                'loai_hop_dong_id' => $validated['loai_hop_dong_id'],
                'PhongBanId' => $validated['phong_ban_id'],
                'ChucVuId' => $validated['chuc_vu_id'],
                'TrangThai' => $validated['trang_thai'],
                // Salary fields
                'LuongCoBan' => (float) $request->luong_co_ban,
                'TongLuong' => (float) $request->luong_co_ban + $tong_phu_cap,
                'NgayPhepNam' => $request->ngay_phep_nam ?? 12,
                'NgayPhepKhaDung' => $request->ngay_phep_kha_dung,
                'PhuCap' => $phuCapData,
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
            $hopDong = \DB::transaction(function () use ($data, $maLoai) {
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

                // NEW: Auto-create NDA for the first contract of an employee
                // Only if the current contract is NOT an NDA itself
                $isLaborContract = !str_starts_with($data['Loai'] ?? '', 'nda');
                if ($isLaborContract) {
                    $existingContractsCount = \App\Models\HopDong::where('NhanVienId', $data['NhanVienId'])
                        ->where('id', '!=', $hopDong->id)
                        ->count();

                    if ($existingContractsCount === 0) {
                        \App\Models\HopDong::create([
                            'NhanVienId' => $data['NhanVienId'],
                            'NguoiKyId' => $data['NguoiKyId'],
                            'SoHopDong' => str_replace($maLoai, 'NDA', $data['SoHopDong']),
                            'Loai' => 'nda',
                            'loai_hop_dong_id' => 8, // ID for NDA contract type
                            'PhongBanId' => $data['PhongBanId'],
                            'ChucVuId' => $data['ChucVuId'],
                            'TrangThai' => 1,
                            'NgayBatDau' => $data['NgayBatDau'],
                            'NgayKetThuc' => null,
                            'LuongCoBan' => 0,
                            'TongLuong' => 0,
                        ]);
                    }
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
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'nguoiKy.ttCongViec.chucVu',
            'loaiHopDong',
            'chucVu'
        ])->findOrFail($id);
        $this->checkAuthority($hopDong);

        $templatePath = storage_path('app/contracts/template_hop_dong.docx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Không tìm thấy file mẫu hợp đồng lao động.');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Variables from NhanVien (Bên B)
        $nv = $hopDong->nhanVien;
        $templateProcessor->setValue('TenNhanVien', $nv ? strtoupper($nv->Ten) : '');
        $templateProcessor->setValue('NgaySinh', $nv && $nv->NgaySinh ? \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') : '');
        $templateProcessor->setValue('ChucDanh', 'Nhân viên'); // Standard occupational info
        $templateProcessor->setValue('DiaChi', $nv ? $nv->DiaChi : '');
        $templateProcessor->setValue('SoCCCD', $nv ? $nv->SoCCCD : '');
        $templateProcessor->setValue('NgayCap', $nv && $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d/m/Y') : '');
        $templateProcessor->setValue('NoiCap', $nv ? $nv->NoiCap : '');

        // Variables from NguoiKy / Company (Bên A)
        $nguoiKy = $hopDong->nguoiKy;
        $templateProcessor->setValue('TenDaiDien', $nguoiKy ? $nguoiKy->Ten : '');
        $templateProcessor->setValue('ChucVuDaiDien', $nguoiKy ? ($nguoiKy->ttCongViec?->chucVu?->TenChucVu ?? 'Giám đốc') : 'Giám đốc');

        // Variables from HopDong
        $templateProcessor->setValue('SoHopDong', $hopDong->SoHopDong ?? '');
        $templateProcessor->setValue('TenLoaiHD', $hopDong->loaiHopDong ? $hopDong->loaiHopDong->TenLoai : 'Hợp đồng lao động');
        $templateProcessor->setValue('NgayBatDau', $hopDong->NgayBatDau ? \Carbon\Carbon::parse($hopDong->NgayBatDau)->format('d/m/Y') : '');
        $templateProcessor->setValue('ChucDanhChiTiet', $hopDong->chucVu ? $hopDong->chucVu->TenChucVu : '');

        $luongCoBan = number_format($hopDong->LuongCoBan ?? 0, 0, ',', '.');
        $templateProcessor->setValue('LuongCoBan', $luongCoBan);

        // Date variables for header/footer
        $now = now();
        $templateProcessor->setValue('NgayKy', $now->day);
        $templateProcessor->setValue('ThangKy', $now->month);
        $templateProcessor->setValue('NamKy', $now->year);

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
                    : ($model->hopDongGoc ? $model->hopDongGoc->NhanVienId : null);

                if (!$user->nhanVien || $nhanVienId != $user->nhanVien->id) {
                    return response()->json(['success' => false, 'message' => 'Bạn không thể ký hợp đồng này.'], 403);
                }
            } else {
                // Employee trying to sign as Company
                $nguoiKyId = ($request->type === 'hop_dong')
                    ? $model->NguoiKyId
                    : ($model->hopDongGoc ? $model->hopDongGoc->NguoiKyId : null);

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
                'nhan_vien_id' => $model->NhanVienId ?? ($model->HopDongGocId ? $model->hopDongGoc->NhanVienId : null),
                'chu_ky_nhan_vien' => $filePath,
                'ngay_ky_nhan_vien' => now(),
            ]);
        } else {
            $kySo->update([
                'nguoi_dai_dien_id' => $model->NguoiKyId ?? ($model->HopDongGocId ? $model->hopDongGoc->NguoiKyId : null),
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
        $phuLuc = \App\Models\PhuLucHopDong::where('HopDongGocId', $id)->with(['kySo'])->latest()->first();

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

        $signerName = $nguoiKy ? $nguoiKy->Ten : \App\Models\NhanVien::find(\App\Models\SystemConfig::getValue('signer_id'))?->Ten ?? 'ĐẠI DIỆN CÔNG TY';
        $templateProcessor->setValue('TenDaiDien', $signerName);
        $templateProcessor->setValue('ChucVuDaiDien', $nguoiKy ? ($nguoiKy->ttCongViec?->chucVu?->TenChucVu ?? 'Giám đốc') : 'Giám đốc');

        $templateProcessor->setValue('TenNhanVien', $nv ? strtoupper($nv->Ten) : '');
        $templateProcessor->setValue('NgaySinh', $nv && $nv->NgaySinh ? \Carbon\Carbon::parse($nv->NgaySinh)->format('d/m/Y') : '');
        $templateProcessor->setValue('DiaChi', $nv ? ($nv->DiaChi ?? '') : '');
        $templateProcessor->setValue('SoCCCD', $nv ? ($nv->SoCCCD ?? '') : '');
        $templateProcessor->setValue('NgayCap', $nv && $nv->NgayCap ? \Carbon\Carbon::parse($nv->NgayCap)->format('d/m/Y') : '');

        $templateProcessor->setValue('NgayKyHD', $ngayKyHD->format('d/m/Y'));
        $templateProcessor->setValue('NgayBatDau', \Carbon\Carbon::parse($phuLuc->ngay_hieu_luc)->format('d/m/Y'));

        if (!empty($hopDong->PhuCap)) {
            $tongPhuCap = collect($hopDong->PhuCap)->sum('amount');
        } else {
            $tongPhuCap = 0;
        }
        $templateProcessor->setValue('TongPhuCap', number_format($tongPhuCap, 0, ',', '.'));
        $templateProcessor->setValue('TongPhuCapChu', self::docSoThanhChu($tongPhuCap));

        // Phụ cấp breakdown (Single line layout)
        $phuCaps = $hopDong->PhuCap ?? [];
        $templateProcessor->cloneRow('dong_phu_cap', count($phuCaps));
        foreach ($phuCaps as $i => $pc) {
            $rowNum = $i + 1;
            // Use 'name' and 'amount' from the JSON array
            $name = $pc['name'] ?? 'Phụ cấp';
            $amount = $pc['amount'] ?? 0;
            $dongPhuCap = "1.1.{$rowNum} {$name}: " . number_format($amount, 0, ',', '.') . " đồng/ tháng";
            $templateProcessor->setValue('dong_phu_cap#' . $rowNum, $dongPhuCap);
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
        $view = match (true) {
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
            'phuLucs.kySo',
            'phuLucs.hopDongPL',
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

    public function downloadPDF($id)
    {
        $hopDong = \App\Models\HopDong::with(['nhanVien', 'chucVu', 'phongBan', 'loaiHopDong', 'nguoiKy', 'kySo'])->findOrFail($id);
        $this->checkAuthority($hopDong);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contracts.template', compact('hopDong'));
        $fileName = 'HDLD_' . str_replace(['/', '\\'], '-', $hopDong->SoHopDong) . '.pdf';

        return $pdf->download($fileName);
    }

    public function downloadNDAPDF($id)
    {
        $hopDong = \App\Models\HopDong::with(['nhanVien', 'chucVu', 'phongBan', 'loaiHopDong', 'nguoiKy', 'kySo'])->findOrFail($id);
        $this->checkAuthority($hopDong);

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contracts.template.nda', $data);
        $fileName = 'NDA_' . str_replace(['/', '\\'], '-', $hopDong->SoHopDong) . '.pdf';

        return $pdf->download($fileName);
    }

    public function downloadPhuLucPDF($id)
    {
        $hopDong = \App\Models\HopDong::with(['nhanVien', 'chucVu', 'phongBan', 'nguoiKy', 'phuLucs.kySo'])->findOrFail($id);
        $this->checkAuthority($hopDong);

        $phuLuc = $hopDong->phuLucs()->latest()->first();
        if (!$phuLuc)
            return redirect()->back()->with('error', 'Hợp đồng này chưa có phụ lục.');

        $kySo = $phuLuc->kySo;
        $isAdmin = auth()->user() && (auth()->user()->hasRole('Admin') || auth()->user()->id == $hopDong->NguoiKyId);
        $canSign = true;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('contracts.template.pluc', compact('hopDong', 'phuLuc', 'kySo', 'isAdmin', 'canSign'));
        $fileName = 'PLHD_' . str_replace(['/', '\\'], '-', $hopDong->SoHopDong) . '.pdf';

        return $pdf->download($fileName);
    }


    public function SuaView($id)
    {
        $hopDong = \App\Models\HopDong::with([
            'nhanVien',
            'nguoiKy',
            'phongBan',
            'chucVu',
            'loaiHopDong'
        ])->findOrFail($id);

        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();

        // Lấy danh sách nhân viên có vai trò 'Nhân viên'
        $nhanvien = NhanVien::whereHas('nguoiDung', function ($q) {
            $q->whereHas('roles', function ($rq) {
                $rq->where('name', 'Nhân viên');
            });
        })->with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])
            ->withCount([
                'thanNhans as phu_thuoc_count' => function ($query) {
                    $query->where('TrangThai', 1);
                }
            ])->get();

        // Lấy danh sách toàn bộ nhân viên để ký tên (đã gỡ bỏ giới hạn System Admin)
        $nguoiKyList = NhanVien::orderBy('Ten')->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        $loaiHopDongs = \App\Models\DmLoaiHopDong::where('TrangThai', 'mo')->get();

        return view('contracts.edit', compact('hopDong', 'nhanvien', 'phongban', 'chucvu', 'mucLuongCoSo', 'loaiHopDongs', 'nguoiKyList'));
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
            'NgayKetThuc' => 'required_unless:loai,KXDH|nullable|date_format:d/m/Y',
            'trang_thai' => 'required|in:0,1,2',
            'ngay_phep_nam' => 'nullable|integer|min:0',
            'ngay_phep_kha_dung' => 'nullable|numeric|min:0',
            // Salary fields
            'luong_co_ban' => 'required|numeric|min:0',
            'phu_cap' => 'nullable|array',
            'phu_cap.*.name' => 'required_with:phu_cap|string',
            'phu_cap.*.amount' => 'required_with:phu_cap|numeric|min:0',
            // File upload
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            // Process dynamic allowances
            $tong_phu_cap = 0;
            $phuCapData = [];

            if ($request->has('phu_cap')) {
                foreach ($request->phu_cap as $pc) {
                    if (!empty($pc['name']) && isset($pc['amount'])) {
                        $amount = (float) $pc['amount'];
                        $tong_phu_cap += $amount;
                        $phuCapData[] = [
                            'name' => $pc['name'],
                            'amount' => $amount
                        ];
                    }
                }
            }

            $nv = \App\Models\NhanVien::findOrFail($validated['nhan_vien_id']);
            $loaiHD = \App\Models\DmLoaiHopDong::find($validated['loai_hop_dong_id']);
            $currentYear = now()->year;
            $maLoai = $loaiHD ? $loaiHD->MaLoai : 'HD';

            // New format: {MaNV}/Year/MaLoai (No prefix #)
            $soHopDong = "{$nv->Ma}/{$currentYear}/{$maLoai}";

            $data = [
                'NhanVienId' => $validated['nhan_vien_id'],
                'NguoiKyId' => $validated['NguoiKyId'],
                'SoHopDong' => $soHopDong,
                'Loai' => $validated['loai'],
                'loai_hop_dong_id' => $validated['loai_hop_dong_id'],
                'PhongBanId' => $validated['phong_ban_id'],
                'ChucVuId' => $validated['chuc_vu_id'],
                'TrangThai' => $validated['trang_thai'],
                // Salary fields
                'LuongCoBan' => (float) $request->luong_co_ban,
                'TongLuong' => (float) $request->luong_co_ban + $tong_phu_cap,
                'NgayPhepNam' => $request->ngay_phep_nam ?? 12,
                'NgayPhepKhaDung' => $request->ngay_phep_kha_dung,
                'PhuCap' => $phuCapData,
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

            \DB::transaction(function () use (&$hopDong, $data, $request, $phuCapData) {
                // 1. Detect core and financial changes for Versioning
                $fieldsToReview = [
                    'SoHopDong',
                    'Loai',
                    'loai_hop_dong_id',
                    'PhongBanId',
                    'ChucVuId',
                    'NgayBatDau',
                    'NgayKetThuc',
                    'TrangThai',
                    'LuongCoBan',
                    'NgayPhepNam'
                ];

                $hasVersioningChanges = false;
                foreach ($fieldsToReview as $field) {
                    $oldVal = $hopDong->{$field};
                    $newVal = $data[$field] ?? null;

                    // Handle date comparison (Carbon objects vs strings)
                    if (($field === 'NgayBatDau' || $field === 'NgayKetThuc') && $oldVal instanceof \Carbon\Carbon) {
                        $oldVal = $oldVal->toDateString();
                    }

                    if ($oldVal != $newVal) {
                        $hasVersioningChanges = true;
                        break;
                    }
                }

                if ($hasVersioningChanges) {
                    // VERSIONING LOGIC: Retire current, Create new branch

                    // 1. Identify the Root Contract ID (HopDongGocId)
                    // If current contract is already an appendix, use its existing GocId.
                    // Otherwise, this IS the root contract.
                    $rootContractId = $hopDong->parentLink ? $hopDong->parentLink->HopDongGocId : $hopDong->id;

                    // A. Retire the current record (Historical version)
                    $hopDong->update([
                        'TrangThai' => 0,
                    ]);

                    // B. Create NEW contract record (The Appendix)
                    // Appendix Number: PLnn/[RootCode]
                    $rootContract = \App\Models\HopDong::find($rootContractId);
                    $appendixCount = \App\Models\PhuLucHopDong::where('HopDongGocId', $rootContractId)->count();
                    $nextSeq = sprintf("%02d", $appendixCount + 1);

                    $newData = array_merge($data, [
                        'SoHopDong' => "PL{$nextSeq}/" . ($rootContract->SoHopDong),
                        'NhanVienId' => $hopDong->NhanVienId,
                        'NguoiKyId' => $hopDong->NguoiKyId,
                        'File' => $data['File'] ?? $hopDong->File,
                        'TrangThai' => 1 // Active
                    ]);

                    $appendixContract = \App\Models\HopDong::create($newData);

                    // C. Link them in phu_luc_hop_dongs
                    $appendixTitle = "Phụ lục " . $nextSeq;

                    \App\Models\PhuLucHopDong::create([
                        'HopDongGocId' => $rootContractId,
                        'HopDongPLId' => $appendixContract->id,
                        'ten_phu_luc' => $appendixTitle,
                        'ngay_ky' => $data['NgayBatDau'],
                        'TrangThai' => 1
                    ]);

                    // For the response/routing
                    $hopDong = $appendixContract;

                } else {
                    // STANDARD UPDATE LOGIC (No versioning changes)
                    $oldData = $hopDong->toArray();
                    $hopDong->update($data);

                    \App\Services\SystemLogService::log(
                        'Cập nhật',
                        'HopDong',
                        $hopDong->id,
                        'Cập nhật thông tin hợp đồng',
                        $oldData,
                        $hopDong->fresh()->toArray()
                    );
                }
            });

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hợp đồng đã được cập nhật thành công!',
                    'redirect_url' => route('hop-dong.info', $hopDong->id)
                ]);
            }

            return redirect()->route('hop-dong.info', $hopDong->id)->with('success', 'Hợp đồng đã được cập nhật thành công!');

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
            'loaiHopDong'
        ])->findOrFail($id);


        $phongban = DmPhongBan::all();
        $chucvu = DmChucVu::all();

        // Lấy danh sách nhân viên có vai trò 'Nhân viên'
        $nhanvien = NhanVien::whereHas('nguoiDung', function ($q) {
            $q->whereHas('roles', function ($rq) {
                $rq->where('name', 'Nhân viên');
            });
        })->with(['ttCongViec.chucVu', 'ttCongViec.phongBan'])->get();

        // Lấy danh sách toàn bộ nhân viên để ký tên (đã gỡ bỏ giới hạn System Admin)
        $nguoiKyList = NhanVien::orderBy('Ten')->get();

        // Get current base salary
        $baseSalary = \App\Models\ThamSoLuong::getCurrentBaseSalary();
        $mucLuongCoSo = $baseSalary ? $baseSalary->MucLuongCoSo : 2340000;

        // Ngạch lương & bậc lương
        $isRenew = true;
        $loaiHopDongs = \App\Models\DmLoaiHopDong::where('TrangThai', 'mo')->get();

        // Lấy người ký từ cấu hình signer_id (trả về null nếu chưa cấu hình)
        $defaultNguoiKyId = \App\Models\SystemConfig::getValue('signer_id');

        return view('contracts.create', compact('oldContract', 'isRenew', 'nhanvien', 'phongban', 'chucvu', 'mucLuongCoSo', 'loaiHopDongs', 'defaultNguoiKyId', 'nguoiKyList'));
    }

    /**
     * Chuyển số thành chữ (Tiếng Việt)
     */
    public static function docSoThanhChu($number)
    {
        $dictionary = [
            0 => 'không',
            1 => 'một',
            2 => 'hai',
            3 => 'ba',
            4 => 'bốn',
            5 => 'năm',
            6 => 'sáu',
            7 => 'bảy',
            8 => 'tám',
            9 => 'chín',
            10 => 'mười',
            11 => 'mười một',
            12 => 'mười hai',
            13 => 'mười ba',
            14 => 'mười bốn',
            15 => 'mười lăm',
            16 => 'mười sáu',
            17 => 'mười bảy',
            18 => 'mười tám',
            19 => 'mười chín',
            20 => 'hai mươi',
            30 => 'ba mươi',
            40 => 'bốn mươi',
            50 => 'năm mươi',
            60 => 'sáu mươi',
            70 => 'bảy mươi',
            80 => 'tám mươi',
            90 => 'chín mươi',
            100 => 'trăm',
            1000 => 'nghìn',
            1000000 => 'triệu',
            1000000000 => 'tỷ'
        ];

        if (!is_numeric($number))
            return false;
        if ($number == 0)
            return $dictionary[0];

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
            if ($number > 0 && $number < 10)
                $string .= 'lẻ ';
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
                    if ($units == 1)
                        $string .= ' mốt';
                    elseif ($units == 5)
                        $string .= ' lăm';
                    else
                        $string .= ' ' . $dictionary[$units];
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
