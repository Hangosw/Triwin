@extends('layouts.app')

@section('title', 'Tạo yêu cầu tạm ứng - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
<style>
    .max-limit-box {
        background-color: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 12px 16px;
        margin-top: 10px;
        display: none; /* hidden by default */
    }
    body.dark-theme .max-limit-box {
        background-color: #1e293b;
        border-color: #475569;
    }
    .text-success-custom {
        color: #059669;
        font-weight: 700;
        font-size: 16px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Tạo yêu cầu tạm ứng lương</h1>
    <p>Nhân viên có hợp đồng hợp lệ mới được tiếp nhận đề nghị tạm ứng</p>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-body" style="padding: 24px;">
        <form action="{{ route('tam-ung.store') }}" method="POST" id="tamUngForm">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Nhân viên yêu cầu <span class="text-danger">*</span></label>
                <select name="NhanVienId" id="nhanVienSelect" class="form-select select2" required>
                    <option value="">-- Chọn nhân viên --</option>
                    @foreach($nhanViens as $nv)
                        <option value="{{ $nv->id }}" {{ old('NhanVienId') == $nv->id ? 'selected' : '' }}>
                            {{ $nv->Ten }} - {{ $nv->Ma }}
                        </option>
                    @endforeach
                </select>
                @error('NhanVienId')
                    <div class="text-danger mt-1 text-sm">{{ $message }}</div>
                @enderror
                
                <div id="maxLimitBox" class="max-limit-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Hạn mức tạm ứng còn lại:</span>
                        <span id="maxLimitText" class="text-success-custom">Đang tải...</span>
                    </div>
                    <div style="font-size: 11px; margin-top: 4px;" class="text-muted">
                        Hạn mức bằng Tổng lương trừ các khoản đang chờ duyệt hoặc đã duyệt trong tháng hiện tại.
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Số tiền xin ứng (VNĐ) <span class="text-danger">*</span></label>
                <input type="text" id="soTienDisplay" class="form-control" value="{{ old('SoTien') }}" required disabled placeholder="Nhập số tiền tạm ứng (Chọn nhân viên trước)">
                <input type="hidden" id="soTienInput" name="SoTien" value="{{ old('SoTien') }}">
                @error('SoTien')
                    <div class="text-danger mt-1 text-sm">{{ $message }}</div>
                @enderror
                <div class="form-text mt-1 text-muted" id="tienChu" style="font-style: italic; font-size: 12px;"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Lý do <span class="text-danger">*</span></label>
                <textarea name="Lydo" class="form-control" rows="3" required placeholder="Ghi rõ lý do tạm ứng lương (Khai báo viện phí, việc gia đình, ...)"></textarea>
                @error('Lydo')
                    <div class="text-danger mt-1 text-sm">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('tam-ung.index') }}" class="btn btn-light">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Gửi đề nghị</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentLimit = 0;

    $(document).ready(function() {
        if ($('.select2').length) {
            $('.select2').select2({
                placeholder: "-- Chọn nhân viên --",
                width: '100%'
            });
        }

        // Handle employee change
        $('#nhanVienSelect').on('change', function() {
            const nvId = $(this).val();
            const btnSubmit = $('#btnSubmit');
            const soTienInput = $('#soTienInput');

            if (!nvId) {
                $('#maxLimitBox').hide();
                soTienInput.prop('disabled', true);
                btnSubmit.prop('disabled', true);
                return;
            }

            // Show loading
            $('#maxLimitBox').show();
            $('#maxLimitText').text('Đang tải...').removeClass('text-danger').addClass('text-success-custom');
            $('#soTienDisplay').prop('disabled', true);
            btnSubmit.prop('disabled', true);

            fetch(`{{ route('tam-ung.api.max-advance') }}?nhan_vien_id=${nvId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentLimit = parseFloat(data.max_amount);
                    $('#maxLimitText').text(data.formatted_amount);
                    
                    if (currentLimit <= 0) {
                        $('#maxLimitText').removeClass('text-success-custom').addClass('text-danger');
                        $('#maxLimitText').text('Không đủ điều kiện (Không có lương)');
                    } else {
                        $('#soTienDisplay').prop('disabled', false).attr('placeholder', 'Nhập số tiền tạm ứng');
                    }
                }
            })
            .catch(error => {
                $('#maxLimitText').text('Lỗi kết nối');
            });
        });

        // Trigger if old value exists
        if ($('#nhanVienSelect').val()) {
            $('#nhanVienSelect').trigger('change');
            setTimeout(() => {
                let oldVal = $('#soTienInput').val();
                if (oldVal) {
                    $('#soTienDisplay').val(new Intl.NumberFormat('en-US').format(oldVal)).trigger('input');
                }
            }, 1000);
        }

        // Check amount on input
        $('#soTienDisplay').on('input', function() {
            let rawValue = $(this).val().replace(/,/g, '').replace(/\./g, '').replace(/\D/g, '');
            if (!rawValue) {
                $(this).val('');
                $('#soTienInput').val('');
                $('#tienChu').text('');
                $('#btnSubmit').prop('disabled', true);
                return;
            }

            const amount = parseFloat(rawValue) || 0;
            
            // Format number to display 
            $(this).val(new Intl.NumberFormat('en-US').format(amount));
            $('#soTienInput').val(amount);

            const btnSubmit = $('#btnSubmit');

            // Format number to text immediately
            if (amount > 0) {
                $('#tienChu').text('Bằng chữ: ' + DocSoTien(amount));
            } else {
                $('#tienChu').text('');
            }

            if (amount > 0 && amount <= currentLimit) {
                btnSubmit.prop('disabled', false);
                $(this).removeClass('is-invalid');
                $('#amount-error').remove();
            } else {
                btnSubmit.prop('disabled', true);
                if (amount > currentLimit) {
                    $(this).addClass('is-invalid');
                    if (!$('#amount-error').length) {
                        $('<div id="amount-error" class="invalid-feedback">Số tiền xin ứng vượt quá hạn mức còn lại trong tháng.</div>').insertAfter($(this));
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $('#amount-error').remove();
                }
            }
        });
    });

    // Hàm đọc số tiền ra chữ (Tham khảo module chung nếu có)
    const mangso = ['không','một','hai','ba','bốn','năm','sáu','bảy','tám','chín'];
    function dochangchuc(so,daydu){
        let chuoi = "";
        const chuc = Math.floor(so/10);
        const donvi = so%10;
        if (chuc>1) {
            chuoi = " " + mangso[chuc] + " mươi";
            if (donvi==1) chuoi += " mốt";
        } else if (chuc==1) {
            chuoi = " mười";
            if (donvi==1) chuoi += " một";
        } else if (daydu && donvi>0) {
            chuoi = " lẻ";
        }
        if (donvi==5 && chuc>=1) chuoi += " lăm";
        else if (donvi>1||(donvi==1&&chuc==0)) chuoi += " " + mangso[donvi];
        return chuoi;
    }
    function docblock(so,daydu) {
        let chuoi = "";
        const tram = Math.floor(so/100);
        so = so%100;
        if (daydu || tram>0) {
            chuoi = " " + mangso[tram] + " trăm";
            chuoi += dochangchuc(so,true);
        } else {
            chuoi = dochangchuc(so,false);
        }
        return chuoi;
    }
    function dochangtrieu(so,daydu) {
        let chuoi = "";
        const trieu = Math.floor(so/1000000);
        so = so%1000000;
        if (trieu>0) {
            chuoi = docblock(trieu,daydu) + " triệu";
            daydu = true;
        }
        const ngan = Math.floor(so/1000);
        so = so%1000;
        if (ngan>0) {
            chuoi += docblock(ngan,daydu) + " nghìn";
            daydu = true;
        }
        if (so>0) {
            chuoi += docblock(so,daydu);
        }
        return chuoi;
    }
    function DocSoTien(so) {
        if (so==0) return mangso[0] + " đồng";
        let chuoi = "", hauto = "";
        do {
            const ty = so%1000000000;
            so = Math.floor(so/1000000000);
            if (so>0) chuoi = dochangtrieu(ty,true) + hauto + chuoi;
            else chuoi = dochangtrieu(ty,false) + hauto + chuoi;
            hauto = " tỷ";
        } while (so>0);
        chuoi = chuoi.trim();
        if (chuoi.length>0) chuoi = chuoi.charAt(0).toUpperCase() + chuoi.slice(1) + " đồng";
        return chuoi;
    }
</script>
@endpush
@endsection
