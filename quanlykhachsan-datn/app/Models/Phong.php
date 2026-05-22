<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Phong extends Model
{
    protected $table = 'phong';
    protected $primaryKey = 'id_phong';
    public $timestamps = false; // Tắt timestamps

    protected $fillable = [
        'so_phong', 'loai_phong', 'gia_phong', 'so_luong_nguoi', 'trang_thai', 'mo_ta', 'anh',
        'dien_tich', 'huong_phong', 'so_phong_ngu', 'tien_nghi', 'thong_tin_quan_trong',
        'giam_gia_percent', 'sale_tu_ngay', 'sale_den_ngay'
    ];

    protected $casts = [
        'giam_gia_percent' => 'integer',
        'sale_tu_ngay' => 'datetime',
        'sale_den_ngay' => 'datetime',
    ];
    // Thiết lập liên kết lấy đơn đặt phòng đang hoạt động (chỉ đơn Đã đặt)
    public function datPhongHienTai()
    {
        return $this->hasOne(DatPhong::class, 'id_phong')
                    ->whereIn('trang_thai', ['Đã đặt', 'Đã xác nhận'])
                    ->latestOfMany('ngay_dat');
    }

    public function getIsSaleActiveAttribute()
    {
        if ($this->giam_gia_percent <= 0 || !$this->sale_tu_ngay || !$this->sale_den_ngay) {
            return false;
        }

        $now = Carbon::now();
        return $now->between($this->sale_tu_ngay, $this->sale_den_ngay);
    }

    public function getGiaHienTaiAttribute()
    {
        if ($this->is_sale_active) {
            return (int) round($this->gia_phong * (1 - $this->giam_gia_percent / 100));
        }

        return $this->gia_phong;
    }
}
