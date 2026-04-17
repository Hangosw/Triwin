<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SystemConfig;

class BirthdayMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nhanVien;
    public $companyName;

    /**
     * Create a new message instance.
     */
    public function __construct($nhanVien)
    {
        $this->nhanVien = $nhanVien;
        $this->companyName = SystemConfig::getValue('company_name', \App\Models\SystemConfig::getValue('company_name') );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("🎂 Chúc mừng sinh nhật, {$this->nhanVien->Ten}! 🎉")
            ->view('emails.birthday');
    }
}
