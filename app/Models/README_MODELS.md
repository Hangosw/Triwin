# HRM Database Models

## Danh sách Models theo nhóm chức năng

### 1. NHÓM NGƯỜI DÙNG & TỔ CHỨC
- **NguoiDung.php** - Quản lý người dùng/tài khoản (Authentication)
- **DonVi.php** - Quản lý đơn vị
- **DmPhongBan.php** - Quản lý phòng ban
- **DmChucVu.php** - Quản lý chức vụ

### 2. NHÓM NHÂN VIÊN & HỢP ĐỒNG
- **NhanVien.php** - Quản lý nhân viên
- **QuaTrinhCongTac.php** - Quản lý quá trình công tác/điều chuyển
- **DmLoaiHopDong.php** - Danh mục loại hợp đồng
- **HopDong.php** - Quản lý hợp đồng lao động

### 3. NHÓM LƯƠNG
- **NgachLuong.php** - Quản lý ngạch lương
- **BacLuong.php** - Quản lý bậc lương
- **ThamSoLuong.php** - Quản lý tham số lương cơ sở
- **DienBienLuong.php** - Quản lý diễn biến lương nhân viên
- **Luong.php** - Quản lý bảng lương

### 4. NHÓM CHUYÊN CẦN & PHÉP
- **ChamCong.php** - Quản lý chấm công
- **LoaiNghiPhep.php** - Danh mục loại nghỉ phép
- **WorkFromHome.php** - Quản lý làm việc từ xa (thay thế tăng ca)
- **CauHinhPhepNam.php** - Cấu hình phép năm
- **QuanLyPhepNam.php** - Quản lý số phép năm của nhân viên
- **DangKyNghiPhep.php** - Quản lý đăng ký nghỉ phép

### 5. NHÓM DANH MỤC HỆ THỐNG
- **DmNgayLe.php** - Danh sách ngày lễ

## Tính năng chính của Models

### Authentication & Authorization
```php
// NguoiDung.php extends Authenticatable
- Login/Logout functionality
- Password hashing
- Active/Inactive status
```

### Relationships (Eloquent ORM)
Tất cả models đều được định nghĩa đầy đủ relationships:
- `belongsTo()` - Quan hệ 1-1 (thuộc về)
- `hasMany()` - Quan hệ 1-n (có nhiều)

### Scopes (Query shortcuts)
```php
// Ví dụ scope functions:
NhanVien::vanPhong()->get(); // Lấy nhân viên văn phòng
Luong::chuaTra()->get(); // Lấy lương chưa trả
DangKyNghiPhep::dangCho()->get(); // Lấy đơn đang chờ duyệt
```

### Helper Methods
```php
// Ví dụ helper methods:
$nhanVien->getYearsOfService(); // Tính số năm công tác
$hopDong->isValid(); // Kiểm tra hợp đồng còn hiệu lực
$chamCong->getWorkingHours(); // Tính số giờ làm việc
$dienBienLuong->calculateTotalSalary($mucLuongCoSo, $phuCapChucVu);
```

### Casts (Type conversion)
```php
// Tự động convert kiểu dữ liệu:
- Integer fields → (int)
- Date fields → Carbon instances
- Decimal fields → (decimal)
- Datetime fields → Carbon instances
```

## Quy ước đặt tên

### Bảng (Tables)
- Số nhiều, snake_case: `nhan_viens`, `cham_congs`, `hop_dongs`

### Model Classes
- Số ít, PascalCase: `NhanVien`, `ChamCong`, `HopDong`

### Columns
- PascalCase: `NhanVienId`, `TrangThai`, `NgayBatDau`

### Foreign Keys
- Format: `[TenBang]Id` → `DonViId`, `PhongBanId`, `ChucVuId`

## Chú thích quan trọng

### Enum Values
```php
// GioiTinh: 1 là nam, 0 là nữ
// TrangThai: 0 là không hoạt động, 1 là hoạt động
// Loai (DmChucVu): 1 là trưởng phòng, 0 là bình thường
// Nhom (NhanVien): van_phong, cong_nhan
// LoaiLuong: 0 văn phòng, 1 công nhân, 2 cộng tác viên
// TrangThai (HopDong): 0 hết hạn, 1 còn hiệu lực, 2 bị hủy
// TrangThai (Approval): 0 từ chối, 1 đã duyệt, 2 đang chờ
// LoaiLe: 1 là dương, 0 là âm
```

### Validation Rules (nên implement)
```php
// After TuNgay/DenNgay → check dates
// PhuCapVuotKhung → percentage (%)
// Dem → counter for resubmission attempts
```

## Usage Examples

### Lấy nhân viên với các thông tin liên quan
```php
$nhanVien = NhanVien::with([
    'chucVu',
    'phongBan.donVi',
    'hopDongs',
    'dienBienLuongs.bacLuong.ngachLuong'
])->find($id);
```

### Tính lương nhân viên
```php
$dienBienLuong = DienBienLuong::getCurrentForEmployee($nhanVienId);
$thamSoLuong = ThamSoLuong::getCurrentBaseSalary();
$phuCapChucVu = $nhanVien->chucVu->PhuCapChucVu;

$totalSalary = $dienBienLuong->calculateTotalSalary(
    $thamSoLuong->MucLuongCoSo,
    $phuCapChucVu
);
```

### Quản lý phép năm
```php
$config = CauHinhPhepNam::getCurrentConfig();
$yearsOfService = $nhanVien->getYearsOfService();
$totalLeave = $config->calculateTotalLeave($yearsOfService);

$quanLyPhep = QuanLyPhepNam::getCurrentForEmployee($nhanVienId);
if ($quanLyPhep->hasEnoughLeave($soNgayNghi)) {
    $quanLyPhep->deductLeave($soNgayNghi);
}
```

### Lọc dữ liệu với Scopes
```php
// Lương chưa trả tháng 1/2024
$unpaidSalaries = Luong::chuaTra()
    ->thang(1, 2024)
    ->get();

// Đơn nghỉ phép đang chờ duyệt
$pendingLeaves = DangKyNghiPhep::dangCho()
    ->where('NguoiDuyetId', $managerId)
    ->get();

// Nhân viên văn phòng
$officeWorkers = NhanVien::vanPhong()
    ->active()
    ->get();
```

## Database Indexes
Models đã được tối ưu với indexes:
- Foreign keys
- Frequently queried fields (NhanVienId, PhongBanId, etc.)
- Date fields for time-based queries

## Notes
- Tất cả models sử dụng `InnoDB` engine
- Charset: `utf8mb4_unicode_ci` (support tiếng Việt có dấu)
- Soft deletes: Chưa implement (có thể thêm nếu cần)
- Timestamps: Laravel tự động quản lý `created_at`, `updated_at`
