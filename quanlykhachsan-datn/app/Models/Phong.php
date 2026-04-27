<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    protected $table = 'phong';
    protected $primaryKey = 'id_phong';
    public $timestamps = false; // Tắt timestamps nếu bảng của mày không có created_at, updated_at

    protected $fillable = [
        'so_phong', 'loai_phong', 'gia_phong', 'so_luong_nguoi', 'trang_thai', 'mo_ta', 'anh'
    ];
}
