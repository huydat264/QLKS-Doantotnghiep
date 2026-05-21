<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{
    use Notifiable;

    protected $table = 'taikhoan'; // Kết nối đúng tên bảng trong SQL của mày
    protected $primaryKey = 'id_taikhoan'; // Khóa chính tùy biến

    protected $fillable = [
        'username', 'password', 'role', 'trang_thai'
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = false; // Trong SQL không thấy có updated_at nên tắt timestamps mặc định
}
