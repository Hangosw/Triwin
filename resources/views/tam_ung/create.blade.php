@extends('layouts.app')

@section('title', __('Create Salary Advance') . ' - ' . \App\Models\SystemConfig::getValue('company_name'))

@push('styles')
<style>
    .form-section {
        background: var(--bg-card);
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 24px;
        margin-bottom: 24px;
    }

    .form-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--accent-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .form-group label .required {
        color: #dc2626;
        margin-left: 4px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 16px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s;
        background-color: var(--bg-card);
        color: var(--text-primary);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(11, 170, 75, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 24px;
        border-top: 1px solid var(--border-color);
    }

    .limit-info-box {
        background-color: rgba(11, 170, 75, 0.05);
        border: 1px dashed var(--accent-color);
        border-radius: 8px;
        padding: 16px;
        margin-top: 12px;
        display: none;
    }

    .limit-value {
        font-weight: 700;
        color: var(--accent-color);
        font-size: 18px;
    }

    body.dark-theme .limit-info-box {
        background-color: rgba(11, 170, 75, 0.1);
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        height: 42px;
        padding: 6px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background-color: var(--bg-card);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text-primary);
        line-height: 28px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>{{ __('Create Salary Advance') }}</h1>
        <p>{{ __('Enter request details for salary advance') }}</p>
    </div>
    <a href="{{ route('tam-ung.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('Back to List') }}
    </a>
</div>

<form action="{{ route('tam-ung.store') }}" method="POST" id="advanceForm">
    @csrf

    <div class="form-section">
        <h2>
            <i class="bi bi-cash-stack"></i>
            {{ __('Advance Request Details') }}
        </h2>

        <div class="form-row">
            <div class="form-group">
                <label>{{ __('Employee') }} <span class="required">*</span></label>
                <select name="NhanVienId" id="nhanVienSelect" class="form-control select2" required>
                    <option value="">{{ __('— Select Employee —') }}</option>
                    @foreach($nhanViens as $nv)
                        <option value="{{ $nv->id }}" {{ old('NhanVienId') == $nv->id ? 'selected' : '' }}>
                            {{ $nv->Ma }} - {{ $nv->Ten }}
                        </option>
                    @endforeach
                </select>
                @error('NhanVienId')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror

                <div id="limitInfoBox" class="limit-info-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary small">{{ __('Remaining Advance Limit:') }}</span>
                        <span id="limitValue" class="limit-value">{{ __('Loading...') }}</span>
                    </div>
                    <div class="text-muted small mt-1 italic" style="font-size: 11px;">
                        {{ __('The limit is calculated based on the primary contract minus current month advances.') }}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('Advance Amount') }} (đ) <span class="required">*</span></label>
                <input type="text" id="amountDisplay" class="form-control" placeholder="{{ __('Select employee first') }}" disabled required>
                <input type="hidden" name="SoTien" id="amountInput" value="{{ old('SoTien') }}">
                @error('SoTien')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
                <div id="amountWords" class="text-muted small mt-2 italic" style="font-size: 12px;"></div>
            </div>
        </div>

        <div class="form-group mb-4">
            <label>{{ __('Reason') }} <span class="required">*</span></label>
            <textarea name="Lydo" class="form-control" rows="3" placeholder="{{ __('Enter request reason...') }}" required>{{ old('Lydo') }}</textarea>
            @error('Lydo')
                <div class="text-danger mt-1 small">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label>{{ __('Note') }}</label>
            <textarea name="GhiChu" class="form-control" rows="2" placeholder="{{ __('Optional additional notes...') }}">{{ old('GhiChu') }}</textarea>
        </div>

        <div class="form-actions">
            <a href="{{ route('tam-ung.index') }}" class="btn btn-secondary px-4">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary px-4" id="submitBtn" disabled>
                <i class="bi bi-send-fill"></i> {{ __('Submit Request') }}
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let currentLimit = 0;

    $(document).ready(function() {
        // Initialize Select2 if available
        if ($.fn.select2) {
            $('#nhanVienSelect').select2({
                width: '100%'
            });
        }

        // Handle Employee Change
        $('#nhanVienSelect').on('change', function() {
            const nvId = $(this).val();
            const limitBox = $('#limitInfoBox');
            const limitValue = $('#limitValue');
            const amountDisplay = $('#amountDisplay');
            const submitBtn = $('#submitBtn');

            if (!nvId) {
                limitBox.hide();
                amountDisplay.prop('disabled', true).val('').attr('placeholder', '{{ __("Select employee first") }}');
                submitBtn.prop('disabled', true);
                return;
            }

            limitBox.show();
            limitValue.text('{{ __("Loading...") }}').removeClass('text-danger').addClass('limit-value');
            amountDisplay.prop('disabled', true);
            submitBtn.prop('disabled', true);

            fetch(`{{ route('tam-ung.api.max-advance') }}?nhan_vien_id=${nvId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        currentLimit = parseFloat(data.max_amount);
                        limitValue.text(data.formatted_amount);

                        if (currentLimit <= 0) {
                            limitValue.removeClass('limit-value').addClass('text-danger').text('{{ __("No limit available") }}');
                            amountDisplay.prop('disabled', true).val('').attr('placeholder', '{{ __("No contract or limit reached") }}');
                        } else {
                            amountDisplay.prop('disabled', false).attr('placeholder', '{{ __("Enter amount...") }}');
                        }
                    }
                })
                .catch(err => {
                    limitValue.text('{{ __("Error loading limit") }}');
                });
        });

        // Handle Amount Input
        $('#amountDisplay').on('input', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            if (!val) {
                $(this).val('');
                $('#amountInput').val('');
                $('#amountWords').text('');
                $('#submitBtn').prop('disabled', true);
                return;
            }

            const amount = parseFloat(val);
            $(this).val(new Intl.NumberFormat('vi-VN').format(amount));
            $('#amountInput').val(amount);

            // Words
            if (amount > 0) {
                $('#amountWords').text('{{ __("In words:") }} ' + DocSoTien(amount));
            } else {
                $('#amountWords').text('');
            }

            // Validation
            if (amount > 0 && amount <= currentLimit) {
                $('#submitBtn').prop('disabled', false);
                $(this).removeClass('is-invalid');
                $('#limit-error').remove();
            } else {
                $('#submitBtn').prop('disabled', true);
                if (amount > currentLimit) {
                    $(this).addClass('is-invalid');
                    if (!$('#limit-error').length) {
                        $('<div id="limit-error" class="text-danger small mt-1">{{ __("Amount exceeds limit!") }}</div>').insertAfter($(this));
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $('#limit-error').remove();
                }
            }
        });

        // Recovery if old values
        if ($('#nhanVienSelect').val()) {
            $('#nhanVienSelect').trigger('change');
            setTimeout(() => {
                const oldAmt = $('#amountInput').val();
                if (oldAmt) {
                    $('#amountDisplay').val(new Intl.NumberFormat('vi-VN').format(oldAmt)).trigger('input');
                }
            }, 800);
        }
    });

    // Number to words logic (Vietnamese)
    const mangso = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    function dochangchuc(so, daydu) {
        let chuoi = "";
        const chuc = Math.floor(so / 10);
        const donvi = so % 10;
        if (chuc > 1) {
            chuoi = " " + mangso[chuc] + " mươi";
            if (donvi == 1) chuoi += " mốt";
        } else if (chuc == 1) {
            chuoi = " mười";
            if (donvi == 1) chuoi += " một";
        } else if (daydu && donvi > 0) {
            chuoi = " lẻ";
        }
        if (donvi == 5 && chuc >= 1) chuoi += " lăm";
        else if (donvi > 1 || (donvi == 1 && chuc == 0)) chuoi += " " + mangso[donvi];
        return chuoi;
    }
    function docblock(so, daydu) {
        let chuoi = "";
        const tram = Math.floor(so / 100);
        so = so % 100;
        if (daydu || tram > 0) {
            chuoi = " " + mangso[tram] + " trăm";
            chuoi += dochangchuc(so, true);
        } else {
            chuoi = dochangchuc(so, false);
        }
        return chuoi;
    }
    function dochangtrieu(so, daydu) {
        let chuoi = "";
        const trieu = Math.floor(so / 1000000);
        so = so % 1000000;
        if (trieu > 0) {
            chuoi = docblock(trieu, daydu) + " triệu";
            daydu = true;
        }
        const ngan = Math.floor(so / 1000);
        so = so % 1000;
        if (ngan > 0) {
            chuoi += docblock(ngan, daydu) + " nghìn";
            daydu = true;
        }
        if (so > 0) {
            chuoi += docblock(so, daydu);
        }
        return chuoi;
    }
    function DocSoTien(so) {
        if (so == 0) return mangso[0] + " đồng";
        let chuoi = "", hauto = "";
        do {
            const ty = so % 1000000000;
            so = Math.floor(so / 1000000000);
            if (so > 0) chuoi = dochangtrieu(ty, true) + hauto + chuoi;
            else chuoi = dochangtrieu(ty, false) + hauto + chuoi;
            hauto = " tỷ";
        } while (so > 0);
        chuoi = chuoi.trim();
        if (chuoi.length > 0) chuoi = chuoi.charAt(0).toUpperCase() + chuoi.slice(1) + " đồng";
        return chuoi;
    }
</script>
@endpush