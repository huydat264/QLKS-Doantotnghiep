<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChamCong extends Model
{
    protected $table = 'chamcong';
    protected $primaryKey = 'id_chamcong';
    public $timestamps = false;
    protected $fillable = [
        'id_nhanvien',
        'thang',
        'nam',
        'so_ngay_di_lam',
        'so_ngay_nghi_khong_phep',
        'so_ngay_nghi_co_phep',
    ];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhanvien', 'id_nhanvien');
    }
}
