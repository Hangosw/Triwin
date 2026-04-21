@php
    $laborContracts = $employee->hopDongs->filter(fn($hd) => !str_starts_with($hd->Loai ?? '', 'nda'));
    $ndaContracts = $employee->hopDongs->filter(fn($hd) => str_starts_with($hd->Loai ?? '', 'nda'));
    
    $latestLabor = $laborContracts->where('TrangThai', 1)->first() ?? $laborContracts->first();
    $latestNDA = $ndaContracts->where('TrangThai', 1)->first() ?? $ndaContracts->first();
    $latestPhuLuc = $latestLabor ? \App\Models\PhuLucHopDong::where('HopDongGocId', $latestLabor->id)->latest()->first() : null;
@endphp

<div class="tab-content" id="tab-contracts">
    <!-- Top Cards Section -->
    <style>
        .contracts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        @media (max-width: 991px) {
            .contracts-grid {
                grid-template-columns: 1fr;
            }
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-secondary {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        .contract-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            height: 100%;
        }
        .contract-card:hover {
            border-color: #0BAA4B;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
    </style>

    <div class="contracts-grid">
        <!-- Labor Contract Card -->
        <div class="contract-card">
            <div style="padding: 20px; flex-grow: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">HĐ Lao động</div>
                        <div style="font-size: 15px; font-weight: 700; color: #111827; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            @if($latestLabor)
                                {{ $latestLabor->loaiHopDong->TenLoai ?? 'HĐ Lao động' }}
                                @if($latestLabor->TrangThai == 1)
                                    <span style="font-size: 9px; padding: 2px 8px; background: #dcfce7; color: #15803d; border-radius: 10px; margin-left: 4px; vertical-align: middle;">Active</span>
                                @endif
                            @else
                                <span style="color: #9ca3af;">Chưa có dữ liệu</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="font-size: 13px; color: #4b5563;">
                    Số: <span style="font-weight: 600; color: #111827;">{{ $latestLabor->SoHopDong ?? 'N/A' }}</span>
                </div>
            </div>
            
            <div style="background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 12px 15px; display: flex; justify-content: center; gap: 15px;">
                @if($latestLabor)
                    <a href="{{ route('hop-dong.info', $latestLabor->id) }}" title="Xem chi tiết" style="color: #16a34a; font-size: 18px;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('hop-dong.download-word', $latestLabor->id) }}" title="Tải file Word" style="color: #2563eb; font-size: 18px;">
                        <i class="bi bi-file-earmark-word"></i>
                    </a>
                    <a href="{{ route('hop-dong.download-pdf', $latestLabor->id) }}" title="Tải file PDF" style="color: #dc2626; font-size: 18px;">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                @else
                    <span style="font-size: 11px; color: #9ca3af; font-style: italic;">Chưa có hợp đồng</span>
                @endif
            </div>
        </div>

        <!-- NDA Card -->
        <div class="contract-card">
            <div style="padding: 20px; flex-grow: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #fffaf5; color: #f97316; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Bảo mật (NDA)</div>
                        <div style="font-size: 15px; font-weight: 700; color: #111827; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            @if($latestNDA)
                                Thỏa thuận NDA
                                @if($latestNDA->TrangThai == 1)
                                    <span style="font-size: 9px; padding: 2px 8px; background: #ffedd5; color: #c2410c; border-radius: 10px; margin-left: 4px; vertical-align: middle;">Active</span>
                                @endif
                            @else
                                <span style="color: #9ca3af;">Chưa có dữ liệu</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="font-size: 13px; color: #4b5563;">
                    Số: <span style="font-weight: 600; color: #111827;">{{ $latestNDA->SoHopDong ?? 'N/A' }}</span>
                </div>
            </div>

            <div style="background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 12px 15px; display: flex; justify-content: center; gap: 15px;">
                @if($latestNDA)
                    <a href="{{ route('hop-dong.print', $latestNDA->id) }}" title="Xem" style="color: #16a34a; font-size: 18px;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('hop-dong.download-nda-word', $latestNDA->id) }}" title="Tải file Word" style="color: #2563eb; font-size: 18px;">
                        <i class="bi bi-file-earmark-word"></i>
                    </a>
                    <a href="{{ route('hop-dong.download-nda-pdf', $latestNDA->id) }}" title="Tải file PDF" style="color: #dc2626; font-size: 18px;">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                @else
                    <span style="font-size: 11px; color: #9ca3af; font-style: italic;">Sẽ tạo cùng HĐLĐ</span>
                @endif
            </div>
        </div>

        <!-- Addendum Card -->
        <div class="contract-card">
            <div style="padding: 20px; flex-grow: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #f0f9ff; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                        <i class="bi bi-file-earmark-plus"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Phụ lục HĐ</div>
                        <div style="font-size: 15px; font-weight: 700; color: #0284c7; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            @if($latestPhuLuc)
                                Phụ lục điều chỉnh
                                <span style="font-size: 9px; padding: 2px 8px; background: #e0f2fe; color: #0369a1; border-radius: 10px; margin-left: 4px; vertical-align: middle;">Active</span>
                            @else
                                <span style="color: #9ca3af;">Không có dữ liệu</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="font-size: 13px; color: #4b5563;">
                    @if($latestPhuLuc)
                        Ngày ký: <span style="font-weight: 600; color: #111827;">{{ \Carbon\Carbon::parse($latestPhuLuc->ngay_ky)->format('d/m/Y') }}</span>
                    @else
                        <span style="font-style: italic; color: #9ca3af;">Tự động khi có phụ cấp</span>
                    @endif
                </div>
            </div>

            <div style="background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 12px 15px; display: flex; justify-content: center; gap: 15px;">
                @if($latestPhuLuc && $latestLabor)
                    <a href="{{ route('hop-dong.print-phu-luc', $latestLabor->id) }}" title="Xem" style="color: #16a34a; font-size: 18px;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('hop-dong.download-phu-luc-word', $latestLabor->id) }}" title="Tải file Word" style="color: #2563eb; font-size: 18px;">
                        <i class="bi bi-file-earmark-word"></i>
                    </a>
                    <a href="{{ route('hop-dong.download-phu-luc-pdf', $latestLabor->id) }}" title="Tải file PDF" style="color: #dc2626; font-size: 18px;">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                @else
                    <span style="font-size: 11px; color: #9ca3af; font-style: italic;">Hệ thống tự tạo</span>
                @endif
            </div>
        </div>
    </div>

    <div class="detail-section">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="section-icon-circle">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h2 style="margin: 0; border-bottom: none; font-size: 1.25rem; font-weight: 700; color: #1f2937;">
                    Lịch sử hợp đồng lao động
                </h2>
            </div>
            @canany(['Sửa Hợp Đồng', 'Tạo Hợp Đồng'])
                <a href="{{ route('hop-dong.taoView') }}?nhanVienId={{ $employee->id }}" class="btn-premium-add text-decoration-none">
                    <i class="bi bi-plus-lg"></i>
                    <span>Ký hợp đồng mới</span>
                </a>
            @endcanany
        </div>

        <div class="premium-table">
            <table class="table mb-0" id="contractsTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="all">STT</th>
                        <th class="all">Số hợp đồng</th>
                        <th class="all">Loại hợp đồng</th>
                        <th class="min-tablet">Chức vụ</th>
                        <th class="min-tablet">Ngày bắt đầu</th>
                        <th class="none">Ngày kết thúc</th>
                        <th style="text-align: center;" class="all">Trạng thái</th>
                        <th style="width: 120px; text-align: center;" class="all">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($laborContracts as $index => $hd)
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td style="font-weight: 600;" class="text-primary-hr">{{ $hd->SoHopDong }}</td>
                            <td>{{ $hd->loaiHopDong->TenLoai ?? 'Hợp đồng lao động' }}</td>
                            <td>{{ $hd->chucVu->Ten ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($hd->NgayBatDau)->format('d/m/Y') }}</td>
                            <td>{{ $hd->NgayKetThuc ? \Carbon\Carbon::parse($hd->NgayKetThuc)->format('d/m/Y') : 'Không thời hạn' }}</td>
                            <td style="text-align: center;">
                                @if($hd->TrangThai == 1)
                                    <span class="badge badge-success">Đang hiệu lực</span>
                                @elseif($hd->TrangThai == 0 || $hd->TrangThai === null)
                                    <span class="badge badge-secondary">Hết hiệu lực</span>
                                @else
                                    <span class="badge badge-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 4px;">
                                    <a href="{{ route('hop-dong.info', $hd->id) }}" class="action-icon-btn text-info" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @canany(['Sửa Hợp Đồng', 'Tạo Hợp Đồng'])
                                        <a href="{{ route('hop-dong.suaView', $hd->id) }}" class="action-icon-btn text-primary" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcanany
                                    @canany(['Sửa Hợp Đồng', 'Tạo Hợp Đồng'])
                                        {{-- Re-sign button if expired --}}
                                        @if($hd->TrangThai == 0 || ($hd->NgayKetThuc && \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($hd->NgayKetThuc), false) <= 25))
                                            <a href="{{ route('hop-dong.renew', $hd->id) }}" class="action-icon-btn text-warning" title="Tái ký">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>
                                        @endif
                                    @endcanany
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

