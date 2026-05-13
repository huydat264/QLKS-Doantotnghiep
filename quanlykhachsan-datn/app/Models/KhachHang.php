<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khachhang';
    protected $primaryKey = 'id_khachhang';
    public $timestamps = false;

    // Sửa 'id_taikhoan' thành 'tai_khoan_khachhang_id' để khớp với DB
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

    // Cập nhật lại liên kết với bảng taikhoan
    public function user()
    {
        // 'tai_khoan_khachhang_id' là khóa ngoại ở bảng khachhang
        // 'id_taikhoan' là khóa chính ở bảng taikhoan
        return $this->belongsTo(User::class, 'tai_khoan_khachhang_id', 'id_taikhoan');
    }
}
