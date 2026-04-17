@extends('layouts.app')

@section('title', 'Quản lý đơn vị - ' . \App\Models\SystemConfig::getValue('company_name'))

@section('content')
<div class="page-header">
    <h1>Quản lý đơn vị</h1>
    <p>Danh sách các đơn vị trong hệ thống</p>
</div>

<!-- Actions Bar -->
<div class="card">
    <div class="action-bar">
        <div class="search-bar">
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" class="form-control" placeholder="Tìm kiếm đơn vị..." id="searchInput">
        </div>
        <div class="action-buttons">
            <a href="{{ route('don-vi.taoView') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm đơn vị
            </a>
        </div>
    </div>
</div>

<!-- Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px;">
    @forelse($donVis ?? [] as $donVi)
    <a href="{{ route('don-vi.info', $donVi->id) }}" style="text-decoration: none; color: inherit;">
        <div class="card donvi-card" style="transition: box-shadow 0.2s; cursor: pointer;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <div style="width: 48px; height: 48px; background-color: rgba(15, 81, 50, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg fill="none" stroke="#0BAA4B" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">{{ $donVi->Ten }}</h3>
                    <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px;">{{ $donVi->DiaChi ?? 'Chưa có địa chỉ' }}</p>
                    
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 14px;">
                            <span style="color: #6b7280;">Mã đơn vị:</span>
                            <span class="font-medium" style="color: #1f2937;">{{ $donVi->Ma }}</span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 14px;">
                            <span style="color: #6b7280;">Số phòng ban:</span>
                            <span class="font-medium" style="color: #0BAA4B;">{{ $donVi->phongBans->count() ?? 0 }} phòng ban</span>
                        </div>
                    </div>

                    <div style="padding-top: 16px; border-top: 1px solid #f3f4f6;">
                        <span class="badge badge-success">Hoạt động</span>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @empty
    <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px; margin: 0 auto 16px; color: #9ca3af;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <p style="color: #6b7280; font-size: 16px;">Chưa có đơn vị nào</p>
        <a href="{{ route('don-vi.taoView') }}" class="btn btn-primary" style="margin-top: 16px;">Thêm đơn vị đầu tiên</a>
    </div>
    @endforelse
</div>
@endsection

@push('styles')
<style>
    .donvi-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const cards = document.querySelectorAll('.donvi-card');
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.parentElement.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush
