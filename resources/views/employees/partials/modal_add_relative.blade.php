<div class="modal fade" id="addRelativeModal" tabindex="-1" aria-labelledby="addRelativeModalLabel" aria-hidden="true"
    style="display:none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-premium">
            <div class="modal-header">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div
                        style="background: rgba(255, 255, 255, 0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-person-plus-fill" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" id="addRelativeModalLabel" style="font-weight: 700; margin: 0;">Thêm
                            thân nhân mới</h5>
                        <p style="margin: 0; font-size: 13px; opacity: 0.8;">Điền thông tin người thân của nhân viên</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addRelativeForm">
                @csrf
                <input type="hidden" name="NhanVienId" value="{{ $employee->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Họ tên <span
                                    class="text-danger">*</span></label>
                            <div class="form-icon-group">
                                <i class="bi bi-person-fill form-icon"></i>
                                <input type="text" name="HoTen" class="form-control form-icon-input"
                                    placeholder="Họ và tên" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Quan hệ <span
                                    class="text-danger">*</span></label>
                            <div class="form-icon-group">
                                <i class="bi bi-diagram-3-fill form-icon"></i>
                                <select name="QuanHe" class="form-select form-icon-input" required>
                                    <option value="">-- Quan hệ --</option>
                                    <option value="bo_de">Bố đẻ</option>
                                    <option value="me_de">Mẹ đẻ</option>
                                    <option value="vo_chong">Vợ/Chồng</option>
                                    <option value="con_ruot">Con ruột</option>
                                    <option value="con_nuoi">Con nuôi</option>
                                    <option value="khac">Khác</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Ngày sinh</label>
                            <div class="form-icon-group">
                                <i class="bi bi-calendar-date-fill form-icon"></i>
                                <input type="date" name="NgaySinh" class="form-control form-icon-input">
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-weight: 600; color: #374151;">CCCD/CMND</label>
                            <div class="form-icon-group">
                                <i class="bi bi-card-heading form-icon"></i>
                                <input type="text" name="CCCD" class="form-control form-icon-input"
                                    placeholder="Số CCCD">
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Điện thoại</label>
                            <div class="form-icon-group">
                                <i class="bi bi-telephone-fill form-icon"></i>
                                <input type="text" name="SoDienThoai" class="form-control form-icon-input"
                                    placeholder="Số điện thoại">
                            </div>
                        </div>
                        <div class="col-5">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Mã số thuế</label>
                            <div class="form-icon-group">
                                <i class="bi bi-hash form-icon"></i>
                                <input type="text" name="MaSoThue" class="form-control form-icon-input"
                                    placeholder="Mã số thuế">
                            </div>
                        </div>
                        <div class="col-7">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Giấy tờ chứng minh
                                (Ảnh/PDF)</label>
                            <div class="input-group">
                                <span class="input-group-text"
                                    style="background: #fff; border-right: none; border-radius: 8px 0 0 8px;">
                                    <i class="bi bi-file-earmark-arrow-up-fill" style="color: #6b7280;"></i>
                                </span>
                                <input type="file" name="TepDinhKem" class="form-control"
                                    style="border-left: none; border-radius: 0 8px 8px 0; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="toggle-switch-group" onclick="document.getElementById('laGiamTru').click()">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="background: #D1FAE5; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-shield-check" style="color: #059669; font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #111827;">Người phụ thuộc</div>
                                        <div style="font-size: 13px; color: #6b7280;">Giảm trừ gia cảnh (thuế TNCN)
                                        </div>
                                    </div>
                                </div>
                                <div class="form-check form-switch" style="padding-left: 0; margin-bottom: 0;">
                                    <input class="form-check-input" type="checkbox" name="LaGiamTruGiaCanh"
                                        id="laGiamTru" value="1" style="width: 48px; height: 24px; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 24px; background: #f8fafc; border-top: 1px solid #e5e7eb;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        style="border-radius: 8px; padding: 10px 24px;">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary"
                        style="background: #0BAA4B; border: none; border-radius: 8px; padding: 10px 32px; font-weight: 600; box-shadow: 0 4px 6px -1px rgba(15, 81, 50, 0.2);">
                        Lưu thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
