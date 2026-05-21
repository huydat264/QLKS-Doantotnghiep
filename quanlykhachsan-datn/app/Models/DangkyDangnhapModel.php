<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class DangkyDangnhapModel extends Authenticatable
{
    use Notifiable;
    protected $table = 'taikhoan';

    protected $primaryKey = 'id_taikhoan';

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
