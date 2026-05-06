<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khachhang'; // Tên bảng trong DB của mày
    protected $primaryKey = 'id_khachhang';
    public $timestamps = false;

    // Các cột cho phép lưu dữ liệu từ form (Mass Assignment)
    protected $fillable = [
        'id_taikhoan',
        'ho_ten',
        'ngay_sinh',
        'gioi_tinh',
        'so_dien_thoai',
        'email',
        'cccd',
        'dia_chi'
    ];

    // Liên kết với bảng User (nếu cần)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_taikhoan');
    }
}
