<?php

use App\Http\Controllers\ChamCongController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\DangNhapController;
use App\Http\Controllers\HopDongController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NghiPhepController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\PhongBanController;
use App\Http\Controllers\WorkFromHomeController;
use App\Http\Controllers\LuongController;
use App\Http\Controllers\CongTacController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CauHinhController;

use App\Http\Controllers\DmPlHopDongController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DangNhapController::class, 'DangNhap'])->name('login');
Route::post('/', [DangNhapController::class, 'XuLyDangNhap']);
Route::post('/login', [DangNhapController::class, 'XuLyDangNhap']);
Route::get('/logout', [DangNhapController::class, 'DangXuat'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/gui-birthday-mail/{id}', [DashboardController::class, 'GuiBirthdayMail'])->name('dashboard.gui-birthday-mail');

    // nguoidung
    Route::prefix('nguoi-dung')->name('nguoi-dung.')->group(function () {
        Route::get('/danh-sach', [NguoiDungController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Xem Danh Sách Người Dùng');
        Route::get('/data', [NguoiDungController::class, 'DataNguoiDung'])->name('data');
        Route::get('/tao', [NguoiDungController::class, 'TaoView'])->name('taoView')->middleware('permission:Tạo Người Dùng');
        Route::post('/tao', [NguoiDungController::class, 'Tao'])->name('tao')->middleware('permission:Tạo Người Dùng');
        Route::get('/sua/{id}', [NguoiDungController::class, 'SuaView'])->name('suaView')->middleware('permission:Chỉnh Sửa Người Dùng');
        Route::post('/sua/{id}', [NguoiDungController::class, 'CapNhat'])->name('cap-nhat')->middleware('permission:Chỉnh Sửa Người Dùng');
        Route::post('/xoa/{id}', [NguoiDungController::class, 'Xoa'])->name('xoa')->middleware('permission:Xóa Người Dùng');
        Route::post('/xoa-nhieu', [NguoiDungController::class, 'XoaNhieu'])->name('xoa-nhieu')->middleware('permission:Xóa Người Dùng');
        Route::post('/toggle-status/{id}', [NguoiDungController::class, 'toggleStatus'])->name('toggle-status')->middleware('permission:Chỉnh Sửa Người Dùng');
    });

    // Profile Settings
    Route::get('/cai-dat-tai-khoan', [ProfileController::class, 'SettingsView'])->name('profile.settings');
    Route::post('/cai-dat-tai-khoan/mat-khau', [ProfileController::class, 'UpdatePassword'])->name('profile.update-password');
    Route::post('/cai-dat-tai-khoan/email', [ProfileController::class, 'UpdateEmail'])->name('profile.update-email');



    Route::prefix('phong-ban')->name('phong-ban.')->middleware('permission:Quản lý tổ chức')->group(function () {
        Route::get('/danh-sach', [PhongBanController::class, 'DanhSachView'])->name('danh-sach');
        Route::get('/data', [PhongBanController::class, 'DataPhongBan'])->name('data');
        Route::get('/tao', [PhongBanController::class, 'TaoView'])->name('taoView');
        Route::post('/tao', [PhongBanController::class, 'Tao'])->name('tao');
        Route::get('/info/{id}', [PhongBanController::class, 'InfoView'])->name('info');
        Route::get('/sua/{id}', [PhongBanController::class, 'SuaView'])->name('suaView');
        Route::post('/sua/{id}', [PhongBanController::class, 'CapNhat'])->name('cap-nhat');
        Route::post('/xoa/{id}', [PhongBanController::class, 'Xoa'])->name('xoa');
        Route::post('/xoa-nhieu', [PhongBanController::class, 'XoaNhieu'])->name('xoa-nhieu');
    });

    Route::prefix('chuc-vu')->name('chuc-vu.')->middleware('permission:Quản lý tổ chức')->group(function () {
        Route::get('/danh-sach', [ChucVuController::class, 'DanhSachView'])->name('danh-sach');
        Route::get('/tao', [ChucVuController::class, 'TaoView'])->name('taoView');
        Route::post('/tao', [ChucVuController::class, 'Tao'])->name('tao');
        Route::get('/info/{id}', [ChucVuController::class, 'InfoView'])->name('info');
        Route::get('/sua/{id}', [ChucVuController::class, 'SuaView'])->name('suaView');
        Route::post('/sua/{id}', [ChucVuController::class, 'CapNhat'])->name('cap-nhat');
        Route::post('/xoa/{id}', [ChucVuController::class, 'Xoa'])->name('xoa');
        Route::post('/xoa-nhieu', [ChucVuController::class, 'XoaNhieu'])->name('xoa-nhieu');
    });



    Route::prefix('nhan-vien')->name('nhan-vien.')->group(function () {
        Route::get('/danh-sach', [NhanVienController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Xem Nhân Viên');
        Route::get('/data', [NhanVienController::class, 'DataNhanVien'])->name('data')->middleware('permission:Xem Nhân Viên');
        Route::get('/tao', [NhanVienController::class, 'TaoView'])->name('taoView')->middleware('permission:Thêm Nhân Viên');
        Route::post('/tao', [NhanVienController::class, 'Tao'])->name('tao')->middleware('permission:Thêm Nhân Viên');
        Route::get('/import', [NhanVienController::class, 'importView'])->name('importView')->middleware('permission:Thêm Nhân Viên');
        Route::post('/import', [NhanVienController::class, 'import'])->name('import')->middleware('permission:Thêm Nhân Viên');
        Route::get('/info/{id}', [NhanVienController::class, 'Info'])->name('info');
        Route::get('/sua/{id}', [NhanVienController::class, 'SuaView'])->name('suaView');
        Route::post('/sua/{id}', [NhanVienController::class, 'CapNhat'])->name('cap-nhat');
        Route::post('/xoa/{id}', [NhanVienController::class, 'Xoa'])->name('xoa')->middleware('permission:Xóa Nhân Viên');
        Route::post('/xoa-nhieu', [NhanVienController::class, 'XoaNhieu'])->name('xoa-nhieu')->middleware('permission:Xóa Nhân Viên');
    });

    // API chấm công nhân viên
    Route::get('/api/nhan-vien/{id}/cham-cong', [ChamCongController::class, 'ChamCongData'])
        ->name('api.nhan-vien.cham-cong');

    // API kiểm tra chức vụ tồn tại trong phòng ban
    Route::post('/api/check-chuc-vu-ton-tai', [NhanVienController::class, 'checkChucVuTonTai'])
        ->name('api.check-chuc-vu-ton-tai');

    // API kiểm tra unique fields
    Route::post('/api/check-cccd-exists', [NhanVienController::class, 'checkCCCDExists'])
        ->name('api.check-cccd-exists');
    Route::post('/api/check-bhxh-exists', [NhanVienController::class, 'checkBHXHExists'])
        ->name('api.check-bhxh-exists');
    Route::post('/api/check-bhyt-exists', [NhanVienController::class, 'checkBHYTExists'])
        ->name('api.check-bhyt-exists');


    Route::prefix('hop-dong')->name('hop-dong.')->group(function () {
        Route::get('/danh-sach', [HopDongController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Xem Danh Sách Hợp Đồng');
        Route::get('/data', [HopDongController::class, 'DataHopDong'])->name('data')->middleware('permission:Xem Danh Sách Hợp Đồng');
        Route::get('/tao', [HopDongController::class, 'TaoView'])->name('taoView')->middleware('permission:Tạo Hợp Đồng');
        Route::post('/tao', [HopDongController::class, 'Tao'])->name('tao')->middleware('permission:Tạo Hợp Đồng');
        Route::get('/info/{id}', [HopDongController::class, 'Info'])->name('info')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/sua/{id}', [HopDongController::class, 'SuaView'])->name('suaView')->middleware('permission:Sửa Hợp Đồng');
        Route::post('/sua/{id}', [HopDongController::class, 'CapNhat'])->name('cap-nhat')->middleware('permission:Sửa Hợp Đồng');
        Route::post('/xoa/{id}', [HopDongController::class, 'Xoa'])->name('xoa')->middleware('permission:Xóa Hợp Đồng');
        Route::post('/xoa-nhieu', [HopDongController::class, 'XoaNhieu'])->name('xoa-nhieu')->middleware('permission:Xóa Hợp Đồng');
        Route::get('/{id}/download-word', [HopDongController::class, 'downloadWord'])->name('download-word')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/{id}/download-nda-word', [HopDongController::class, 'downloadNDAWord'])->name('download-nda-word')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/{id}/download-phu-luc-word', [HopDongController::class, 'downloadPhuLucWord'])->name('download-phu-luc-word')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');

        // PDF Downloads
        Route::get('/{id}/download-pdf', [HopDongController::class, 'downloadPDF'])->name('download-pdf')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/{id}/download-nda-pdf', [HopDongController::class, 'downloadNDAPDF'])->name('download-nda-pdf')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/{id}/download-phu-luc-pdf', [HopDongController::class, 'downloadPhuLucPDF'])->name('download-phu-luc-pdf')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');

        Route::get('/{id}/print', [HopDongController::class, 'print'])->name('print')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/{id}/print-phu-luc', [HopDongController::class, 'printPhuLuc'])->name('print-phu-luc')->middleware('permission:Xem Danh Sách Hợp Đồng|Xem Hợp Đồng Cá Nhân');
        Route::get('/tai-ki/{id}', [HopDongController::class, 'RenewView'])->name('renew')->middleware('permission:Sửa Hợp Đồng');
        Route::post('/save-signature', [HopDongController::class, 'saveSignature'])->name('save-signature');

        // Danh mục phụ lục
        Route::prefix('dm-phu-luc')->name('dm-phu-luc.')->group(function () {
            Route::get('/', [DmPlHopDongController::class, 'index'])->name('index');
            Route::get('/data', [DmPlHopDongController::class, 'data'])->name('data');
            Route::post('/store', [DmPlHopDongController::class, 'store'])->name('store');
            Route::post('/update/{id}', [DmPlHopDongController::class, 'update'])->name('update');
        });

        // Danh mục loại hợp đồng
        Route::prefix('dm-loai-hop-dong')->name('loai-hop-dong.')->group(function () {
            Route::get('/', [\App\Http\Controllers\DmLoaiHopDongController::class, 'index'])->name('index');
            Route::get('/data', [\App\Http\Controllers\DmLoaiHopDongController::class, 'data'])->name('data');
            Route::post('/store', [\App\Http\Controllers\DmLoaiHopDongController::class, 'store'])->name('store');
            Route::post('/update/{id}', [\App\Http\Controllers\DmLoaiHopDongController::class, 'update'])->name('update');
            Route::post('/delete/{id}', [\App\Http\Controllers\DmLoaiHopDongController::class, 'destroy'])->name('destroy');
            Route::post('/toggle-status/{id}', [\App\Http\Controllers\DmLoaiHopDongController::class, 'toggleStatus'])->name('toggle-status');
        });
    });

    Route::prefix('cham-cong')->name('cham-cong.')->group(function () {
        // Chấm công cá nhân
        Route::get('/ca-nhan', [ChamCongController::class, 'CaNhanTaoView'])->name('ca-nhan');
        Route::post('/ca-nhan', [ChamCongController::class, 'CaNhanTao'])->name('ca-nhan.post');

        Route::get('/danh-sach', [ChamCongController::class, 'DanhSachView'])->name('danh-sach');
        Route::get('/tong-quan-ngay', [ChamCongController::class, 'TongQuanNgayView'])->name('tong-quan-ngay');
        Route::get('/data', [ChamCongController::class, 'DataChamCong'])->name('data');
        Route::get('/tao', [ChamCongController::class, 'TaoView'])->name('taoView')->middleware('permission:Chấm Công');
        Route::post('/tao', [ChamCongController::class, 'Tao'])->name('tao')->middleware('permission:Chấm Công');
        Route::get('/import', [ChamCongController::class, 'importView'])->name('importView')->middleware('permission:Chấm Công');
        Route::post('/import', [ChamCongController::class, 'import'])->name('import')->middleware('permission:Chấm Công');
        Route::get('/info/{id}', [ChamCongController::class, 'Info'])->name('info')->middleware('permission:Xem Danh Sách Chấm Công');
        Route::get('/sua/{id}', [ChamCongController::class, 'SuaView'])->name('suaView')->middleware('permission:Chấm Công');
        Route::post('/sua/{id}', [ChamCongController::class, 'CapNhat'])->name('cap-nhat')->middleware('permission:Chấm Công');
        Route::post('/xoa/{id}', [ChamCongController::class, 'Xoa'])->name('xoa')->middleware('permission:Chấm Công');
        Route::post('/xoa-nhieu', [ChamCongController::class, 'XoaNhieu'])->name('xoa-nhieu')->middleware('permission:Chấm Công');

    });

    Route::prefix('wfh')->name('wfh.')->group(function () {
        // Tuyến đường cho nhân viên
        Route::get('/ca-nhan', [WorkFromHomeController::class, 'CaNhanView'])->name('ca-nhan');
        Route::post('/tao-moi', [WorkFromHomeController::class, 'TaoMoi'])->name('tao-moi');

        // Tuyến đường cho Admin
        Route::get('/danh-sach', [WorkFromHomeController::class, 'DanhSachView'])->name('danh-sach');
        Route::post('/duyet/{id}', [WorkFromHomeController::class, 'Duyet'])->name('duyet');
        Route::post('/tu-choi/{id}', [WorkFromHomeController::class, 'TuChoi'])->name('tu-choi');
        Route::post('/bulk-duyet', [WorkFromHomeController::class, 'DuyetNhieu'])->name('bulk-duyet');
        Route::post('/bulk-tu-choi', [WorkFromHomeController::class, 'TuChoiNhieu'])->name('bulk-tu-choi');
    });

    Route::post('/nghi-phep/khoi-tao-phep-nam', [NghiPhepController::class, 'KhoiTaoPhepNamHangLoat'])
        ->name('nghi-phep.khoi-tao-phep-nam')
        ->middleware('permission:Sửa Nhân Viên');

    Route::prefix('nghi-phep')->name('nghi-phep.')->group(function () {
        Route::get('/ca-nhan', [NghiPhepController::class, 'CaNhanView'])->name('ca-nhan');
        Route::get('/dang-ky', [NghiPhepController::class, 'DangKyView'])->name('dang-ky');
        Route::post('/tao-moi', [NghiPhepController::class, 'TaoMoi'])->name('tao-moi');
        Route::get('/danh-sach', [NghiPhepController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Xem Danh Sách Nghỉ Phép');
        Route::get('/admin-dang-ky', [NghiPhepController::class, 'AdminDangKyView'])->name('admin-dang-ky')->middleware('permission:Xem Danh Sách Nghỉ Phép');
        Route::get('/con-lai', [NghiPhepController::class, 'DanhSachConLaiView'])->name('con-lai')->middleware('permission:Xem Danh Sách Nghỉ Phép');
        Route::post('/duyet/{id}', [NghiPhepController::class, 'Duyet'])->name('duyet')->middleware('permission:Duyệt Nghỉ Phép');
        Route::post('/tu-choi/{id}', [NghiPhepController::class, 'TuChoi'])->name('tu-choi')->middleware('permission:Duyệt Nghỉ Phép');
        Route::post('/bulk-duyet', [NghiPhepController::class, 'DuyetNhieu'])->name('bulk-duyet')->middleware('permission:Duyệt Nghỉ Phép');
        Route::post('/bulk-tu-choi', [NghiPhepController::class, 'TuChoiNhieu'])->name('bulk-tu-choi')->middleware('permission:Duyệt Nghỉ Phép');

        Route::get('/config', [NghiPhepController::class, 'ConfigView'])->name('config')->middleware('permission:Xem Danh Sách Nghỉ Phép');
        Route::post('/config/save', [NghiPhepController::class, 'SaveLoaiPhep'])->name('config.save')->middleware('permission:Xem Danh Sách Nghỉ Phép');
        Route::post('/config/delete/{id}', [NghiPhepController::class, 'DeleteLoaiPhep'])->name('config.delete')->middleware('permission:Xem Danh Sách Nghỉ Phép');
        Route::get('/api/limits', [NghiPhepController::class, 'getEmployeeLeaveLimits'])->name('api.limits');
    });



    // Người phụ thuộc
    Route::post('/nguoi-phu-thuoc/tao', [\App\Http\Controllers\ThanNhanController::class, 'store'])->name('nguoi-phu-thuoc.tao');
    Route::post('/nguoi-phu-thuoc/duyet/{id}', [\App\Http\Controllers\ThanNhanController::class, 'approve'])->name('nguoi-phu-thuoc.duyet');
    Route::post('/nguoi-phu-thuoc/tu-choi/{id}', [\App\Http\Controllers\ThanNhanController::class, 'reject'])->name('nguoi-phu-thuoc.tu-choi');
    Route::post('/nguoi-phu-thuoc/xoa/{id}', [\App\Http\Controllers\ThanNhanController::class, 'destroy'])->name('nguoi-phu-thuoc.xoa');

    // Công tác
    Route::prefix('cong-tac')->name('cong-tac.')->group(function () {
        Route::get('/danh-sach', [CongTacController::class, 'index'])->name('danh-sach')
            ->middleware('permission:Xem Danh Sách Công Tác');
        Route::get('/tao', [CongTacController::class, 'taoView'])->name('taoView');
        Route::post('/tao', [CongTacController::class, 'store'])->name('store');
        Route::post('/update/{id}', [CongTacController::class, 'update'])->name('update');
    });

    Route::prefix('salary')->name('salary.')->group(function () {
        Route::get('/', [LuongController::class, 'IndexView'])->name('index')->middleware('permission:Xem Danh Sách Lương');
        Route::get('/monthly', [LuongController::class, 'MonthlyView'])->name('monthly')->middleware('permission:Xem Danh Sách Lương');

        Route::get('/detail/{id?}', [LuongController::class, 'DetailView'])->name('detail')->middleware('permission:Xem Lương Cá Nhân');
        Route::get('/history/{id?}', [LuongController::class, 'HistoryView'])->name('history')->middleware('permission:Xem Lương Cá Nhân');
        Route::get('/slip/{id}', [LuongController::class, 'SlipView'])->name('slip')->middleware('permission:Xem Lương Cá Nhân');
        Route::get('/tinh-luong/{id}', [LuongController::class, 'TinhLuong'])->name('tinh-luong')->middleware('permission:Xem Danh Sách Lương');
        Route::post('/tinh-luong-update/{id}', [LuongController::class, 'UpdateSingleSalary'])->name('update-single')->middleware('permission:Xem Danh Sách Lương');
        Route::post('/tinh-luong-hang-loat', [LuongController::class, 'TinhLuongHangLoat'])->name('tinh-luong-hang-loat')->middleware('permission:Xem Danh Sách Lương');
        Route::get('/config-global', [LuongController::class, 'ConfigGlobalView'])->name('config-global')->middleware('permission:Xem Danh Sách Lương');
        Route::post('/config-global', [LuongController::class, 'SaveConfigGlobal'])->name('config-global.save')->middleware('permission:Xem Danh Sách Lương');
        Route::post('/config-global/save-params', [LuongController::class, 'SaveThamSoLuong'])->name('config-global.save-params')->middleware('permission:Xem Danh Sách Lương');
        Route::post('/gui-mail', [LuongController::class, 'GuiMailLuong'])->name('gui-mail')->middleware('permission:Xem Danh Sách Lương');


    });

    Route::middleware('permission:Quản lý hệ thống')->group(function () {
        // Phân quyền (Roles)
        Route::prefix('phan-quyen')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/tao', [RoleController::class, 'create'])->name('create');
            Route::post('/tao', [RoleController::class, 'store'])->name('store');
            Route::get('/sua/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::post('/sua/{id}', [RoleController::class, 'update'])->name('update');
            Route::post('/xoa/{id}', [RoleController::class, 'destroy'])->name('destroy');
        });

        // Quyền (Permissions)
        Route::prefix('quyen')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::get('/tao', [PermissionController::class, 'create'])->name('create');
            Route::post('/tao', [PermissionController::class, 'store'])->name('store');
            Route::get('/sua/{id}', [PermissionController::class, 'edit'])->name('edit');
            Route::post('/sua/{id}', [PermissionController::class, 'update'])->name('update');
            Route::post('/xoa/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        });

        Route::get('/config', [CauHinhController::class, 'index'])->name('config.index');
        Route::get('/lich-su', [\App\Http\Controllers\LichSuController::class, 'index'])->name('lich-su.index');
        Route::post('/config', [CauHinhController::class, 'update'])->name('config.update');
        Route::post('/config/ca-lam-viec', [CauHinhController::class, 'updateCaLamViec'])->name('config.ca-lam-viec.update');
        Route::post('/config/lich-lam-viec', [CauHinhController::class, 'updateLichLamViec'])->name('config.lich-lam-viec.update');
        Route::get('/settings', function () {
            return 'Cài đặt';
        })->name('settings.index');
    });
});

// Placeholder routes for sidebar items
Route::get('/users', function () {
    return 'Danh sách người dùng';
})->name('users.index');
Route::get('/departments', function () {
    return 'Danh sách phòng ban';
})->name('departments.index');
Route::get('/employees', function () {
    return 'Danh sách nhân viên';
})->name('employees.index');
Route::get('/employees/info', function () {
    return 'Thông tin nhân viên';
})->name('employees.info-demo');

Route::get('/chuc-vu', function () {
    return redirect()->route('chuc-vu.danh-sach');
})->name('chuc-vu.index');
Route::get('/contracts', function () {
    return redirect()->route('hop-dong.danh-sach');
})->name('contracts.index');
Route::get('/attendance', function () {
    return 'Chấm công';
})->name('attendance.index');
Route::get('/overtime-leave', function () {
    return 'Tăng ca & Nghỉ phép';
})->name('overtime-leave.index');

