<div class="tab-content" id="tab-relatives">
    <div class="detail-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="margin-bottom: 0; border-bottom: none;">
                <i class="bi bi-people-fill"></i>
                Danh sách thân nhân
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRelativeModal"
                style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
                <i class="bi bi-plus-lg"></i> Thêm thân nhân
            </button>
        </div>

        <div class="table-responsive premium-table">
            <table class="table mb-0" id="relativesTable">
                <thead>
                    <tr>
                        <th style="width: 250px;">Họ và tên</th>
                        <th>Mối quan hệ</th>
                        <th>Ngày sinh</th>
                        <th>CCCD/CMND</th>
                        <th>Số điện thoại</th>
                        <th>Giảm trừ</th>
                        <th style="width: 100px; text-align: center;">Thao tác</th>
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
                            <td style="font-weight: 500; color: #111827;">{{ $tn->HoTen }}</td>
                            <td>
                                <span class="badge-relationship {{ $relClass }}">
                                    {{ $relText }}
                                </span>
                            </td>
                            <td>{{ $tn->NgaySinh ? \Carbon\Carbon::parse($tn->NgaySinh)->format('d/m/Y') : '-' }}</td>
                            <td style="font-family: monospace; color: #4b5563;">{{ $tn->CCCD ?? '-' }}</td>
                            <td>{{ $tn->SoDienThoai ?? '-' }}</td>
                            <td>
                                @if($tn->LaGiamTruGiaCanh)
                                    <span class="badge badge-success"
                                        style="background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">
                                        <i class="bi bi-check-circle-fill"></i> Có giảm trừ
                                    </span>
                                @else
                                    <span class="badge badge-secondary"
                                        style="background: #F3F4F6; color: #4B5563; border: 1px solid #E5E7EB;">
                                        Không
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="action-icon-btn" onclick="deleteRelative({{ $tn->id }})" title="Xóa">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px; background: #fafafa;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                    <i class="bi bi-people" style="font-size: 48px; color: #d1d5db;"></i>
                                    <div style="color: #6b7280; font-size: 15px;">Chưa có thông tin thân nhân trong hồ sơ
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
