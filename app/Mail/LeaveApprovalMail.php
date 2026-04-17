<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SystemConfig;

class LeaveApprovalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $leave;
    public $companyName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($leave)
    {
        $this->leave = $leave;
        $this->companyName = SystemConfig::getValue('company_name', \App\Models\SystemConfig::getValue('company_name') );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $nhanVien = $this->leave->nhanVien;
        $subject = "Thông báo: Đơn nghỉ phép của bạn đã được duyệt - {$this->companyName}";

        return $this->subject($subject)
                    ->view('emails.leave_approval');
    }
}
