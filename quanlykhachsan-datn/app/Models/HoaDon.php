<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'hoadon';
    protected $primaryKey = 'id_hoadon';
    public $timestamps = false;
    protected $fillable = [
        'id_datphong',
        'tong_tien',
        'ngay_xuat',
    ];

    public function datphong()
    {
        return $this->belongsTo(DatPhong::class, 'id_datphong', 'id_datphong');
    }
}
