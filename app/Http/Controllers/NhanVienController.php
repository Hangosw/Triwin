<?php

namespace App\Http\Controllers;


use App\Models\NhanVien;
use App\Models\DmChucVu;
use App\Models\DmPhongBan;

use App\Models\TtNhanVienCongViec;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NhanVienImport;

class NhanVienController extends Controller
{
    public function DanhSachView()
    {
        return view('employees.index');
    }

    public function DataNhanVien(Request $request)
    {
        $query = NhanVien::with(['ttCongViec.phongBan', 'ttCongViec.chucVu'])
        ;

        // Server-side processing
        $totalRecords = $query->count();

        // Search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('Ten', 'like', "%{$searchValue}%")
                    ->orWhere('SoCCCD', 'like', "%{$searchValue}%")
                    ->orWhere('Email', 'like', "%{$searchValue}%")
                    ->orWhere('SoDienThoai', 'like', "%{$searchValue}%");
            });
        }

        // Filter by Gender
        if ($request->has('gioi_tinh') && $request->gioi_tinh !== null && $request->gioi_tinh !== '') {
            $query->where('GioiTinh', $request->gioi_tinh);
        }

        // Filter by Status
        if ($request->has('trang_thai') && $request->trang_thai !== null && $request->trang_thai !== '') {
            $query->where('TrangThai', $request->trang_thai);
        }

        $filteredRecords = $query->count();

        // Sorting
        if ($request->has('order')) {
            $columnIndex = $request->order[0]['column'];
            $columnDir = $request->order[0]['dir'];
            $columns = ['id', 'Ten', 'SoDienThoai', 'phong_ban_id', 'Nhom', 'id'];
            if (isset($columns[$columnIndex])) {
                $query->orderBy($columns[$columnIndex], $columnDir);
            }
        }

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        if ($length != -1) {
            $data = $query->skip($start)->take($length)->get();
        } else {
            $data = $query->get();
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function importView()
    {
        return view('employees.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Vui lòng chọn file Excel.',
            'file.mimes' => 'File phải có định dạng xlsx, xls hoặc csv.',
            'file.max' => 'Dung lượng file không được vượt quá 2MB.',
        ]);

        try {
            $import = new NhanVienImport();
            Excel::import($import, $request->file('file'));

            if (count($import->errors) > 0) {
                // If there are errors, return back with the array of errors and a success count
                return back()->with('import_errors', $import->errors)
                    ->with('import_success_count', $import->successCount);
            }

            return redirect()->route('nhan-vien.danh-sach')
                ->with('success', "Đã import thành công {$import->successCount} nhân viên!");
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi import file: ' . $e->getMessage());
        }
    }

    public function Info($id)
    {
        $employee = NhanVien::with([
            'ttCongViec.chucVu',
            'ttCongViec.phongBan',
            'thanNhans',
            'hopDongs' => function ($q) {
                $q->with(['loaiHopDong', 'chucVu'])
                    ->orderByRaw('CASE WHEN TrangThai = 1 THEN 0 ELSE 1 END')
                    ->orderBy('NgayBatDau', 'desc');
            },
            'luongs' => function ($q) {
                $q->orderBy('ThoiGian', 'desc');
            },
        ])->findOrFail($id);

        // Ownership / Permission check
        if (!auth()->user()->can('Xem Nhân Viên') && $employee->NguoiDungId != auth()->id()) {
            abort(403, 'Bạn không có quyền xem thông tin của nhân viên này.');
        }

        return view('employees.show', compact('employee'));
    }

    /**
     * Helper: Convert date format from dd-mm-yyyy or dd/mm/yyyy to yyyy-mm-dd
     */
    private function convertDateFormat($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Check if format is dd-mm-yyyy or dd/mm/yyyy
        if (preg_match('/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/', $dateString, $matches)) {
            // Convert to yyyy-mm-dd
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        // Already in correct format or other format, return as is
        return $dateString;
    }

    public function TaoView()
    {
        $phongBans = DmPhongBan::all();
        $chucVus = DmChucVu::all();
        return view('employees.create', compact('phongBans', 'chucVus'));
    }

    public function Tao(Request $request)
    {
        // Validate dữ liệu - sử dụng tên columns trong database
        $request->validate([
            'Ten' => 'required|string|max:255',
            'NgaySinh' => 'required|date',
            'GioiTinh' => 'required|integer|in:0,1',
            'SoCCCD' => 'required|string|unique:nhan_viens,SoCCCD',
            'Email' => 'required|email|unique:nhan_viens,Email|unique:nguoi_dungs,Email',
            'SoDienThoai' => 'required|string',
            'DiaChi' => 'required|string',

            'PhongBanId' => 'required|integer',
            'ChucVuId' => 'required|integer',
            'Nhom' => 'required|string',
            'NgayTuyenDung' => 'required|date',
            'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cropped_avatar' => 'nullable|string',
            'anh_cccd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'anh_cccd_sau' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'anh_bhxh.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'TrangThai' => 'nullable|string|in:dang_lam,nghi_viec,nghi_thai_san',
        ], [
            // Thông tin cá nhân
            'Ten.required' => 'Vui lòng nhập họ và tên nhân viên.',
            'Ten.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'NgaySinh.required' => 'Vui lòng chọn ngày sinh.',
            'NgaySinh.date' => 'Ngày sinh không hợp lệ.',
            'GioiTinh.required' => 'Vui lòng chọn giới tính.',
            'GioiTinh.in' => 'Giới tính không hợp lệ.',

            // CCCD
            'SoCCCD.required' => 'Vui lòng nhập số CCCD/CMND.',
            'SoCCCD.unique' => 'Số CCCD này đã tồn tại trong hệ thống.',

            // Liên hệ
            'Email.required' => 'Vui lòng nhập địa chỉ email.',
            'Email.email' => 'Địa chỉ email không hợp lệ.',
            'Email.unique' => 'Email này đã được sử dụng (trong hồ sơ nhân viên hoặc tài khoản người dùng).',
            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'DiaChi.required' => 'Vui lòng nhập địa chỉ thường trú.',

            // Công việc

            'PhongBanId.required' => 'Vui lòng chọn phòng ban.',
            'ChucVuId.required' => 'Vui lòng chọn chức vụ.',
            'Nhom.required' => 'Vui lòng chọn loại nhân viên.',
            'NgayTuyenDung.required' => 'Vui lòng chọn ngày vào làm.',
            'NgayTuyenDung.date' => 'Ngày vào làm không hợp lệ.',

            // Avatar
            'AnhDaiDien.image' => 'File tải lên phải là hình ảnh.',
            'AnhDaiDien.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, hoặc gif.',
            'AnhDaiDien.max' => 'Kích thước ảnh không được vượt quá 2MB.',

            'anh_cccd.image' => 'File tải lên CCCD mặt trước phải là hình ảnh.',
            'anh_cccd.max' => 'Kích thước ảnh CCCD mặt trước không được vượt quá 5MB.',
            'anh_cccd_sau.image' => 'File tải lên CCCD mặt sau phải là hình ảnh.',
            'anh_cccd_sau.max' => 'Kích thước ảnh CCCD mặt sau không được vượt quá 5MB.',
            'anh_bhxh.*.image' => 'File tải lên BHXH phải là hình ảnh.',
            'anh_bhxh.*.max' => 'Kích thước mỗi ảnh BHXH không được vượt quá 5MB.',
        ]);

        try {
            // Sử dụng DB Transaction để tránh race condition khi tạo mã nhân viên
            $Ma = \DB::transaction(function () use ($request) {
                // 1. Tự động tạo Mã nhân viên: NV_YY_XXXXX với row-level locking
                $year = date('y'); // 2 số cuối của năm hiện tại, ví dụ 26

                // Sử dụng lockForUpdate() để khóa row khi query, tránh 2 user cùng lấy mã cuối
                $latestEmployee = NhanVien::where('Ma', 'like', "NV_{$year}_%")
                    ->orderBy('Ma', 'desc')
                    ->lockForUpdate()
                    ->first();

                if ($latestEmployee) {
                    // Lấy số thứ tự từ mã cuối cùng
                    $lastNumber = intval(substr($latestEmployee->Ma, -5));
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                // Format số thứ tự thành chuỗi 5 chữ số, ví dụ 00001
                $sequence = str_pad($newNumber, 5, '0', STR_PAD_LEFT);
                $Ma = "NV_{$year}_{$sequence}";

                // 2. Xử lý upload ảnh đại diện
                $avatarPath = null;
                if ($request->filled('cropped_avatar')) {
                    $imageData = $request->cropped_avatar;
                    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                        $imageData = substr($imageData, strpos($imageData, ',') + 1);
                        $imageData = base64_decode($imageData);
                        $extension = strtolower($type[1]); // png, jpg, etc.

                        $filename = 'avatar_' . $Ma . '_' . time() . '.' . $extension;
                        file_put_contents(public_path('AnhDaiDien/' . $filename), $imageData);
                        $avatarPath = 'AnhDaiDien/' . $filename;
                    }
                } elseif ($request->hasFile('AnhDaiDien')) {
                    $file = $request->file('AnhDaiDien');
                    $filename = 'avatar_' . $Ma . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('AnhDaiDien'), $filename);
                    $avatarPath = 'AnhDaiDien/' . $filename;
                }

                // Handle CCCD images (Front and Back)
                $anhCccdPaths = [];
                if ($request->hasFile('anh_cccd')) {
                    $file = $request->file('anh_cccd');
                    $filename = 'cccd_front_' . $Ma . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/cccd'), $filename);
                    $anhCccdPaths[] = 'uploads/cccd/' . $filename; // Store as array with 1 element for compatibility
                }

                $anhCccdSauPath = null;
                if ($request->hasFile('anh_cccd_sau')) {
                    $file = $request->file('anh_cccd_sau');
                    $filename = 'cccd_back_' . $Ma . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/cccd'), $filename);
                    $anhCccdSauPath = 'uploads/cccd/' . $filename;
                }

                // Handle multiple BHXH images
                $anhBhxhPaths = [];
                if ($request->hasFile('anh_bhxh')) {
                    foreach ($request->file('anh_bhxh') as $file) {
                        $filename = 'bhxh_' . $Ma . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads/bhxh'), $filename);
                        $anhBhxhPaths[] = 'uploads/bhxh/' . $filename;
                    }
                }

                // 3. Convert date formats from dd-mm-yyyy to yyyy-mm-dd
                $ngaySinh = $this->convertDateFormat($request->NgaySinh);
                $ngayCap = $this->convertDateFormat($request->NgayCap);

                // 4. Tự động tạo tài khoản người dùng trước để lấy ID
                $user = NguoiDung::create([
                    'TaiKhoan' => $request->Email,
                    'Email' => $request->Email,
                    'SoDienThoai' => $request->SoDienThoai,
                    'MatKhau' => Hash::make($request->SoDienThoai),
                    'TrangThai' => 1, // Hoạt động
                ]);

                // 5. Tạo record trong bảng nhan_viens (thông tin cá nhân)
                $nhanVien = NhanVien::create([
                    'Ma' => $Ma,
                    'Ten' => $request->Ten,
                    'NguoiDungId' => $user->id,
                    'NgaySinh' => $ngaySinh,
                    'GioiTinh' => $request->GioiTinh,
                    'SoCCCD' => $request->SoCCCD,
                    'NgayCap' => $ngayCap,
                    'NoiCap' => $request->NoiCap,
                    'DiaChi' => $request->DiaChi,
                    'SoDienThoai' => $request->SoDienThoai,
                    'Email' => $request->Email,
                    'DanToc' => $request->DanToc,
                    'TonGiao' => $request->TonGiao,
                    'QuocTich' => $request->QuocTich,
                    'TinhTrangHonNhan' => $request->TinhTrangHonNhan,
                    'TenNganHang' => $request->TenNganHang,
                    'SoTaiKhoan' => $request->SoTaiKhoan,
                    'ChiNhanhNganHang' => $request->ChiNhanhNganHang,
                    'BHXH' => $request->BHXH,
                    'anh_bhxh' => $anhBhxhPaths,
                    'NoiCapBHXH' => $request->NoiCapBHXH,
                    'BHYT' => $request->BHYT,
                    'NoiCapBHYT' => $request->NoiCapBHYT,
                    'Note' => $request->Note,
                    'AnhDaiDien' => $avatarPath,
                    'anh_cccd' => $anhCccdPaths,
                    'anh_cccd_sau' => $anhCccdSauPath,
                    'TrangThai' => $request->TrangThai ?? 'dang_lam',
                ]);

                // Verify NhanVien was created successfully
                if (!$nhanVien || !$nhanVien->id) {
                    throw new \Exception('Failed to create NhanVien record');
                }

                // Refresh to ensure we have the latest data
                $nhanVien->refresh();

                \Log::info('NhanVien created successfully', [
                    'id' => $nhanVien->id,
                    'Ma' => $nhanVien->Ma,
                    'Ten' => $nhanVien->Ten
                ]);

                // 5. Tạo record trong bảng tt_nhan_vien_cong_viecs (thông tin công việc)
                // Chuyển đổi Nhom sang LoaiNhanVien: van_phong = 1, cong_nhan = 0
                $loaiNhanVien = ($request->Nhom === 'van_phong') ? 1 : 0;
                $ngayTuyenDung = $this->convertDateFormat($request->NgayTuyenDung);
                $ngayVaoBienChe = $this->convertDateFormat($request->NgayVaoBienChe);

                // Debug: Log NhanVien ID
                \Log::info('Creating TtNhanVienCongViec', [
                    'nhan_vien_id' => $nhanVien->id,
                    'ma_nhan_vien' => $Ma,
                    'loai_nhan_vien' => $loaiNhanVien
                ]);

                // Temporarily disable foreign key checks to bypass MySQL temp file issue
                \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                TtNhanVienCongViec::create([
                    'NhanVienId' => $nhanVien->id,
                    'LoaiNhanVien' => $loaiNhanVien,

                    'PhongBanId' => $request->PhongBanId,
                    'ChucVuId' => $request->ChucVuId,
                    'NgayTuyenDung' => $ngayTuyenDung,
                    'NgayVaoBienChe' => $ngayVaoBienChe,
                    'TrinhDoHocVan' => $request->TrinhDoHocVan,
                    'ChuyenNganh' => $request->ChuyenNganh,
                    'TrinhDoChuyenMon' => $request->TrinhDoChuyenMon,
                    'NgoaiNgu' => $request->NgoaiNgu,
                ]);

                // Khởi tạo phép năm tự động
                \App\Models\QuanLyPhepNam::khoiTaoPhepNam($nhanVien->id, date('Y'));

                // Re-enable foreign key checks
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                return $Ma;
            });

            $nvCreated = NhanVien::where('Ma', $Ma)->first();
            if ($nvCreated) {
                \App\Services\SystemLogService::log(
                    'Tạo mới',
                    'NhanVien',
                    $nvCreated->id,
                    "Thêm mới nhân viên: {$nvCreated->Ten} ({$nvCreated->Ma})"
                );
            }

            return response()->json([
                'success' => true,
                'message' => "Thêm nhân viên thành công! Mã nhân viên: {$Ma}",
                'ma_nhan_vien' => $Ma,
                'redirect' => route('nhan-vien.danh-sach')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm nhân viên: ' . $e->getMessage()
            ], 422);
        }
    }

    public function SuaView($id)
    {
        $employee = NhanVien::with([
            'ttCongViec.chucVu',
            'ttCongViec.phongBan'
        ])->findOrFail($id);

        // Ownership / Permission check
        if (!auth()->user()->can('Sửa Nhân Viên') && !auth()->user()->can('Chỉnh Sửa Một Phần Nhân Viên') && $employee->NguoiDungId != auth()->id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa nhân viên này.');
        }

        $phongBans = DmPhongBan::all();
        $chucVus = DmChucVu::all();

        return view('employees.edit', compact('employee', 'phongBans', 'chucVus'));
    }

    public function CapNhat(Request $request, $id)
    {
        try {
            // Validation rules
            $rules = [
                'Ten' => 'required|string|max:255',
                'SoCCCD' => 'nullable|string|max:20',
                'NgaySinh' => 'nullable|string',
                'GioiTinh' => 'required|in:0,1',
                'Email' => 'nullable|email|max:255',
                'SoDienThoai' => 'nullable|string|max:15',
                'LoaiNhanVien' => 'required|in:0,1',
                'PhongBanId' => 'required|exists:dm_phong_bans,id',
                'ChucVuId' => 'required|exists:dm_chuc_vus,id',
                'AnhDaiDien' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'cropped_avatar' => 'nullable|string',
                'TrangThai' => 'required|string|in:dang_lam,nghi_viec,nghi_thai_san',
            ];

            $messages = [
                'Ten.required' => 'Vui lòng nhập tên nhân viên',
                'GioiTinh.required' => 'Vui lòng chọn giới tính',
                'Email.email' => 'Email không đúng định dạng',
                'PhongBanId.required' => 'Vui lòng chọn phòng ban',
                'PhongBanId.exists' => 'Phòng ban không tồn tại',
                'ChucVuId.required' => 'Vui lòng chọn chức vụ',
                'ChucVuId.exists' => 'Chức vụ không tồn tại',
                'LoaiNhanVien.required' => 'Vui lòng chọn loại nhân viên',
                'anh_cccd.*.image' => 'File tải lên CCCD phải là hình ảnh.',
                'anh_cccd.*.max' => 'Kích thước mỗi ảnh CCCD không được vượt quá 5MB.',
                'anh_bhxh.*.image' => 'File tải lên BHXH phải là hình ảnh.',
                'anh_bhxh.*.max' => 'Kích thước mỗi ảnh BHXH không được vượt quá 5MB.',
            ];

            $rules['anh_cccd'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';
            $rules['anh_cccd_sau'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';
            $rules['anh_bhxh.*'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';

            $request->validate($rules, $messages);

            $employee = NhanVien::findOrFail($id);

            // Ownership / Permission check
            if (!auth()->user()->can('Sửa Nhân Viên') && !auth()->user()->can('Chỉnh Sửa Một Phần Nhân Viên') && $employee->NguoiDungId != auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật thông tin nhân viên này.'
                ], 403);
            }

            $oldData = $employee->toArray();

            // Handle Avatar Upload
            // Handle Avatar Upload
            if ($request->filled('cropped_avatar')) {
                $imageData = $request->cropped_avatar;
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageData = base64_decode($imageData);
                    $extension = strtolower($type[1]);

                    $ma = $employee->Ma ?: 'NV_OLD_' . $employee->id;
                    $filename = 'avatar_' . $ma . '_' . time() . '.' . $extension;
                    file_put_contents(public_path('AnhDaiDien/' . $filename), $imageData);
                    $employee->AnhDaiDien = 'AnhDaiDien/' . $filename;
                }
            } elseif ($request->hasFile('AnhDaiDien')) {
                $file = $request->file('AnhDaiDien');
                $ma = $employee->Ma ?: 'NV_OLD_' . $employee->id;
                $filename = 'avatar_' . $ma . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('AnhDaiDien'), $filename);

                // Update the attribute in the model instance
                $employee->AnhDaiDien = 'AnhDaiDien/' . $filename;
            }

            // Handle CCCD images
            if ($request->hasFile('anh_cccd')) {
                $ma = $employee->Ma ?: 'NV_OLD_' . $employee->id;
                $file = $request->file('anh_cccd');
                $filename = 'cccd_front_' . $ma . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/cccd'), $filename);
                $employee->anh_cccd = ['uploads/cccd/' . $filename];
            }

            if ($request->hasFile('anh_cccd_sau')) {
                $ma = $employee->Ma ?: 'NV_OLD_' . $employee->id;
                $file = $request->file('anh_cccd_sau');
                $filename = 'cccd_back_' . $ma . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/cccd'), $filename);
                $employee->anh_cccd_sau = 'uploads/cccd/' . $filename;
            }

            // Handle multiple BHXH images
            if ($request->hasFile('anh_bhxh')) {
                $anhBhxhPaths = [];
                $ma = $employee->Ma ?: 'NV_OLD_' . $employee->id;
                foreach ($request->file('anh_bhxh') as $file) {
                    $filename = 'bhxh_' . $ma . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/bhxh'), $filename);
                    $anhBhxhPaths[] = 'uploads/bhxh/' . $filename;
                }
                $employee->anh_bhxh = $anhBhxhPaths;
            }

            // Update NhanVien table
            $employee->update([
                'Ten' => $request->Ten,
                'SoCCCD' => $request->SoCCCD,
                'NoiCap' => $request->NoiCap,
                'NgayCap' => $this->convertDateFormat($request->NgayCap),
                'NgaySinh' => $this->convertDateFormat($request->NgaySinh),
                'GioiTinh' => $request->GioiTinh,
                'DiaChi' => $request->DiaChi,
                'QueQuan' => $request->QueQuan,
                'DanToc' => $request->DanToc,
                'TonGiao' => $request->TonGiao,
                'QuocTich' => $request->QuocTich,
                'TinhTrangHonNhan' => $request->TinhTrangHonNhan,
                'Email' => $request->Email,
                'SoDienThoai' => $request->SoDienThoai,
                'TenNganHang' => $request->TenNganHang,
                'SoTaiKhoan' => $request->SoTaiKhoan,
                'ChiNhanhNganHang' => $request->ChiNhanhNganHang,
                'BHXH' => $request->BHXH,
                'anh_bhxh' => $employee->anh_bhxh,
                'NoiCapBHXH' => $request->NoiCapBHXH,
                'BHYT' => $request->BHYT,
                'NoiCapBHYT' => $request->NoiCapBHYT,
                'Note' => $request->Note,
                'AnhDaiDien' => $employee->AnhDaiDien,
                'anh_cccd' => $employee->anh_cccd,
                'anh_cccd_sau' => $employee->anh_cccd_sau,
                'TrangThai' => $request->TrangThai,
            ]);

            // Sync account status if checked
            if ($request->sync_account_status == "1" && $employee->nguoiDung) {
                // Map enum status to integer status for User account (1: Active, 0: Locked)
                $userStatus = ($request->TrangThai === 'nghi_viec') ? 0 : 1;
                $employee->nguoiDung->update([
                    'TrangThai' => $userStatus
                ]);
            }

            // Update or create TtNhanVienCongViec
            TtNhanVienCongViec::updateOrCreate(
                ['NhanVienId' => $employee->id],
                [
                    'LoaiNhanVien' => $request->LoaiNhanVien,

                    'PhongBanId' => $request->PhongBanId,
                    'ChucVuId' => $request->ChucVuId,
                    'NgayTuyenDung' => $this->convertDateFormat($request->NgayTuyenDung),
                    'NgayVaoBienChe' => $this->convertDateFormat($request->NgayVaoBienChe),
                    'TrinhDoHocVan' => $request->TrinhDoHocVan,
                    'ChuyenNganh' => $request->ChuyenNganh,
                    'TrinhDoChuyenMon' => $request->TrinhDoChuyenMon,
                    'NgoaiNgu' => $request->NgoaiNgu,
                ]
            );

            $newData = $employee->fresh()->toArray();
            \App\Services\SystemLogService::log(
                'Cập nhật',
                'NhanVien',
                $employee->id,
                "Cập nhật thông tin nhân viên: {$employee->Ten}",
                $oldData,
                $newData
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin nhân viên thành công!',
                'redirect' => route('nhan-vien.info', $employee->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Kiểm tra CCCD đã tồn tại chưa
     */
    public function checkCCCDExists(Request $request)
    {
        $cccd = $request->so_cccd;
        $nhanVienId = $request->nhan_vien_id; // Null khi tạo mới

        if (empty($cccd)) {
            return response()->json(['exists' => false, 'message' => '']);
        }

        $query = NhanVien::where('SoCCCD', $cccd);

        if ($nhanVienId) {
            $query->where('id', '!=', $nhanVienId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Số CCCD này đã tồn tại trong hệ thống' : ''
        ]);
    }

    /**
     * Kiểm tra BHXH đã tồn tại chưa
     */
    public function checkBHXHExists(Request $request)
    {
        $bhxh = $request->so_bhxh;
        $nhanVienId = $request->nhan_vien_id;

        if (empty($bhxh)) {
            return response()->json(['exists' => false, 'message' => '']);
        }

        $query = NhanVien::where('SoBHXH', $bhxh);

        if ($nhanVienId) {
            $query->where('id', '!=', $nhanVienId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Số BHXH này đã tồn tại trong hệ thống' : ''
        ]);
    }

    /**
     * Kiểm tra BHYT đã tồn tại chưa
     */
    public function checkBHYTExists(Request $request)
    {
        $bhyt = $request->so_bhyt;
        $nhanVienId = $request->nhan_vien_id;

        if (empty($bhyt)) {
            return response()->json(['exists' => false, 'message' => '']);
        }

        $query = NhanVien::where('SoBHYT', $bhyt);

        if ($nhanVienId) {
            $query->where('id', '!=', $nhanVienId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Số BHYT này đã tồn tại trong hệ thống' : ''
        ]);
    }

    /**
     * Kiểm tra chức vụ Loại 1 đã tồn tại trong phòng ban chưa
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkChucVuTonTai(Request $request)
    {

        $phongBanId = $request->phong_ban_id;
        $chucVuId = $request->chuc_vu_id;
        $nhanVienId = $request->nhan_vien_id; // Null khi tạo mới, có giá trị khi cập nhật

        // Lấy thông tin chức vụ
        $chucVu = DmChucVu::find($chucVuId);

        // Nếu không phải chức vụ Loại 1, không cần kiểm tra
        if (!$chucVu || $chucVu->Loai != 1) {
            return response()->json([
                'exists' => false,
                'message' => ''
            ]);
        }

        // Kiểm tra logic: 1 Phòng ban, 1 Chức vụ loại 1
        $query = TtNhanVienCongViec::where('PhongBanId', $phongBanId)
            ->where('ChucVuId', $chucVuId);

        // Nếu đang cập nhật, loại trừ nhân viên hiện tại
        if ($nhanVienId) {
            $query->where('NhanVienId', '!=', $nhanVienId);
        }

        $existingNhanVien = $query->first();

        if ($existingNhanVien) {
            // Lấy tên nhân viên đang giữ chức vụ
            $nhanVien = NhanVien::find($existingNhanVien->NhanVienId);

            return response()->json([
                'exists' => true,
                'message' => "Phòng ban này đã có nhân viên " . ($nhanVien ? $nhanVien->Ten : '') . " giữ chức vụ {$chucVu->Ten} rồi.",
                'chuc_vu_ten' => $chucVu->Ten
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => ''
        ]);
    }

    /**
     * Xóa một nhân viên và các dữ liệu liên quan
     */
    public function Xoa($id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $nhanVien = NhanVien::findOrFail($id);
            $nguoiDungId = $nhanVien->NguoiDungId;

            // Xóa dữ liệu liên quan
            \App\Models\TtNhanVienCongViec::where('NhanVienId', $id)->delete();
            \App\Models\ThanNhan::where('NhanVienId', $id)->delete();
            \App\Models\DienBienLuong::where('NhanVienId', $id)->delete();
            \App\Models\Luong::where('NhanVienId', $id)->delete();
            \App\Models\ChamCong::where('NhanVienId', $id)->delete();
            \App\Models\WorkFromHome::where('NhanVienId', $id)->delete();
            \App\Models\QuanLyPhepNam::where('NhanVienId', $id)->delete();
            \App\Models\DangKyNghiPhep::where('NhanVienId', $id)->delete();
            \App\Models\QuaTrinhCongTac::where('NhanVienId', $id)->delete();

            // Xử lý giữ lại hợp đồng khi nhân viên nghỉ việc: Đánh dấu hết hạn, lưu Tên + Mã nhân viên, và gỡ foreign key NhanVienId
            \App\Models\HopDong::where('NhanVienId', $id)->update([
                'TenNhanVien' => $nhanVien->Ten . ' - ' . $nhanVien->Ma,
                'TrangThai' => 0, // 0 = Hết hạn
                'NhanVienId' => null
            ]);

            // Set các ID tham chiếu sang null để bảo toàn dữ liệu lịch sử
            \App\Models\HopDong::where('NguoiKyId', $id)->update(['NguoiKyId' => null]);
            \App\Models\WorkFromHome::where('NguoiDuyetId', $id)->update(['NguoiDuyetId' => null]);
            \App\Models\DangKyNghiPhep::where('NguoiDuyetId', $id)->update(['NguoiDuyetId' => null]);

            // Xóa file ảnh đại diện nếu có
            if ($nhanVien->AnhDaiDien && file_exists(public_path($nhanVien->AnhDaiDien))) {
                unlink(public_path($nhanVien->AnhDaiDien));
            }

            // Xóa nhân viên
            $nhanVien->delete();

            // Xóa người dùng tương ứng
            if ($nguoiDungId) {
                \App\Models\NguoiDung::where('id', $nguoiDungId)->delete();
            }

            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nhân viên và các dữ liệu liên quan thành công!'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa nhân viên: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Xóa nhiều nhân viên và các dữ liệu liên quan
     */
    public function XoaNhieu(Request $request)
    {
        try {
            if (!$request->has('ids') || !is_array($request->ids) || count($request->ids) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn ít nhất một nhân viên để xóa.'
                ], 400);
            }

            $ids = $request->ids;

            \Illuminate\Support\Facades\DB::beginTransaction();
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $nhanViens = NhanVien::whereIn('id', $ids)->get();
            $nguoiDungIds = $nhanViens->pluck('NguoiDungId')->filter()->toArray();

            // Xóa dữ liệu liên quan cho tất cả nhân viên được chọn
            \App\Models\TtNhanVienCongViec::whereIn('NhanVienId', $ids)->delete();
            \App\Models\ThanNhan::whereIn('NhanVienId', $ids)->delete();
            \App\Models\DienBienLuong::whereIn('NhanVienId', $ids)->delete();
            \App\Models\Luong::whereIn('NhanVienId', $ids)->delete();
            \App\Models\ChamCong::whereIn('NhanVienId', $ids)->delete();
            \App\Models\WorkFromHome::whereIn('NhanVienId', $ids)->delete();
            \App\Models\QuanLyPhepNam::whereIn('NhanVienId', $ids)->delete();
            \App\Models\DangKyNghiPhep::whereIn('NhanVienId', $ids)->delete();
            \App\Models\QuaTrinhCongTac::whereIn('NhanVienId', $ids)->delete();

            // Xử lý giữ lại hợp đồng khi nhân viên nghỉ việc: Đánh dấu hết hạn, lưu Tên + Mã nhân viên, và gỡ foreign key NhanVienId
            foreach ($nhanViens as $nv) {
                \App\Models\HopDong::where('NhanVienId', $nv->id)->update([
                    'TenNhanVien' => $nv->Ten . ' - ' . $nv->Ma,
                    'TrangThai' => 0, // 0 = Hết hạn
                    'NhanVienId' => null
                ]);
            }

            // Cập nhật tham chiếu
            \App\Models\HopDong::whereIn('NguoiKyId', $ids)->update(['NguoiKyId' => null]);
            \App\Models\WorkFromHome::whereIn('NguoiDuyetId', $ids)->update(['NguoiDuyetId' => null]);
            \App\Models\DangKyNghiPhep::whereIn('NguoiDuyetId', $ids)->update(['NguoiDuyetId' => null]);

            // Xóa ảnh đại diện
            foreach ($nhanViens as $nv) {
                if ($nv->AnhDaiDien && file_exists(public_path($nv->AnhDaiDien))) {
                    unlink(public_path($nv->AnhDaiDien));
                }
            }

            // Xóa nhân viên
            NhanVien::whereIn('id', $ids)->delete();

            // Xóa người dùng tương ứng
            if (!empty($nguoiDungIds)) {
                \App\Models\NguoiDung::whereIn('id', $nguoiDungIds)->delete();
            }

            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ' . count($ids) . ' nhân viên và các dữ liệu liên quan thành công!'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa nhân viên: ' . $e->getMessage()
            ], 422);
        }
    }
}
