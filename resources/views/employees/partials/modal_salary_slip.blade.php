<div id="slipModal" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.55); align-items:center; justify-content:center;
    overflow-y:auto; padding:24px 16px;
">
    <div style="
        border-radius:12px; width:100%; max-width:860px;
        margin:auto; box-shadow:0 25px 60px rgba(0,0,0,0.3);
        display:flex; flex-direction:column; max-height:90vh;
    " class="modal-slip-container">
        {{-- Modal Header --}}
        <div style="
            display:flex; justify-content:space-between; align-items:center;
            padding:16px 20px;
            background:linear-gradient(135deg,#0BAA4B,#088c3d);
            border-radius:12px 12px 0 0;
        " class="modal-slip-header">
            <div style="color:#fff; font-size:16px; font-weight:700;">
                <i class="bi bi-file-earmark-text"></i>
                &nbsp;Phiếu Lương
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <button id="btnPrintSlip" style="
                    background:#fff; color:#0BAA4B; border:none; border-radius:6px;
                    padding:6px 14px; font-size:13px; font-weight:600; cursor:pointer;
                    display:flex; align-items:center; gap:6px;
                ">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    In phiếu
                </button>
                <button onclick="closeSlipModal()" style="
                    background:rgba(255,255,255,0.2); border:none; border-radius:6px;
                    color:#fff; font-size:20px; cursor:pointer; width:32px; height:32px;
                    display:flex; align-items:center; justify-content:center; line-height:1;
                ">✕</button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div id="slipContent" style="padding:20px; overflow-y:auto; flex:1;" class="modal-slip-body">
            <div style="text-align:center; padding:40px; color:#6b7280;">
                <div style="font-size:32px; margin-bottom:8px;">⏳</div>
                <div>Đang tải phiếu lương...</div>
            </div>
        </div>
    </div>
</div>
