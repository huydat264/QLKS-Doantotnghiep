<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phong extends Model
{
    protected $table = 'phong';
    protected $primaryKey = 'id_phong';
    public $timestamps = false; // Tắt timestamps

    protected $fillable = [
    'so_phong', 'loai_phong', 'gia_phong', 'so_luong_nguoi', 'trang_thai', 'mo_ta', 'anh',
    'dien_tich', 'huong_phong', 'so_phong_ngu', 'tien_nghi', 'thong_tin_quan_trong'
];
}
