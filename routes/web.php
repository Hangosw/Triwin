<?php

use App\Http\Controllers\ChamCongController;
use App\Http\Controllers\ChucVuController;
use App\Http\Controllers\DangNhapController;
use App\Http\Controllers\HopDongController;
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\NghiPhepController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\PhongBanController;
use App\Http\Controllers\TangCaController;
use App\Http\Controllers\LuongController;
use App\Http\Controllers\DieuChuyenNoiBoController;
use App\Http\Controllers\CongTacController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CauHinhController;
use App\Http\Controllers\NgachLuongController;

use App\Http\Controllers\DashboardController;
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
        Route::get('/danh-sach', [NguoiDungController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Quản lý người dùng');
        Route::get('/data', [NguoiDungController::class, 'DataNguoiDung'])->name('data');
        Route::get('/tao', [NguoiDungController::class, 'TaoView'])->name('taoView')->middleware('permission:Quản lý người dùng');
        Route::post('/tao', [NguoiDungController::class, 'Tao'])->name('tao')->middleware('permission:Quản lý người dùng');
        Route::get('/sua/{id}', [NguoiDungController::class, 'SuaView'])->name('suaView')->middleware('permission:Quản lý người dùng');
        Route::post('/sua/{id}', [NguoiDungController::class, 'CapNhat'])->name('cap-nhat')->middleware('permission:Quản lý người dùng');
        Route::post('/xoa/{id}', [NguoiDungController::class, 'Xoa'])->name('xoa')->middleware('permission:Quản lý người dùng');
        Route::post('/xoa-nhieu', [NguoiDungController::class, 'XoaNhieu'])->name('xoa-nhieu')->middleware('permission:Quản lý người dùng');
    });



    Route::prefix('phong-ban')->name('phong-ban.')->group(function () {
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

    Route::prefix('chuc-vu')->name('chuc-vu.')->group(function () {
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
        Route::get('/danh-sach', [NhanVienController::class, 'DanhSachView'])->name('danh-sach');
        Route::get('/data', [NhanVienController::class, 'DataNhanVien'])->name('data');
        Route::get('/tao', [NhanVienController::class, 'TaoView'])->name('taoView');
        Route::post('/tao', [NhanVienController::class, 'Tao'])->name('tao');
        Route::get('/import', [NhanVienController::class, 'importView'])->name('importView');
        Route::post('/import', [NhanVienController::class, 'import'])->name('import');
        Route::get('/info/{id}', [NhanVienController::class, 'Info'])->name('info');
        Route::get('/sua/{id}', [NhanVienController::class, 'SuaView'])->name('suaView');
        Route::post('/sua/{id}', [NhanVienController::class, 'CapNhat'])->name('cap-nhat');
        Route::post('/xoa/{id}', [NhanVienController::class, 'Xoa'])->name('xoa');
        Route::post('/xoa-nhieu', [NhanVienController::class, 'XoaNhieu'])->name('xoa-nhieu');
    });

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
        Route::get('/danh-sach', [HopDongController::class, 'DanhSachView'])->name('danh-sach');
        Route::get('/data', [HopDongController::class, 'DataHopDong'])->name('data');
        Route::get('/tao', [HopDongController::class, 'TaoView'])->name('taoView');
        Route::post('/tao', [HopDongController::class, 'Tao'])->name('tao');
        Route::get('/info/{id}', [HopDongController::class, 'Info'])->name('info');
        Route::get('/sua/{id}', [HopDongController::class, 'SuaView'])->name('suaView');
        Route::post('/sua/{id}', [HopDongController::class, 'CapNhat'])->name('cap-nhat');
        Route::post('/xoa/{id}', [HopDongController::class, 'Xoa'])->name('xoa');
        Route::post('/xoa-nhieu', [HopDongController::class, 'XoaNhieu'])->name('xoa-nhieu');
        Route::get('/{id}/download-word', [HopDongController::class, 'downloadWord'])->name('download-word');
        Route::get('/{id}/print', [HopDongController::class, 'print'])->name('print');
        Route::get('/tai-ki/{id}', [HopDongController::class, 'RenewView'])->name('renew');
    });

    Route::prefix('cham-cong')->name('cham-cong.')->group(function () {
        // Chấm công cá nhân
        Route::get('/ca-nhan', [ChamCongController::class, 'CaNhanTaoView'])->name('ca-nhan');
        Route::post('/ca-nhan', [ChamCongController::class, 'CaNhanTao'])->name('ca-nhan.post');

        Route::get('/danh-sach', [ChamCongController::class, 'DanhSachView'])->name('danh-sach');
        Route::get('/data', [ChamCongController::class, 'DataChamCong'])->name('data');
        Route::get('/tao', [ChamCongController::class, 'TaoView'])->name('taoView')->middleware('permission:Quản lý chấm công');
        Route::post('/tao', [ChamCongController::class, 'Tao'])->name('tao')->middleware('permission:Quản lý chấm công');
        Route::get('/import', [ChamCongController::class, 'importView'])->name('importView')->middleware('permission:Quản lý chấm công');
        Route::post('/import', [ChamCongController::class, 'import'])->name('import')->middleware('permission:Quản lý chấm công');
        Route::get('/info/{id}', [ChamCongController::class, 'Info'])->name('info')->middleware('permission:Quản lý chấm công');
        Route::get('/sua/{id}', [ChamCongController::class, 'SuaView'])->name('suaView')->middleware('permission:Quản lý chấm công');
        Route::post('/sua/{id}', [ChamCongController::class, 'CapNhat'])->name('cap-nhat')->middleware('permission:Quản lý chấm công');
        Route::post('/xoa/{id}', [ChamCongController::class, 'Xoa'])->name('xoa')->middleware('permission:Quản lý chấm công');
        Route::post('/xoa-nhieu', [ChamCongController::class, 'XoaNhieu'])->name('xoa-nhieu')->middleware('permission:Quản lý chấm công');

    });

    Route::prefix('tang-ca')->name('tang-ca.')->group(function () {
        // Tuyến đường cho nhân viên
        Route::get('/ca-nhan', [TangCaController::class, 'CaNhanView'])->name('ca-nhan');
        Route::post('/tao-moi', [TangCaController::class, 'TaoMoi'])->name('tao-moi');
        Route::post('/yeu-cau-lai/{id}', [TangCaController::class, 'YeuCauLai'])->name('yeu-cau-lai');

        // Tuyến đường cho Admin
        Route::get('/danh-sach', [TangCaController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Quản lý tăng ca nghỉ phép');
        Route::post('/duyet/{id}', [TangCaController::class, 'Duyet'])->name('duyet')->middleware('permission:Duyệt yêu cầu');
        Route::post('/tu-choi/{id}', [TangCaController::class, 'TuChoi'])->name('tu-choi')->middleware('permission:Duyệt yêu cầu');
        Route::post('/bulk-duyet', [TangCaController::class, 'DuyetNhieu'])->name('bulk-duyet')->middleware('permission:Duyệt yêu cầu');
        Route::post('/bulk-tu-choi', [TangCaController::class, 'TuChoiNhieu'])->name('bulk-tu-choi')->middleware('permission:Duyệt yêu cầu');
        Route::post('/khoi-tao-phep-nam', [TangCaController::class, 'KhoiTaoPhepNamHangLoat'])->name('khoi-tao-phep-nam')->middleware('permission:Quản lý tăng ca nghỉ phép');
    });

    Route::prefix('nghi-phep')->name('nghi-phep.')->group(function () {
        Route::get('/ca-nhan', [NghiPhepController::class, 'CaNhanView'])->name('ca-nhan');
        Route::post('/tao-moi', [NghiPhepController::class, 'TaoMoi'])->name('tao-moi');
        Route::get('/danh-sach', [NghiPhepController::class, 'DanhSachView'])->name('danh-sach')->middleware('permission:Quản lý tăng ca nghỉ phép');
        Route::get('/con-lai', [NghiPhepController::class, 'DanhSachConLaiView'])->name('con-lai')->middleware('permission:Quản lý tăng ca nghỉ phép');
        Route::post('/duyet/{id}', [NghiPhepController::class, 'Duyet'])->name('duyet')->middleware('permission:Duyệt yêu cầu');
        Route::post('/tu-choi/{id}', [NghiPhepController::class, 'TuChoi'])->name('tu-choi')->middleware('permission:Duyệt yêu cầu');
        Route::post('/bulk-duyet', [NghiPhepController::class, 'DuyetNhieu'])->name('bulk-duyet')->middleware('permission:Duyệt yêu cầu');
        Route::post('/bulk-tu-choi', [NghiPhepController::class, 'TuChoiNhieu'])->name('bulk-tu-choi')->middleware('permission:Duyệt yêu cầu');

        Route::get('/config', [NghiPhepController::class, 'ConfigView'])->name('config')->middleware('permission:Quản lý tăng ca nghỉ phép');
        Route::post('/config/save', [NghiPhepController::class, 'SaveLoaiPhep'])->name('config.save')->middleware('permission:Quản lý tăng ca nghỉ phép');
        Route::post('/config/delete/{id}', [NghiPhepController::class, 'DeleteLoaiPhep'])->name('config.delete')->middleware('permission:Quản lý tăng ca nghỉ phép');
        Route::get('/api/limits', [NghiPhepController::class, 'getEmployeeLeaveLimits'])->name('api.limits');
    });

    Route::prefix('dieu-chuyen')->name('dieu-chuyen.')->group(function () {
        Route::get('/danh-sach', [DieuChuyenNoiBoController::class, 'index'])->name('index');
        Route::get('/tao', [DieuChuyenNoiBoController::class, 'create'])->name('taoView');
        Route::post('/tao', [DieuChuyenNoiBoController::class, 'store'])->name('tao');
        Route::post('/duyet/{id}', [DieuChuyenNoiBoController::class, 'duyet'])->name('duyet');
        Route::post('/tu-choi/{id}', [DieuChuyenNoiBoController::class, 'tuChoi'])->name('tuChoi');
    });

    // Thân nhân
    Route::post('/than-nhan/tao', [\App\Http\Controllers\ThanNhanController::class, 'store'])->name('than-nhan.tao');
    Route::post('/than-nhan/xoa/{id}', [\App\Http\Controllers\ThanNhanController::class, 'destroy'])->name('than-nhan.xoa');

    // Công tác
    Route::prefix('cong-tac')->name('cong-tac.')->group(function () {
        Route::get('/danh-sach', [CongTacController::class, 'index'])->name('danh-sach')
            ->middleware('permission:Quản lý công tác');
        Route::get('/tao', [CongTacController::class, 'taoView'])->name('taoView');
        Route::post('/tao', [CongTacController::class, 'store'])->name('store');
    });

    // Phân quyền (Roles)
    Route::prefix('phan-quyen')->name('roles.')->middleware('permission:Quản lý hệ thống')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/tao', [RoleController::class, 'create'])->name('create');
        Route::post('/tao', [RoleController::class, 'store'])->name('store');
        Route::get('/sua/{id}', [RoleController::class, 'edit'])->name('edit');
        Route::post('/sua/{id}', [RoleController::class, 'update'])->name('update');
        Route::post('/xoa/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Quyền (Permissions)
    Route::prefix('quyen')->name('permissions.')->middleware('permission:Quản lý hệ thống')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/tao', [PermissionController::class, 'create'])->name('create');
        Route::post('/tao', [PermissionController::class, 'store'])->name('store');
        Route::get('/sua/{id}', [PermissionController::class, 'edit'])->name('edit');
        Route::post('/sua/{id}', [PermissionController::class, 'update'])->name('update');
        Route::post('/xoa/{id}', [PermissionController::class, 'destroy'])->name('destroy');
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
Route::prefix('salary')->name('salary.')->group(function () {
    Route::get('/', [LuongController::class, 'IndexView'])->name('index');
    Route::get('/monthly', [LuongController::class, 'MonthlyView'])->name('monthly');
    Route::get('/config/{nhanVienId}', [LuongController::class, 'ConfigView'])->name('config');
    Route::get('/detail/{id?}', [LuongController::class, 'DetailView'])->name('detail');
    Route::get('/slip/{id}', [LuongController::class, 'SlipView'])->name('slip');
    Route::get('/tinh-luong/{id}', [LuongController::class, 'TinhLuong'])->name('tinh-luong');
    Route::post('/tinh-luong-hang-loat', [LuongController::class, 'TinhLuongHangLoat'])->name('tinh-luong-hang-loat');
    Route::get('/config-global', [LuongController::class, 'ConfigGlobalView'])->name('config-global');
    Route::post('/config-global', [LuongController::class, 'SaveConfigGlobal'])->name('config-global.save');
    Route::post('/config-global/save-params', [LuongController::class, 'SaveThamSoLuong'])->name('config-global.save-params');
    Route::post('/gui-mail', [LuongController::class, 'GuiMailLuong'])->name('gui-mail')->middleware('permission:Quản lý lương');

    // Ngạch lương (Admin only)
    Route::prefix('ngach-luong')->name('ngach-luong.')->group(function () {
        Route::get('/', [NgachLuongController::class, 'index'])->name('index');
        Route::post('/store', [NgachLuongController::class, 'store'])->name('store');
        Route::post('/update/{id}', [NgachLuongController::class, 'update'])->name('update');
        Route::post('/delete/{id}', [NgachLuongController::class, 'destroy'])->name('destroy');
    });
});
Route::get('/config', [CauHinhController::class, 'index'])->name('config.index');
Route::get('/lich-su', [\App\Http\Controllers\LichSuController::class, 'index'])->name('lich-su.index');
Route::post('/config', [CauHinhController::class, 'update'])->name('config.update');
Route::post('/config/ca-lam-viec', [CauHinhController::class, 'updateCaLamViec'])->name('config.ca-lam-viec.update');
Route::post('/config/lich-lam-viec', [CauHinhController::class, 'updateLichLamViec'])->name('config.lich-lam-viec.update');
Route::get('/settings', function () {
    return 'Cài đặt';
})->name('settings.index');

