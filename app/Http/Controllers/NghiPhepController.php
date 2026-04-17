<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DangKyNghiPhep;
use App\Models\QuanLyPhepNam;
use App\Models\LoaiNghiPhep;
use App\Models\DmPhongBan;
use App\Models\CauHinhLichLamViec;
use App\Models\ChamCong;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveApprovalMail;

class NghiPhepController extends Controller
{
    public function DanhSachView(Request $request)
    {
        $phongBanId = $request->phong_ban_id;
        $loaiNghiPhepId = $request->loai_phep_id;
        $trangThai = $request->trang_thai;

        $query = DangKyNghiPhep::with(['nhanVien.ttCongViec.phongBan', 'loaiNghiPhep']);

        if ($phongBanId) {
            $query->whereHas('nhanVien.ttCongViec', function ($q) use ($phongBanId) {
                $q->where('PhongBanId', $phongBanId);
            });
        }

        if ($loaiNghiPhepId) {
            $query->where('LoaiNghiPhepId', $loaiNghiPhepId);
        }

        if ($trangThai !== null && $trangThai !== '') {
            $query->where('TrangThai', $trangThai);
        }

        $leaves = $query->orderBy('TuNgay', 'desc')->get();

        // Stats
        $now = Carbon::now();
        $totalInMonth = DangKyNghiPhep::where(function ($q) use ($now) {
            $q->whereYear('TuNgay', $now->year)->whereMonth('TuNgay', $now->month)
                ->orWhereYear('DenNgay', $now->year)->whereMonth('DenNgay', $now->month);
        })->count();

        $totalCount = DangKyNghiPhep::count();
        $pendingCount = DangKyNghiPhep::where('TrangThai', 2)->count();
        $approvedCount = DangKyNghiPhep::where('TrangThai', 1)->count();
        $rejectedCount = DangKyNghiPhep::where('TrangThai', 0)->count();

        $phongBans = DmPhongBan::all();
        $loaiNghiPheps = LoaiNghiPhep::where('TrangThai', '1')->get();
        $workingSchedule = CauHinhLichLamViec::all();

        return view('leave.index', compact(
            'leaves',
            'totalInMonth',
            'totalCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'phongBans',
            'loaiNghiPheps',
            'workingSchedule'
        ));
    }

    public function DanhSachConLaiView(Request $request)
    {
        $nam = $request->nam ?? date('Y');
        $phongBanId = $request->phong_ban_id;

        // Các loại phép có hạn mức (CoHanMuc = 1) hoặc Phép năm
        $loaiNghiPheps = LoaiNghiPhep::where('TrangThai', '1')
            ->where(function ($q) {
                $q->where('CoHanMuc', 1)
                    ->orWhere('Ten', 'Nghỉ phép năm');
            })->get();

        $query = \App\Models\NhanVien::with([
            'ttCongViec.phongBan',
            'dangKyNghiPheps' => function ($q) use ($nam) {
                $q->whereIn('TrangThai', [1, 2])
                    ->where(function ($qq) use ($nam) {
                        $qq->whereYear('TuNgay', $nam)->orWhereYear('DenNgay', $nam);
                    });
            },
            'quanLyPhepNams' => function ($q) use ($nam) {
                $q->where('Nam', $nam);
            }
        ])->has('ttCongViec');

        if ($phongBanId) {
            $query->whereHas('ttCongViec', function ($q) use ($phongBanId) {
                $q->where('PhongBanId', $phongBanId);
            });
        }

        $nhanViens = $query->get();
        $phongBans = DmPhongBan::all();

        return view('leave.con-lai', compact('nhanViens', 'loaiNghiPheps', 'phongBans', 'nam'));
    }

    /**
     * API lấy hạn mức nghỉ phép của nhân viên
     */
    public function getEmployeeLeaveLimits(Request $request)
    {
        $nhanVienId = $request->nhanVienId;
        $currentYear = date('Y');

        $limits = LoaiNghiPhep::where('TrangThai', '1')
            ->get()
            ->mapWithKeys(function ($type) use ($nhanVienId, $currentYear) {
                if ($type->Ten == 'Nghỉ phép năm') {
                    $phepNam = QuanLyPhepNam::where('NhanVienId', $nhanVienId)->where('Nam', $currentYear)->first();
                    return [$type->id => [
                        'kha_dung' => (float) ($phepNam ? $phepNam->PhepKhaDung : 0),
                        'con_lai' => (float) ($phepNam ? $phepNam->ConLai : 0)
                    ]];
                } else {
                    $used = DangKyNghiPhep::where('NhanVienId', $nhanVienId)
                        ->where('LoaiNghiPhepId', $type->id)
                        ->whereIn('TrangThai', [1, 2])
                        ->where(function ($q) use ($currentYear) {
                            $q->whereYear('TuNgay', $currentYear)
                                ->orWhereYear('DenNgay', $currentYear);
                        })
                        ->sum('SoNgayNghi');

                    $val = $type->CoHanMuc == 1 ? (float) max(0, $type->HanMucToiDa - $used) : 999;
                    return [$type->id => [
                        'kha_dung' => $val,
                        'con_lai' => $val
                    ]];
                }
            });

        return response()->json($limits);
    }

    /**
     * View nghỉ phép cá nhân dành cho nhân viên
     */
    public function CaNhanView()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $nhanVien = auth()->user()->nhanVien;
        if (!$nhanVien) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin nhân viên.');
        }

        // Tự động khởi tạo bảng phép năm nếu chưa có
        $phepNam = QuanLyPhepNam::getCurrentForEmployee($nhanVien->id);
        if (!$phepNam) {
            $phepNam = QuanLyPhepNam::khoiTaoPhepNam($nhanVien->id, now()->year);
        }

        $nghiPheps = DangKyNghiPhep::with(['loaiNghiPhep', 'nguoiDuyet'])
            ->where('NhanVienId', $nhanVien->id)
            ->orderBy('TuNgay', 'desc')
            ->get();

        $loaiNghiPheps = LoaiNghiPhep::where('TrangThai', '1')->get();
        $workingSchedule = CauHinhLichLamViec::all();

        // Thống kê các loại nghỉ phép khác (không phải phép năm) trong năm hiện tại
        $currentYear = now()->year;
        $otherLeaveStats = LoaiNghiPhep::where('Ten', '!=', 'Nghỉ phép năm')
            ->where('TrangThai', '1')
            ->get()
            ->map(function ($type) use ($nhanVien, $currentYear) {
                $daysUsed = DangKyNghiPhep::where('NhanVienId', $nhanVien->id)
                    ->where('LoaiNghiPhepId', $type->id)
                    ->where('TrangThai', 1) // Đã duyệt
                    ->whereYear('TuNgay', $currentYear)
                    ->sum('SoNgayNghi');

                return [
                    'id' => $type->id,
                    'ten' => $type->Ten,
                    'da_dung' => $daysUsed,
                    'co_han_muc' => $type->CoHanMuc == 1,
                    'han_muc' => $type->HanMucToiDa
                ];
            });

        // Thống kê hạn mức còn lại của tất cả các loại nghỉ để truyền sang JS
        $leaveLimitsMap = LoaiNghiPhep::where('TrangThai', '1')
            ->get()
            ->mapWithKeys(function ($type) use ($nhanVien, $currentYear) {
                if ($type->Ten == 'Nghỉ phép năm') {
                    $phepNam = QuanLyPhepNam::where('NhanVienId', $nhanVien->id)->where('Nam', $currentYear)->first();
                    return [$type->id => [
                        'kha_dung' => (float) ($phepNam ? $phepNam->PhepKhaDung : 0),
                        'con_lai' => (float) ($phepNam ? $phepNam->ConLai : 0)
                    ]];
                } else {
                    $used = DangKyNghiPhep::where('NhanVienId', $nhanVien->id)
                        ->where('LoaiNghiPhepId', $type->id)
                        ->whereIn('TrangThai', [1, 2])
                        ->where(function ($q) use ($currentYear) {
                            $q->whereYear('TuNgay', $currentYear)
                                ->orWhereYear('DenNgay', $currentYear);
                        })
                        ->sum('SoNgayNghi');

                    $val = $type->CoHanMuc == 1 ? (float) max(0, $type->HanMucToiDa - $used) : 999;
                    return [$type->id => [
                        'kha_dung' => $val,
                        'con_lai' => $val
                    ]];
                }
            })->toArray();

        return view('leave.self', compact(
            'nghiPheps',
            'phepNam',
            'loaiNghiPheps',
            'workingSchedule',
            'otherLeaveStats',
            'leaveLimitsMap'
        ));
    }

    /**
     * View đăng ký nghỉ phép mới dành cho Admin (đăng ký cho nhân viên khác)
     */
    public function AdminDangKyView()
    {
        $nhanViens = \App\Models\NhanVien::with('ttCongViec.phongBan')->get();
        $loaiNghiPheps = LoaiNghiPhep::where('TrangThai', '1')->get();
        $workingSchedule = CauHinhLichLamViec::all();

        $currentYear = now()->year;
        $annualLeaveLimit = (float) \App\Models\SystemConfig::getValue('annual_leave_limit_per_request', 5);
        $annualLeaveId = $loaiNghiPheps->firstWhere('Ten', 'Nghỉ phép năm')->id ?? null;

        // Initialize with empty map, will be fetched via AJAX in view
        $leaveLimitsMap = [];
        $isAdmin = true;

        return view('leave.register', compact(
            'nhanViens',
            'loaiNghiPheps',
            'workingSchedule',
            'leaveLimitsMap',
            'annualLeaveLimit',
            'annualLeaveId',
            'isAdmin'
        ));
    }

    /**
     * View đăng ký nghỉ phép mới (giao diện chọn buổi chi tiết)
     */
    public function DangKyView()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $nhanVien = auth()->user()->nhanVien;
        if (!$nhanVien) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin nhân viên.');
        }

        $phepNam = QuanLyPhepNam::getCurrentForEmployee($nhanVien->id);
        if (!$phepNam) {
            $phepNam = QuanLyPhepNam::khoiTaoPhepNam($nhanVien->id, now()->year);
        }

        $loaiNghiPheps = LoaiNghiPhep::where('TrangThai', '1')->get();
        $workingSchedule = CauHinhLichLamViec::all();

        $currentYear = now()->year;
        $annualLeaveLimit = (float) \App\Models\SystemConfig::getValue('annual_leave_limit_per_request', 5);
        $annualLeaveId = $loaiNghiPheps->firstWhere('Ten', 'Nghỉ phép năm')->id ?? null;

        $leaveLimitsMap = LoaiNghiPhep::where('TrangThai', '1')
            ->get()
            ->mapWithKeys(function ($type) use ($nhanVien, $currentYear) {
                if ($type->Ten == 'Nghỉ phép năm') {
                    $phepNam = QuanLyPhepNam::where('NhanVienId', $nhanVien->id)->where('Nam', $currentYear)->first();
                    return [$type->id => [
                        'kha_dung' => (float) ($phepNam ? $phepNam->PhepKhaDung : 0),
                        'con_lai' => (float) ($phepNam ? $phepNam->ConLai : 0)
                    ]];
                } else {
                    $used = DangKyNghiPhep::where('NhanVienId', $nhanVien->id)
                        ->where('LoaiNghiPhepId', $type->id)
                        ->whereIn('TrangThai', [1, 2])
                        ->where(function ($q) use ($currentYear) {
                            $q->whereYear('TuNgay', $currentYear)
                                ->orWhereYear('DenNgay', $currentYear);
                        })
                        ->sum('SoNgayNghi');

                    $val = $type->CoHanMuc == 1 ? (float) max(0, $type->HanMucToiDa - $used) : 999;
                    return [$type->id => [
                        'kha_dung' => $val,
                        'con_lai' => $val
                    ]];
                }
            })->toArray();

        return view('leave.register', compact(
            'phepNam',
            'loaiNghiPheps',
            'workingSchedule',
            'leaveLimitsMap',
            'annualLeaveLimit',
            'annualLeaveId'
        ));
    }

    /**
     * Tạo mới đơn nghỉ phép
     */
    public function TaoMoi(Request $request)
    {
        // Chuẩn hóa định dạng ngày từ d/m/Y sang Y-m-d nếu cần
        if ($request->has('TuNgay')) {
            try {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->TuNgay)) {
                    $request->merge([
                        'TuNgay' => Carbon::createFromFormat('d/m/Y', $request->TuNgay)->format('Y-m-d')
                    ]);
                }
            } catch (\Exception $e) {
            }
        }
        if ($request->has('DenNgay')) {
            try {
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->DenNgay)) {
                    $request->merge([
                        'DenNgay' => Carbon::createFromFormat('d/m/Y', $request->DenNgay)->format('Y-m-d')
                    ]);
                }
            } catch (\Exception $e) {
            }
        }

        $request->validate([
            'LoaiNghiPhepId' => 'required|exists:loai_nghi_pheps,id',
            'TuNgay' => 'required|date',
            'DenNgay' => 'required|date|after_or_equal:TuNgay',
            'LyDo' => 'required|string|max:500',
        ], [
            'TuNgay.required' => 'Vui lòng chọn ngày bắt đầu.',
            'TuNgay.date' => 'Ngày bắt đầu không đúng định dạng.',
            'DenNgay.required' => 'Vui lòng chọn ngày kết thúc.',
            'DenNgay.date' => 'Ngày kết thúc không đúng định dạng.',
            'DenNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ], [
            'TuNgay' => 'Ngày bắt đầu',
            'DenNgay' => 'Ngày kết thúc',
            'LoaiNghiPhepId' => 'Loại nghỉ phép',
            'LyDo' => 'Lý do',
        ]);

        try {
            // Xác định NhanVienId: ưu tiên từ request (Admin), nếu không có dùng của User hiện tại
            $nhanVienId = $request->NhanVienId;
            if (!$nhanVienId && auth()->user()->nhanVien) {
                $nhanVienId = auth()->user()->nhanVien->id;
            }

            if (!$nhanVienId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin nhân viên để đăng ký.'
                ]);
            }

            $tuNgay = Carbon::parse($request->TuNgay)->startOfDay();
            $denNgay = Carbon::parse($request->DenNgay)->startOfDay();
            $tuBuoi = $request->TuBuoi ?? 'ca_ngay';
            $denBuoi = $request->DenBuoi ?? 'ca_ngay';

            // Lấy chi tiết buổi nếu có (từ giao diện mới)
            $chiTietBuoi = $request->ChiTietBuoi; // Expected array {date: [sang, chieu]}

            $soNgay = $this->calculateActualLeaveDays($tuNgay, $denNgay, $tuBuoi, $denBuoi, $chiTietBuoi);

            // Kiểm tra quỹ phép nếu là nghỉ phép năm
            $loaiNghiPhep = LoaiNghiPhep::find($request->LoaiNghiPhepId);
            
            // Tìm loại nghỉ phép Năm (mặc định ID 1)
            $annualLeave = LoaiNghiPhep::where('Ten', 'Nghỉ phép năm')->first() ?? LoaiNghiPhep::find(1);
            $annualLeaveId = $annualLeave ? $annualLeave->id : 1;
            
            $phepNam = QuanLyPhepNam::getCurrentForEmployee($nhanVienId);
            
            // Hạn mức cấu hình (số ngày tối đa cho 1 lần nghỉ)
            $limitConfig = (float) \App\Models\SystemConfig::getValue('annual_leave_limit_per_request', 5);
            
            // Hạn mức khả dụng (accrued) và hạn mức còn lại cả năm (totalYearRemaining)
            $khaDung = $phepNam ? (float) $phepNam->KhaDung : 0;
            $conLaiCaNam = $phepNam ? (float) $phepNam->ConLai : 0;

            // QUY TẮC ƯU TIÊN: Nếu còn phép năm (cả năm), bắt buộc dùng phép năm trước
            $effectiveLoaiNghiPhepId = $request->LoaiNghiPhepId;
            if ($conLaiCaNam > 0 && $loaiNghiPhep && $loaiNghiPhep->id != $annualLeaveId) {
                // Nếu loại chọn không phải phép năm nhưng vẫn còn phép năm -> Ép dùng phép năm
                $effectiveLoaiNghiPhepId = $annualLeaveId;
                $loaiNghiPhep = $annualLeave;
            }

            $isLongVacation = false;
            $maxAllowedForPart1 = $limitConfig;

            if ($effectiveLoaiNghiPhepId == $annualLeaveId) {
                // Đối với phép năm, giới hạn phần 1 là số ngày tối đa hệ thống cho phép HOẶC số ngày còn lại của cả năm
                $maxAllowedForPart1 = min($limitConfig, $conLaiCaNam);
            } else if ($loaiNghiPhep && $loaiNghiPhep->CoHanMuc == 1) {
                // Đối với loại nghỉ khác có hạn mức
                $currentYear = date('Y');
                $used = DangKyNghiPhep::where('NhanVienId', $nhanVienId)
                    ->where('LoaiNghiPhepId', $loaiNghiPhep->id)
                    ->whereIn('TrangThai', [1, 2])
                    ->where(function ($q) use ($currentYear) {
                        $q->whereYear('TuNgay', $currentYear)
                            ->orWhereYear('DenNgay', $currentYear);
                    })
                    ->sum('SoNgayNghi');
                $otherConLai = (float) max(0, $loaiNghiPhep->HanMucToiDa - $used);
                $maxAllowedForPart1 = $otherConLai;
            }

            if ($soNgay > $maxAllowedForPart1) {
                if (!$request->SplitLoaiNghiPhepId) {
                    // Admin/Manager can override limits without splitting
                    if (auth()->user()->can('Xem Danh Sách Nghỉ Phép')) {
                        $isLongVacation = false;
                    } else {
                        $reason = $soNgay > $maxAllowedForPart1 ? "vượt quá hạn mức có thể sử dụng ($maxAllowedForPart1 ngày)" : "";
                        return response()->json([
                            'success' => false,
                            'message' => "Số ngày nghỉ $reason. Vui lòng chọn loại nghỉ thay thế cho phần dư."
                        ]);
                    }
                } else {
                    $isLongVacation = true;
                }
            }

            // Kiểm tra tồn tại đơn trong khoảng thời gian này
            $exists = DangKyNghiPhep::where('NhanVienId', $nhanVienId)
                ->where(function ($q) use ($tuNgay, $denNgay) {
                    $q->whereBetween('TuNgay', [$tuNgay, $denNgay])
                        ->orWhereBetween('DenNgay', [$tuNgay, $denNgay]);
                })
                ->whereIn('TrangThai', [1, 2])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã có đơn nghỉ phép trong khoảng thời gian này.'
                ]);
            }

            return \DB::transaction(function () use ($isLongVacation, $nhanVienId, $request, $tuNgay, $denNgay, $tuBuoi, $denBuoi, $soNgay, $loaiNghiPhep, $khaDung, $conLaiCaNam, $maxAllowedForPart1, $annualLeaveId) {
                if ($isLongVacation) {
                    $schedule = CauHinhLichLamViec::all()->keyBy('Thu');

                    // Tìm ngày chia tách đơn
                    $splitDate = $tuNgay->copy();
                    $actualDaysCount = 0;

                    while ($actualDaysCount < $maxAllowedForPart1 && $splitDate->lte($denNgay)) {
                        $dayOfWeek = $splitDate->dayOfWeek;
                        $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);
                        if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                            $actualDaysCount += (float) $schedule[$dbDayOfWeek]->CoLamViec;
                        }
                        if ($actualDaysCount < $maxAllowedForPart1) {
                            $splitDate->addDay();
                        }
                    }

                    // Tính toán accrued/advance cho Đơn 1
                    $note1 = "";
                    if ($loaiNghiPhep->id == $annualLeaveId) {
                        $part1Days = $actualDaysCount;
                        $fromAccrued = min($part1Days, $khaDung);
                        $fromAdvance = max(0, $part1Days - $fromAccrued);
                        if ($fromAdvance > 0) {
                            $note1 = " ($fromAccrued ngày phép năm + $fromAdvance ngày ứng phép)";
                        }
                    }

                    // Đơn 1: Loại nghỉ chính (đến splitDate)
                    DangKyNghiPhep::create([
                        'NhanVienId' => $nhanVienId,
                        'LoaiNghiPhepId' => $loaiNghiPhep->id,
                        'TuNgay' => $tuNgay->format('Y-m-d'),
                        'DenNgay' => $splitDate->format('Y-m-d'),
                        'SoNgayNghi' => $actualDaysCount,
                        'LyDo' => $request->LyDo . " (Phần 1: " . $loaiNghiPhep->Ten . ")" . $note1,
                        'TrangThai' => 2,
                        'Dem' => 1
                    ]);

                    // Đơn 2: Loại nghỉ thay thế
                    $nextDay = $splitDate->copy()->addDay();
                    $soNgayConLai = $soNgay - $actualDaysCount;

                    if ($soNgayConLai > 0 && $nextDay->lte($denNgay)) {
                        $splitType = LoaiNghiPhep::find($request->SplitLoaiNghiPhepId);
                        $note2 = "";
                        
                        // Nếu đơn 2 vẫn là phép năm, tính phần ứng
                        if ($request->SplitLoaiNghiPhepId == $annualLeaveId) {
                            $newKhaDung = max(0, $khaDung - $actualDaysCount);
                            $fromAccrued2 = min($soNgayConLai, $newKhaDung);
                            $fromAdvance2 = max(0, $soNgayConLai - $fromAccrued2);
                            if ($fromAdvance2 > 0) {
                                $note2 = " ($fromAccrued2 ngày phép năm + $fromAdvance2 ngày ứng phép)";
                            }
                        }

                        DangKyNghiPhep::create([
                            'NhanVienId' => $nhanVienId,
                            'LoaiNghiPhepId' => $request->SplitLoaiNghiPhepId,
                            'TuNgay' => $nextDay->format('Y-m-d'),
                            'DenNgay' => $denNgay->format('Y-m-d'),
                            'SoNgayNghi' => $soNgayConLai,
                            'LyDo' => $request->LyDo . " (Phần 2: " . ($splitType ? $splitType->Ten : 'Nghỉ khác') . ")" . $note2,
                            'TrangThai' => 2,
                            'Dem' => 1
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Đã tách thành " . ($soNgayConLai > 0 ? "2" : "1") . " đơn nghỉ phép để phù hợp quy định hạn mức.\n (Đơn 1: $actualDaysCount ngày $loaiNghiPhep->Ten)"
                    ]);
                } else {
                    // LOGIC BÌNH THƯỜNG
                    $note = "";
                    if ($loaiNghiPhep->id == $annualLeaveId) {
                        $fromAccrued = min($soNgay, $khaDung);
                        $fromAdvance = max(0, $soNgay - $fromAccrued);
                        if ($fromAdvance > 0) {
                            $note = " ($fromAccrued ngày phép năm + $fromAdvance ngày ứng phép)";
                        }
                    }

                    DangKyNghiPhep::create([
                        'NhanVienId' => $nhanVienId,
                        'LoaiNghiPhepId' => $loaiNghiPhep->id,
                        'TuNgay' => $tuNgay->format('Y-m-d'),
                        'TuBuoi' => $tuBuoi,
                        'DenNgay' => $denNgay->format('Y-m-d'),
                        'DenBuoi' => $denBuoi,
                        'ChiTietBuoi' => $request->ChiTietBuoi,
                        'SoNgayNghi' => $soNgay,
                        'LyDo' => $request->LyDo . $note,
                        'TrangThai' => 2,
                        'Dem' => 1
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => "Đơn đăng ký nghỉ phép đã được gửi thành công!" . ($note ? "\nGhi chú: $note" : "")
                    ]);
                }
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Phê duyệt đơn nghỉ phép
     */
    public function Duyet($id)
    {
        $user = auth()->user();
        $nhanVien = $user->nhanVien;

        if (!$nhanVien && $user->id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên để thực hiện duyệt.'
            ]);
        }

        $leave = DangKyNghiPhep::with('loaiNghiPhep')->findOrFail($id);

        $leave->update([
            'TrangThai' => 1,
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null
        ]);

        // Nếu là nghỉ phép năm, khấu trừ vào quỹ phép
        if ($leave->loaiNghiPhep && $leave->loaiNghiPhep->Ten == 'Nghỉ phép năm') {
            $phepNam = QuanLyPhepNam::getCurrentForEmployee($leave->NhanVienId);
            if ($phepNam) {
                $phepNam->deductLeave($leave->SoNgayNghi);
            }
        }

        // Tự động đồng bộ sang bảng chấm công
        // $this->syncLeaveToAttendance($leave);

        // Gửi thông báo email cho nhân viên
        if ($leave->nhanVien && !empty($leave->nhanVien->Email)) {
            try {
                Mail::to($leave->nhanVien->Email)->queue(new LeaveApprovalMail($leave));
                \Log::info("Sent leave approval email to {$leave->nhanVien->Email} for Request #{$leave->id}");
            } catch (\Exception $e) {
                \Log::error("Failed to send leave approval email for Request #{$leave->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã phê duyệt đơn nghỉ phép.'
        ]);
    }

    /**
     * Từ chối đơn nghỉ phép
     */
    public function TuChoi(Request $request, $id)
    {
        $user = auth()->user();
        $nhanVien = $user->nhanVien;

        if (!$nhanVien && $user->id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên để thực hiện từ chối.'
            ]);
        }

        $leave = DangKyNghiPhep::findOrFail($id);

        $leave->update([
            'TrangThai' => 0,
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null,
            'LyDo' => $leave->LyDo . "\n[Lý do từ chối: " . ($request->LyDo ?? 'Không có lý do') . "]"
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối đơn nghỉ phép.'
        ]);
    }

    /**
     * Duyệt nhiều đơn nghỉ phép
     */
    public function DuyetNhieu(Request $request)
    {
        $user = auth()->user();
        $nhanVien = $user->nhanVien;

        if (!$nhanVien && $user->id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên.'
            ]);
        }

        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một đơn.']);
        }

        $leaves = DangKyNghiPhep::with('loaiNghiPhep')->whereIn('id', $ids)->where('TrangThai', 2)->get();
        foreach ($leaves as $leave) {
            $leave->update([
                'TrangThai' => 1,
                'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null
            ]);

            // Nếu là nghỉ phép năm, khấu trừ vào quỹ phép
            if ($leave->loaiNghiPhep && $leave->loaiNghiPhep->Ten == 'Nghỉ phép năm') {
                $phepNam = QuanLyPhepNam::getCurrentForEmployee($leave->NhanVienId);
                if ($phepNam) {
                    $phepNam->deductLeave($leave->SoNgayNghi);
                }
            }

            // $this->syncLeaveToAttendance($leave);

            // Gửi thông báo email cho nhân viên
            if ($leave->nhanVien && !empty($leave->nhanVien->Email)) {
                try {
                    Mail::to($leave->nhanVien->Email)->queue(new LeaveApprovalMail($leave));
                } catch (\Exception $e) {
                    \Log::error("Failed to send bulk leave approval email for Request #{$leave->id}: " . $e->getMessage());
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã phê duyệt các đơn đã chọn.'
        ]);
    }

    /**
     * Từ chối nhiều đơn nghỉ phép
     */
    public function TuChoiNhieu(Request $request)
    {
        $user = auth()->user();
        $nhanVien = $user->nhanVien;

        if (!$nhanVien && $user->id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn chưa được liên kết với hồ sơ nhân viên.'
            ]);
        }

        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một đơn.']);
        }

        DangKyNghiPhep::whereIn('id', $ids)->where('TrangThai', 2)->update([
            'TrangThai' => 0,
            'NguoiDuyetId' => $nhanVien ? $nhanVien->id : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối các đơn đã chọn.'
        ]);
    }

    /**
     * Khởi tạo phép năm hàng loạt cho toàn bộ nhân viên chưa có phép năm trong năm hiện tại
     */
    public function KhoiTaoPhepNamHangLoat(Request $request)
    {
        try {
            $currentYear = date('Y');
            $nhanViens = \App\Models\NhanVien::all();
            $count = 0;

            foreach ($nhanViens as $nv) {
                // Kiểm tra xem nhân viên đã có phép năm cho năm hiện tại chưa
                $exists = \App\Models\QuanLyPhepNam::where('NhanVienId', $nv->id)
                    ->where('Nam', $currentYear)
                    ->exists();

                if (!$exists) {
                    $result = \App\Models\QuanLyPhepNam::khoiTaoPhepNam($nv->id, $currentYear);
                    if ($result) {
                        $count++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Đã khởi tạo phép năm cho {$count} nhân viên mới.",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi khởi tạo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View cấu hình các loại nghỉ phép
     */
    public function ConfigView()
    {
        $loaiNghiPheps = LoaiNghiPhep::all();
        return view('leave.config', compact('loaiNghiPheps'));
    }

    /**
     * Lưu thông tin loại nghỉ phép (Thêm mới hoặc Cập nhật)
     */
    public function SaveLoaiPhep(Request $request)
    {
        $request->validate([
            'Ten' => 'required|string|max:255',
            'HuongLuong' => 'required|numeric|min:0|max:100',
            'CoHanMuc' => 'required|in:0,1',
            'HanMucToiDa' => 'nullable|integer|min:0',
        ]);

        try {
            if ($request->id) {
                $loai = LoaiNghiPhep::findOrFail($request->id);
                $oldData = $loai->toArray();
                $loai->update($request->all());
                $newData = $loai->fresh()->toArray();
                \App\Services\SystemLogService::log('Cập nhật', 'LoaiNghiPhep', $loai->id, "Cập nhật loại nghỉ phép: {$loai->Ten}", $oldData, $newData);
                $message = 'Đã cập nhật loại nghỉ phép thành công.';
            } else {
                $loai = LoaiNghiPhep::create($request->all());
                \App\Services\SystemLogService::log('Tạo mới', 'LoaiNghiPhep', $loai->id, "Thêm loại nghỉ phép mới: {$loai->Ten}");
                $message = 'Đã thêm loại nghỉ phép mới thành công.';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Bật/Tắt trạng thái loại nghỉ phép (Khóa/Mở khóa)
     */
    public function ToggleLoaiPhepStatus($id)
    {
        try {
            $loai = LoaiNghiPhep::findOrFail($id);
            $newStatus = ($loai->TrangThai == 1) ? 0 : 1;
            $actionText = ($newStatus == 1) ? 'Mở khóa' : 'Khóa';

            $loai->update(['TrangThai' => $newStatus]);
            
            \App\Services\SystemLogService::log($actionText, 'LoaiNghiPhep', $loai->id, "{$actionText} loại nghỉ phép: {$loai->Ten}");
            
            return response()->json([
                'success' => true,
                'message' => "Đã {$actionText} loại nghỉ phép thành công."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Tính số ngày nghỉ thực tế dựa trên cấu hình lịch làm việc
     */
    private function calculateActualLeaveDays($tuNgay, $denNgay, $tuBuoi = 'ca_ngay', $denBuoi = 'ca_ngay', $chiTietBuoi = null)
    {
        // Nếu có chi tiết buổi (đăng ký từ giao diện mới)
        if ($chiTietBuoi && is_array($chiTietBuoi)) {
            $count = 0;
            foreach ($chiTietBuoi as $date => $sessions) {
                if (empty($sessions))
                    continue;

                $cur = Carbon::parse($date);
                $dayOfWeek = $cur->dayOfWeek;
                $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);

                $schedule = CauHinhLichLamViec::all()->keyBy('Thu');
                if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                    $dayWorkValue = (float) $schedule[$dbDayOfWeek]->CoLamViec;

                    if (count($sessions) >= 2) {
                        $count += $dayWorkValue;
                    } else {
                        $count += min($dayWorkValue, 0.5);
                    }
                }
            }
            return $count;
        }

        $count = 0;
        $currentDate = $tuNgay->copy();
        $schedule = CauHinhLichLamViec::all()->keyBy('Thu');

        while ($currentDate->lte($denNgay)) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);

            if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                $dayWorkValue = (float) $schedule[$dbDayOfWeek]->CoLamViec;

                // Ngày hiện tại là ngày bắt đầu
                if ($currentDate->isSameDay($tuNgay)) {
                    if ($tuBuoi === 'sang' || $tuBuoi === 'chieu') {
                        $count += min($dayWorkValue, 0.5);
                    } else {
                        $count += $dayWorkValue;
                    }
                }
                // Ngày hiện tại là ngày kết thúc (và KHÔNG phải là ngày bắt đầu - tránh tính 2 lần)
                elseif ($currentDate->isSameDay($denNgay)) {
                    if ($denBuoi === 'sang' || $denBuoi === 'chieu') {
                        $count += min($dayWorkValue, 0.5);
                    } else {
                        $count += $dayWorkValue;
                    }
                }
                // Các ngày giữa
                else {
                    $count += $dayWorkValue;
                }
            }
            $currentDate->addDay();
        }

        // Trường hợp đặc biệt: Nghỉ 1 buổi của 1 ngày duy nhất
        if ($tuNgay->isSameDay($denNgay)) {
            // Nếu cùng 1 ngày, cần ghi đè logic loop trên để không bị sai
            $count = 0;
            $dayOfWeek = $tuNgay->dayOfWeek;
            $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);
            if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                $dayWorkValue = (float) $schedule[$dbDayOfWeek]->CoLamViec;
                if ($tuBuoi === 'sang' && $denBuoi === 'sang')
                    $count = 0.5;
                elseif ($tuBuoi === 'chieu' && $denBuoi === 'chieu')
                    $count = 0.5;
                elseif ($tuBuoi === 'sang' && $denBuoi === 'chieu')
                    $count = 1.0;
                else
                    $count = $dayWorkValue;
            }
        }

        return $count;
    }
}
