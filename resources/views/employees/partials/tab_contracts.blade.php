<div class="tab-content" id="tab-contracts">
    <div class="detail-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="margin-bottom: 0; border-bottom: none;">
                <i class="bi bi-file-earmark-text-fill"></i>
                Lịch sử hợp đồng
            </h2>
            <a href="{{ route('hop-dong.taoView') }}?nhanVienId={{ $employee->id }}" class="btn btn-primary"
                style="border-radius: 8px; padding: 10px 20px; font-weight: 500;">
                <i class="bi bi-plus-lg"></i> Ký hợp đồng mới
            </a>
        </div>

        <div class="table-responsive premium-table">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Số hợp đồng</th>
                        <th>Loại hợp đồng</th>
                        <th>Chức vụ</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: right;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->hopDongs as $hd)
                        <tr>
                            <td style="font-weight: 600; color: #0F5132;">{{ $hd->SoHopDong }}</td>
                            <td>{{ $hd->loaiHopDong->TenLoai ?? 'Hợp đồng mới' }}</td>
                            <td>{{ $hd->chucVu->Ten ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($hd->NgayBatDau)->format('d/m/Y') }}</td>
                            <td>{{ $hd->NgayKetThuc ? \Carbon\Carbon::parse($hd->NgayKetThuc)->format('d/m/Y') : 'Không thời hạn' }}</td>
                            <td style="text-align: center;">
                                @if($hd->TrangThai == 1)
                                    <span class="badge badge-success">Đang hiệu lực</span>
                                @elseif($hd->TrangThai == 0)
                                    <span class="badge badge-secondary" style="background: #f3f4f6; color: #6b7280;">Hết hiệu lực</span>
                                @else
                                    <span class="badge badge-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <a href="{{ route('hop-dong.suaView', $hd->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    {{-- Re-sign button if expired --}}
                                    @if($hd->TrangThai == 0 || ($hd->NgayKetThuc && \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($hd->NgayKetThuc), false) <= 25))
                                        <a href="{{ route('hop-dong.renew', $hd->id) }}" class="btn btn-sm btn-outline-warning" title="Tái ký">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px; background: #fafafa;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                                    <i class="bi bi-file-earmark-x" style="font-size: 48px; color: #d1d5db;"></i>
                                    <div style="color: #6b7280; font-size: 15px;">Dữ liệu hợp đồng đang trống</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
