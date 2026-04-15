<div class="tab-content" id="tab-relatives">
    <div class="detail-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="margin-bottom: 0; border-bottom: none;">
                <i class="bi bi-people-fill"></i>
                Danh sách người phụ thuộc
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRelativeModal"
                style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
                <i class="bi bi-plus-lg"></i> Thêm người phụ thuộc
            </button>
        </div>

        <div class="table-responsive premium-table">
            <table class="table mb-0" id="relativesTable">
                <thead>
                    <tr>
                        <th style="width: 200px;">Họ và tên</th>
                        <th>Mối quan hệ</th>
                        <th>Thông tin</th>
                        <th style="text-align: center;">Giảm trừ</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th>Ghi chú</th>
                        <th style="width: 120px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->thanNhans as $tn)
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
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px;" class="empty-state-cell">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                    <i class="bi bi-people" style="font-size: 48px; color: #d1d5db;"></i>
                                    <div style="color: #6b7280; font-size: 15px;">Chưa có thông tin người phụ thuộc</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>