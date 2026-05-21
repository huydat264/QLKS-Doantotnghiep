<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KhachHang;
use App\Models\Phong;
use App\Models\Combo;
use App\Models\ThanhToan;

class DatPhong extends Model
{
    protected $table = 'datphong';
    protected $primaryKey = 'id_datphong';
    public $timestamps = false;

    protected $fillable = [
        'id_khachhang',
        'id_phong',
        'id_combo',
        'id_voucher',
        'ngay_dat',
        'ngay_nhan',
        'ngay_tra',
        'loai_hinh_dat',
        'tong_tien_phai_tra',
        'trang_thai'
    ];

    public function khachhang()
    {
        return $this->belongsTo(KhachHang::class, 'id_khachhang', 'id_khachhang');
    }

    public function phong()
    {
        return $this->belongsTo(Phong::class, 'id_phong', 'id_phong');
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'id_combo', 'id_combo');
    }

    public function thanhToan()
    {
        return $this->hasMany(ThanhToan::class, 'id_datphong', 'id_datphong');
    }
}
