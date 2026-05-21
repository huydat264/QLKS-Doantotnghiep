<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DangkyDangnhapModel;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khachhang';
    protected $primaryKey = 'id_khachhang';
    public $timestamps = false;


    protected $fillable = [
        'tai_khoan_khachhang_id',
        'ho_ten',
        'ngay_sinh',
        'gioi_tinh',
        'so_dien_thoai',
        'email',
        'cccd',
        'dia_chi'
    ];
    public function user()
    {

        return $this->belongsTo(DangkyDangnhapModel::class, 'tai_khoan_khachhang_id', 'id_taikhoan');
    }
}
