<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BangLuong extends Model
{
    protected $table = 'bangluong';
    protected $primaryKey = 'id_bangluong';
    public $timestamps = false;
    protected $fillable = [
        'id_nhanvien',
        'thang',
        'nam',
        'so_ngay_cong',
        'thuong',
        'phat',
        'thue_tncn',
        'luong_co_ban',
        'tong_luong',
    ];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhanvien', 'id_nhanvien');
    }
}
