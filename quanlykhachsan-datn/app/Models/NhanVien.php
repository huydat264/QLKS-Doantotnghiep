<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'nhanvien';
    protected $primaryKey = 'id_nhanvien';
    public $timestamps = false;
    protected $fillable = [
        'tai_khoan_nhanvien_id',
        'ho_ten',
        'chuc_vu',
        'luong_co_ban',
        'ngay_vao_lam',
        'so_dien_thoai',
        'email',
    ];

    public function bangluongs()
    {
        return $this->hasMany(BangLuong::class, 'id_nhanvien', 'id_nhanvien');
    }

    public function chamcongs()
    {
        return $this->hasMany(ChamCong::class, 'id_nhanvien', 'id_nhanvien');
    }

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'tai_khoan_nhanvien_id', 'id_taikhoan');
    }
}
