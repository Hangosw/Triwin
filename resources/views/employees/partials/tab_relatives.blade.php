<div class="tab-content" id="tab-relatives">
    <div class="detail-section">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="section-icon-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h2 style="margin: 0; border-bottom: none; font-size: 1.25rem; font-weight: 700; color: #1f2937;">
                    Danh sách người phụ thuộc
                </h2>
            </div>
            <button class="btn-premium-add" data-bs-toggle="modal" data-bs-target="#addRelativeModal">
                <i class="bi bi-plus-lg"></i>
                <span>Thêm người phụ thuộc</span>
            </button>
        </div>

        <div class="premium-table">
            <table class="table mb-0" id="relativesTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="all">STT</th>
                        <th class="all">Họ và tên</th>
                        <th class="min-tablet">Mối quan hệ</th>
                        <th class="none">Thông tin</th>
                        <th class="min-tablet">Giảm trừ</th>
                        <th class="none">Trạng thái</th>
                        <th class="none">Ghi chú</th>
                        <th style="width: 100px; text-align: center;" class="all">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employee->thanNhans as $index => $tn)
                        @php
                            $relClass = match ($tn->QuanHe) {
                                'bo_de', 'me_de' => 'badge-bo-me',
                                'vo_chong' => 'badge-vo-chong',
                                'con_ruot', 'con_nuoi' => 'badge-con',
                                default => 'badge-khac'
                            };
                            $relText = match ($tn->QuanHe) {
                                'bo_de' => 'Bố đẻ',
                                'me_de' => 'Mẹ đẻ',
                                'vo_chong' => 'Vợ/Chồng',
                                'con_ruot' => 'Con ruột',
                                'con_nuoi' => 'Con nuôi',
                                default => 'Khác'
                            };
                        @endphp
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td style="font-weight: 500;">{{ $tn->HoTen }}</td>
                            <td>
                                <span class="badge-relationship {{ $relClass }}">
                                    {{ $relText }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 13px; line-height: 1.6;">
                                    <div><i class="bi bi-calendar3 text-muted me-1"></i> {{ $tn->NgaySinh ? \Carbon\Carbon::parse($tn->NgaySinh)->format('d/m/Y') : '-' }}</div>
                                    <div><i class="bi bi-card-text text-muted me-1"></i> {{ $tn->CCCD ?? '-' }}</div>
                                    <div><i class="bi bi-telephone text-muted me-1"></i> {{ $tn->SoDienThoai ?? '-' }}</div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($tn->LaGiamTruGiaCanh)
                                    <span class="badge badge-success"
                                        style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">
                                        <i class="bi bi-check-circle-fill"></i> Có
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Không</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $status = $tn->TrangThai ?? 0;
                                @endphp
                                @if($status == 1)
                                    <span class="badge badge-success"
                                        style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">
                                        <i class="bi bi-patch-check-fill"></i> Đã duyệt
                                    </span>
                                @elseif($status == 2)
                                    <span class="badge badge-danger"
                                        style="background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
                                        <i class="bi bi-x-circle-fill"></i> Từ chối
                                    </span>
                                @else
                                    <span class="badge" style="background: #FFF7ED; color: #9A3412; border: 1px solid #FFEDD5;">
                                        <i class="bi bi-hourglass-split"></i> Chờ duyệt
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 13px; max-width: 200px; color: #4B5563;">
                                    {{ $tn->GhiChu ?? '-' }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 4px; justify-content: center;">
                                    @if($status == 0 && auth()->user()->hasAnyRole(['Super Admin', 'System Admin']))
                                        <button class="action-icon-btn text-success" onclick="approveRelative({{ $tn->id }})"
                                            title="Duyệt">
                                            <i class="bi bi-check-lg" style="font-size: 1.2rem;"></i>
                                        </button>
                                        <button class="action-icon-btn text-danger" onclick="rejectRelative({{ $tn->id }})"
                                            title="Từ chối">
                                            <i class="bi bi-x-lg" style="font-size: 1.2rem;"></i>
                                        </button>
                                    @endif
                                    <button class="action-icon-btn text-secondary" onclick="deleteRelative({{ $tn->id }})" title="Xóa">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>