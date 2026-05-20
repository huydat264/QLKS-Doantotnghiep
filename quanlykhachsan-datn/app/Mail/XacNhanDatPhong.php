<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class XacNhanDatPhong extends Mailable
{
    use Queueable, SerializesModels;

    public $donDat; // Biến này sẽ truyền data sang View

    public function __construct($donDat)
    {
        $this->donDat = $donDat;
    }

    public function build()
    {
        // Trỏ đến file view email của bạn
        return $this->subject('Xác nhận đặt phòng thành công!')
                    ->view('user.email_xacnhan');
    }
}
