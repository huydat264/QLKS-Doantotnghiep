<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DangkyDangnhapModel extends Authenticatable
{
    use Notifiable;

    // Tên bảng trong MySQL của bạn
    protected $table = 'taikhoan';

    // Khóa chính (theo ảnh sơ đồ bạn gửi)
    protected $primaryKey = 'id_taikhoan';

    // Tắt timestamps mặc định của Laravel vì bảng của bạn chỉ có created_at
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'role',
        'trang_thai',
    ];

    protected $hidden = [
        'password',
    ];
}
