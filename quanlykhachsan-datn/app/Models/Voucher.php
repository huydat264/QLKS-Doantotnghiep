<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'voucher';
    protected $primaryKey = 'id_voucher';
    public $timestamps = false;

    protected $fillable = [
        'ma_code',
        'loai_voucher',
        'muc_giam',
        'is_percent',
        'ngay_het_han',
        'id_khachhang',
        'trang_thai',
    ];

    public function khachhang()
    {
        return $this->belongsTo(KhachHang::class, 'id_khachhang', 'id_khachhang');
    }
}
