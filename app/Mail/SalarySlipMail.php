<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalarySlipMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nhanVien;
    public $luong;
    public $thang;
    public $nam;
    public $hopDong;
    public $baoHiems;
    public $luongRecord;

    public $companyName;
    public $thanhPhan;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nhanVien, $luong, $thang, $nam, $luongRecord = null)
    {
        $this->nhanVien = $nhanVien;
        $this->luong = $luong;
        $this->thang = $thang;
        $this->nam = $nam;
        $this->luongRecord = $luongRecord;

        // Extract data for consistency with slip_partial
        $this->hopDong = $luong['hop_dong'] ?? null;
        $this->baoHiems = $luong['bao_hiems'] ?? [];

        // Fetch company name from SystemConfig
        $this->companyName = \App\Models\SystemConfig::getValue('company_name', \App\Models\SystemConfig::getValue('company_name'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Phiếu lương tháng {$this->thang}/{$this->nam} - {$this->companyName}")
            ->view('emails.salary_slip_v2');
    }
}
