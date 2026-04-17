@extends('layouts.app')

@section('title', 'Tổng quan chấm công ngày ' . $dateObj->format('d/m/Y') . ' - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 4px;">Tổng quan chấm công ngày {{ $dateObj->format('d/m/Y') }}</h1>
            <p style="color: #6B7280; font-size: 14px;">Tổng quan tình hình chấm công trong ngày</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <input type="text" id="dateSelector" class="form-control datepicker" value="{{ $dateObj->format('d/m/Y') }}" 
                   style="width: 160px; height: 42px; border-radius: 8px; border: 1px solid #D1D5DB; background: white;">
            <a href="{{ route('cham-cong.danh-sach', ['day' => $dateObj->day, 'month' => $dateObj->month, 'year' => $dateObj->year]) }}" 
               class="btn btn-secondary" style="height: 42px; display: flex; align-items: center;">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Tóm tắt đầu trang -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
        <div class="card" style="padding: 24px; border-radius: 12px; border: 1px solid #E5E7EB; background: white;">
            <div style="color: #6B7280; font-size: 14px; font-weight: 600; margin-bottom: 12px;">Đã chấm công</div>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <span style="font-size: 32px; font-weight: 700; color: #111827;">{{ $checkedInCount }}/{{ $totalEmployeeCount }}</span>
            </div>
            <div style="color: #6B7280; font-size: 13px; margin-top: 4px;">Có {{ $totalEmployeeCount - $checkedInCount }} người chưa checkin</div>
        </div>

        <div class="card" style="padding: 24px; border-radius: 12px; border: 1px solid #E5E7EB; background: white;">
            <div style="color: #6B7280; font-size: 14px; font-weight: 600; margin-bottom: 12px;">Thiết bị</div>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <span style="font-size: 32px; font-weight: 700; color: #111827;">1/1</span>
            </div>
            <div style="color: #6B7280; font-size: 13px; margin-top: 4px;">Thiết bị đang hoạt động bình thường</div>
        </div>

        <div class="card" style="padding: 24px; border-radius: 12px; border: 1px solid #E5E7EB; background: white;">
            <div style="color: #6B7280; font-size: 14px; font-weight: 600; margin-bottom: 12px;">Giờ làm việc</div>
            <div style="display: flex; align-items: baseline; gap: 8px;">
                <span style="font-size: 32px; font-weight: 700; color: #111827;">{{ substr($gioVaoChuan, 0, 5) }} - {{ substr($gioRaChuan, 0, 5) }}</span>
            </div>
            <div style="color: #6B7280; font-size: 13px; margin-top: 4px;">Thứ 2, Thứ 3, Thứ 4, Thứ 5, Thứ 6, Thứ 7</div>
        </div>
    </div>

    <!-- Chi tiết 4 nhóm -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Nhóm 1: Đi sớm -->
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #F3F4F6; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: #374151;">Đi sớm</span>
                <span style="color: #10B981; font-weight: 700;">{{ $diSom->count() }}</span>
            </div>
            <div style="padding: 12px; flex: 1; min-height: 300px;">
                @forelse($diSom as $att)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 10px; border-bottom: 1px solid #F9FAFB;">
                        <span style="color: #6B7280; font-size: 13px; width: 65px;">{{ $att->Vao->format('H:i:s') }}</span>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #F3F4F6; overflow: hidden; cursor: pointer;" 
                             onclick="showAttendanceImage('{{ asset($att->AnhChamCong ?: ($att->nhanVien->AnhDaiDien ?? '')) }}')">
                            @if($att->AnhChamCong)
                                <img src="{{ asset($att->AnhChamCong) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @elseif($att->nhanVien && $att->nhanVien->AnhDaiDien)
                                <img src="{{ asset($att->nhanVien->AnhDaiDien) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9CA3AF; font-size:12px;">?</div>
                            @endif
                        </div>
                        <span style="font-size: 14px; color: #111827;">{{ $att->nhanVien->Ten }}</span>
                    </div>
                @empty
                    <div style="text-align: center; color: #9CA3AF; padding-top: 100px; font-size: 14px;">Chưa có dữ liệu</div>
                @endforelse
            </div>
        </div>

        <!-- Nhóm 2: Đi trễ -->
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #F3F4F6; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: #374151;">Đi trễ</span>
                <span style="color: #F59E0B; font-weight: 700;">{{ $diTre->count() }}</span>
            </div>
            <div style="padding: 12px; flex: 1; min-height: 300px;">
                @forelse($diTre as $att)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 10px; border-bottom: 1px solid #F9FAFB;">
                        <span style="color: #EF4444; font-size: 13px; width: 65px;">{{ $att->Vao->format('H:i:s') }}</span>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #F3F4F6; overflow: hidden; cursor: pointer;"
                             onclick="showAttendanceImage('{{ asset($att->AnhChamCong ?: ($att->nhanVien->AnhDaiDien ?? '')) }}')">
                            @if($att->AnhChamCong)
                                <img src="{{ asset($att->AnhChamCong) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @elseif($att->nhanVien && $att->nhanVien->AnhDaiDien)
                                <img src="{{ asset($att->nhanVien->AnhDaiDien) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9CA3AF; font-size:12px;">?</div>
                            @endif
                        </div>
                        <span style="font-size: 14px; color: #111827;">{{ $att->nhanVien->Ten }}</span>
                    </div>
                @empty
                    <div style="text-align: center; color: #9CA3AF; padding-top: 100px; font-size: 14px;">Chưa có dữ liệu</div>
                @endforelse
            </div>
        </div>

        <!-- Nhóm 3: Chưa Checkin -->
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #F3F4F6; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: #374151;">Chưa Checkin</span>
                <span style="color: #3B82F6; font-weight: 700;">{{ $chuaCheckin->count() }}</span>
            </div>
            <div style="padding: 12px; flex: 1; min-height: 300px; overflow-y: auto; max-height: 500px;">
                @forelse($chuaCheckin as $nv)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 10px; border-bottom: 1px solid #F9FAFB;">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #F3F4F6; overflow: hidden;">
                            @if($nv->AnhDaiDien)
                                <img src="{{ asset($nv->AnhDaiDien) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9CA3AF; font-size:12px;">?</div>
                            @endif
                        </div>
                        <span style="font-size: 14px; color: #111827;">{{ $nv->Ten }}</span>
                    </div>
                @empty
                    <div style="text-align: center; color: #9CA3AF; padding-top: 100px; font-size: 14px;">Đã checkin đầy đủ</div>
                @endforelse
                @if($chuaCheckin->count() > 20)
                    <div style="text-align: center; padding: 12px;">
                        <a href="#" style="font-size: 13px; color: #3B82F6; text-decoration: none;">Xem thêm...</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Nhóm 4: Về trễ -->
        <div class="card" style="background: white; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 16px 20px; border-bottom: 1px solid #F3F4F6; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: #374151;">Về trễ</span>
                <span style="color: #6366F1; font-weight: 700;">{{ $veTre->count() }}</span>
            </div>
            <div style="padding: 12px; flex: 1; min-height: 300px;">
                @forelse($veTre as $att)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 10px; border-bottom: 1px solid #F9FAFB;">
                        <span style="color: #6366F1; font-size: 13px; width: 65px;">{{ $att->Ra->format('H:i:s') }}</span>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #F3F4F6; overflow: hidden; cursor: pointer;"
                             onclick="showAttendanceImage('{{ asset($att->AnhChamCong ?: ($att->nhanVien->AnhDaiDien ?? '')) }}')">
                            @if($att->AnhChamCong)
                                <img src="{{ asset($att->AnhChamCong) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @elseif($att->nhanVien && $att->nhanVien->AnhDaiDien)
                                <img src="{{ asset($att->nhanVien->AnhDaiDien) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9CA3AF; font-size:12px;">?</div>
                            @endif
                        </div>
                        <span style="font-size: 14px; color: #111827;">{{ $att->nhanVien->Ten }}</span>
                    </div>
                @empty
                    <div style="text-align: center; color: #9CA3AF; padding-top: 100px; font-size: 14px;">
                        {{ $dateObj->isToday() && now()->format('H:i') < '17:30' ? 'Dữ liệu sẽ được cập nhật sau 17:30' : 'Không có dữ liệu' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#dateSelector', {
            dateFormat: 'd/m/Y',
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const date = selectedDates[0];
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const formattedDate = `${year}-${month}-${day}`;
                    window.location.href = '{{ route('cham-cong.tong-quan-ngay') }}?date=' + formattedDate;
                }
            }
        });
    });

    function showAttendanceImage(url) {
        if (!url || url.includes('?')) {
            // Check if it's just the placeholder or empty
            if (url.endsWith('/') || url.endsWith('?')) return;
        }
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Ảnh minh chứng chấm công',
            showConfirmButton: false,
            showCloseButton: true,
            width: '600px',
            background: 'transparent',
            backdrop: 'rgba(0,0,0,0.85)',
            customClass: {
                image: 'rounded-lg max-w-full',
                popup: 'p-0 bg-transparent'
            }
        });
    }
</script>
@endpush
