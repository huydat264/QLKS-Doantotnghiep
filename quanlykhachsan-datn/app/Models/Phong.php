<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Tính giá phòng cho khoảng từ/ngày (bao gồm logic sale theo ngày của phòng).
     * Trả về tổng tiền cho toàn bộ khoảng (tương đương DatPhong::calculateRoomPrice trước đó).
     * @param string|\DateTimeInterface $ngayNhan
     * @param string|\DateTimeInterface $ngayTra
     * @return int|float
     */
    public function calculatePriceForPeriod($ngayNhan, $ngayTra)
    {
        $start = Carbon::parse($ngayNhan)->startOfDay();
        $end = Carbon::parse($ngayTra)->startOfDay();

        $soDem = $start->diffInDays($end);
        if ($soDem <= 0) $soDem = 1;

        $tongTien = 0;
        $giaGoc = $this->gia_phong;

        for ($i = 0; $i < $soDem; $i++) {
            $currentDay = $start->copy()->addDays($i);

            $isSale = false;
            if ($this->giam_gia_percent > 0 && $this->sale_tu_ngay && $this->sale_den_ngay) {
                $saleTu = Carbon::parse($this->sale_tu_ngay)->startOfDay();
                $saleDen = Carbon::parse($this->sale_den_ngay)->endOfDay();
                if ($currentDay->between($saleTu, $saleDen)) {
                    $isSale = true;
                }
            }

            if ($isSale) {
                $tongTien += $giaGoc * (1 - ($this->giam_gia_percent / 100));
            } else {
                $tongTien += $giaGoc;
            }
        }

        return $tongTien;
    }

    // Xử lý việc lưu ảnh - ưu tiên UploadedFile, nếu là link thì giữ nguyên
    public function setImageFromRequest($imageInput)
    {
        if ($imageInput instanceof UploadedFile) {
            return $imageInput->store('phong', 'public');
        }

        return $imageInput; // có thể là URL hoặc null
    }

    public function replaceImage($imageInput)
    {
        if ($this->anh && !filter_var($this->anh, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($this->anh)) {
                Storage::disk('public')->delete($this->anh);
            }
        }

        $new = $this->setImageFromRequest($imageInput);
        $this->anh = $new;
        $this->save();
        return $this->anh;
    }

    public function deleteImage()
    {
        if ($this->anh && !filter_var($this->anh, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($this->anh)) {
                Storage::disk('public')->delete($this->anh);
            }
        }
        $this->anh = null;
        $this->save();
    }
}
