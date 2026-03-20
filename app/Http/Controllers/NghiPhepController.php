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
            ->where(function($q) {
                $q->where('CoHanMuc', 1)
                  ->orWhere('Ten', 'Nghỉ phép năm');
            })->get();

        $query = \App\Models\NhanVien::with(['ttCongViec.phongBan', 'dangKyNghiPheps' => function($q) use ($nam) {
                $q->whereIn('TrangThai', [1, 2])
                  ->where(function($qq) use ($nam) {
                      $qq->whereYear('TuNgay', $nam)->orWhereYear('DenNgay', $nam);
                  });
            }, 'quanLyPhepNams' => function($q) use ($nam) {
                $q->where('Nam', $nam);
            }])->has('ttCongViec');

        if ($phongBanId) {
            $query->whereHas('ttCongViec', function($q) use ($phongBanId) {
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
            ->mapWithKeys(function($type) use ($nhanVienId, $currentYear) {
                if ($type->Ten == 'Nghỉ phép năm') {
                    $phepNam = QuanLyPhepNam::where('NhanVienId', $nhanVienId)->where('Nam', $currentYear)->first();
                    return [$type->id => (float)($phepNam ? $phepNam->ConLai : 0)];
                } else {
                    $used = DangKyNghiPhep::where('NhanVienId', $nhanVienId)
                        ->where('LoaiNghiPhepId', $type->id)
                        ->whereIn('TrangThai', [1, 2])
                        ->where(function($q) use ($currentYear) {
                            $q->whereYear('TuNgay', $currentYear)
                              ->orWhereYear('DenNgay', $currentYear);
                        })
                        ->sum('SoNgayNghi');
                    
                    return [$type->id => $type->CoHanMuc == 1 ? (float)max(0, $type->HanMucToiDa - $used) : 999];
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
                    return [$type->id => (float) ($phepNam ? $phepNam->ConLai : 0)];
                } else {
                    $used = DangKyNghiPhep::where('NhanVienId', $nhanVien->id)
                        ->where('LoaiNghiPhepId', $type->id)
                        ->whereIn('TrangThai', [1, 2])
                        ->where(function ($q) use ($currentYear) {
                            $q->whereYear('TuNgay', $currentYear)
                                ->orWhereYear('DenNgay', $currentYear);
                        })
                        ->sum('SoNgayNghi');

                    return [$type->id => $type->CoHanMuc == 1 ? (float) max(0, $type->HanMucToiDa - $used) : 999];
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
            $soNgay = $this->calculateActualLeaveDays($tuNgay, $denNgay);

            // Kiểm tra quỹ phép nếu là nghỉ phép năm
            $loaiNghiPhep = LoaiNghiPhep::find($request->LoaiNghiPhepId);
            $phepNam = null;
            $isLongVacation = false;
            
            $isLongVacation = false;
            $conLai = 999;
            $maxAllowed = 999;
            $limitConfig = 999;

            if ($loaiNghiPhep) {
                if ($loaiNghiPhep->Ten == 'Nghỉ phép năm') {
                    $phepNam = QuanLyPhepNam::getCurrentForEmployee($nhanVienId);
                    $conLai = $phepNam ? (float)$phepNam->ConLai : 0;
                    $limitConfig = (float) \App\Models\SystemConfig::getValue('annual_leave_limit_per_request', 5);
                    $maxAllowed = min($conLai, $limitConfig);
                } else if ($loaiNghiPhep->CoHanMuc == 1) {
                    $currentYear = date('Y');
                    $used = DangKyNghiPhep::where('NhanVienId', $nhanVienId)
                        ->where('LoaiNghiPhepId', $loaiNghiPhep->id)
                        ->whereIn('TrangThai', [1, 2])
                        ->where(function($q) use ($currentYear) {
                            $q->whereYear('TuNgay', $currentYear)
                              ->orWhereYear('DenNgay', $currentYear);
                        })
                        ->sum('SoNgayNghi');
                    $conLai = (float)max(0, $loaiNghiPhep->HanMucToiDa - $used);
                    $maxAllowed = $conLai;
                }

                if ($soNgay > $maxAllowed) {
                    if (!$request->SplitLoaiNghiPhepId) {
                        // Admin/Manager can override limits without splitting
                        if (auth()->user()->can('Quản lý tăng ca nghỉ phép')) {
                            $isLongVacation = false;
                        } else {
                            $reason = $soNgay > $conLai ? "vượt quá hạn mức còn lại ($conLai ngày)" : "vượt quá giới hạn mỗi lần dùng ($limitConfig ngày)";
                            return response()->json([
                                'success' => false,
                                'message' => "Số ngày nghỉ $reason. Vui lòng chọn loại nghỉ thay thế cho phần dư."
                            ]);
                        }
                    } else {
                        $isLongVacation = true;
                    }
                }
            }

            // Kiểm tra tồn tại đơn trong khoảng thời gian này
            $exists = DangKyNghiPhep::where('NhanVienId', $nhanVienId)
                ->where(function ($q) use ($tuNgay, $denNgay) {
                    $q->whereBetween('TuNgay', [$tuNgay, $denNgay])
                        ->orWhereBetween('DenNgay', [$tuNgay, $denNgay]);
                })
                ->whereIn('TrangThai', [1, 2]) // Đã duyệt hoặc đang chờ
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã có đơn nghỉ phép trong khoảng thời gian này.'
                ]);
            }

            return \DB::transaction(function () use ($isLongVacation, $nhanVienId, $request, $tuNgay, $denNgay, $soNgay, $loaiNghiPhep, $conLai, $maxAllowed) {
                if ($isLongVacation) {
                    // LOGIC TÁCH ĐƠN (Đã tính conLai và maxAllowed ở trên)
                    if ($maxAllowed <= 0) {
                        // Nếu hết sạch hạn mức, chỉ tạo 1 đơn loại nghỉ khác cho toàn bộ khoảng thời gian
                        DangKyNghiPhep::create([
                            'NhanVienId' => $nhanVienId,
                            'LoaiNghiPhepId' => $request->SplitLoaiNghiPhepId,
                            'TuNgay' => $tuNgay->format('Y-m-d'),
                            'DenNgay' => $denNgay->format('Y-m-d'),
                            'SoNgayNghi' => $soNgay,
                            'LyDo' => $request->LyDo . " (Cắt từ loại: " . $loaiNghiPhep->Ten . ")",
                            'TrangThai' => 2,
                            'Dem' => 1
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => "Bạn đã hết hạn mức loại nghỉ này. Hệ thống ghi nhận đơn nghỉ loại khác ($soNgay ngày)."
                        ]);
                    }

                    $schedule = CauHinhLichLamViec::all()->keyBy('Thu');
                    
                    // Tìm ngày chia tách (ngày mà phép năm / giới hạn vừa hết)
                    $splitDate = $tuNgay->copy();
                    $actualDaysCount = 0;
                    
                    while ($actualDaysCount < $maxAllowed && $splitDate->lte($denNgay)) {
                        $dayOfWeek = $splitDate->dayOfWeek;
                        $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);
                        if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                            $actualDaysCount += (float) $schedule[$dbDayOfWeek]->CoLamViec;
                        }
                        // Nếu sau khi cộng mà vẫn chưa đủ số ngày phép được phép (maxAllowed), mới tiến tiếp ngày tiếp theo
                        if ($actualDaysCount < $maxAllowed) {
                            $splitDate->addDay();
                        }
                    }

                    // Đơn 1: Loại nghỉ chính (đến splitDate)
                    DangKyNghiPhep::create([
                        'NhanVienId' => $nhanVienId,
                        'LoaiNghiPhepId' => $request->LoaiNghiPhepId,
                        'TuNgay' => $tuNgay->format('Y-m-d'),
                        'DenNgay' => $splitDate->format('Y-m-d'),
                        'SoNgayNghi' => min($conLai, $actualDaysCount),
                        'LyDo' => $request->LyDo . " (Phần 1: " . $loaiNghiPhep->Ten . ")",
                        'TrangThai' => 2,
                        'Dem' => 1
                    ]);

                    // Đơn 2: Loại nghỉ thay thế (từ splitDate + 1 đến DenNgay)
                    $nextDay = $splitDate->copy()->addDay();
                    $soNgayConLai = $soNgay - min($conLai, $actualDaysCount);
                    
                    if ($soNgayConLai > 0 && $nextDay->lte($denNgay)) {
                        DangKyNghiPhep::create([
                            'NhanVienId' => $nhanVienId,
                            'LoaiNghiPhepId' => $request->SplitLoaiNghiPhepId,
                            'TuNgay' => $nextDay->format('Y-m-d'),
                            'DenNgay' => $denNgay->format('Y-m-d'),
                            'SoNgayNghi' => $soNgayConLai,
                            'LyDo' => $request->LyDo . " (Phần 2: Loại nghỉ khác)",
                            'TrangThai' => 2,
                            'Dem' => 1
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => "Đã tạo " . ($soNgayConLai > 0 ? "2" : "1") . " đơn nghỉ phép: " . min($conLai, $actualDaysCount) . " ngày " . $loaiNghiPhep->Ten . ($soNgayConLai > 0 ? " và $soNgayConLai ngày loại khác." : ".")
                    ]);
                } else {
                    // LOGIC BÌNH THƯỜNG
                    DangKyNghiPhep::create([
                        'NhanVienId' => $nhanVienId,
                        'LoaiNghiPhepId' => $request->LoaiNghiPhepId,
                        'TuNgay' => $tuNgay->format('Y-m-d'),
                        'DenNgay' => $denNgay->format('Y-m-d'),
                        'SoNgayNghi' => $soNgay,
                        'LyDo' => $request->LyDo,
                        'TrangThai' => 2,
                        'Dem' => 1
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Đơn đăng ký nghỉ phép đã được gửi thành công! (Số ngày nghỉ thực tế: ' . $soNgay . ' ngày)'
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
        $this->syncLeaveToAttendance($leave);

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

            $this->syncLeaveToAttendance($leave);

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
     * Xóa loại nghỉ phép
     */
    public function DeleteLoaiPhep($id)
    {
        try {
            $loai = LoaiNghiPhep::findOrFail($id);

            $loai->update(['TrangThai' => '0']);
            \App\Services\SystemLogService::log('Xóa', 'LoaiNghiPhep', $loai->id, "Khóa loại nghỉ phép: {$loai->Ten}");
            return response()->json([
                'success' => true,
                'message' => 'Đã khóa loại nghỉ phép thành công.'
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
    private function calculateActualLeaveDays($tuNgay, $denNgay)
    {
        $count = 0;
        $currentDate = $tuNgay->copy();

        // Lấy cấu hình lịch làm việc (cache để tránh query nhiều lần)
        $schedule = CauHinhLichLamViec::all()->keyBy('Thu');

        while ($currentDate->lte($denNgay)) {
            // weekDay() của Carbon trả về 0 (CN) -> 6 (T7)
            // Trong DB Thu là 2 (T2) -> 8 (CN)
            $dayOfWeek = $currentDate->dayOfWeek;
            $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);

            if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                $count += (float) $schedule[$dbDayOfWeek]->CoLamViec;
            }
            $currentDate->addDay();
        }

        return $count;
    }

    /**
     * Đồng bộ đơn nghỉ phép sang bảng chấm công
     */
    private function syncLeaveToAttendance($leave)
    {
        $tuNgay = Carbon::parse($leave->TuNgay)->startOfDay();
        $denNgay = Carbon::parse($leave->DenNgay)->startOfDay();

        // Lấy cấu hình lịch làm việc
        $schedule = CauHinhLichLamViec::all()->keyBy('Thu');

        $cur = $tuNgay->copy();
        $huongLuong = $leave->loaiNghiPhep ? (float) $leave->loaiNghiPhep->HuongLuong : 0;
        $cong = $huongLuong / 100;

        while ($cur->lte($denNgay)) {
            $dayOfWeek = $cur->dayOfWeek;
            $dbDayOfWeek = ($dayOfWeek === 0) ? 8 : ($dayOfWeek + 1);

            // Chỉ đồng bộ nếu là ngày làm việc
            if (isset($schedule[$dbDayOfWeek]) && $schedule[$dbDayOfWeek]->CoLamViec) {
                $dateStr = $cur->toDateString();

                ChamCong::updateOrCreate(
                    [
                        'NhanVienId' => $leave->NhanVienId,
                        'Vao' => Carbon::parse($dateStr . ' 08:00:00'),
                    ],
                    [
                        'Ra' => Carbon::parse($dateStr . ' 17:00:00'),
                        'TrangThai' => 'dung_gio',
                        'Cong' => $cong
                    ]
                );
            }
            $cur->addDay();
        }
    }
}
