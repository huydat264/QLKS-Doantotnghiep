<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\Phong;

class Combo extends Model
{
    // Chỉ định đúng tên bảng
    protected $table = 'combo';

    // Khóa chính
    protected $primaryKey = 'id_combo';

    // Tắt timestamps nếu trong bảng mày không có created_at, updated_at
    public $timestamps = false;

    // Các cột được phép tương tác
    protected $fillable = [
        'ten_combo',
        'mo_ta',
        'gia_combo',
        'so_dem_luu_tru',
        'gia_phong_dinh_muc',
        'hinh_anh',
        'loai_phong_ap_dung',
        'quyen_loi',   // Thêm cột này
        'dieu_khoan'
        , 'active'
    ];

    public function dichVus()
    {
        return $this->belongsToMany(DichVu::class, 'combo_dichvu', 'id_combo', 'id_dichvu');
    }

    // Lưu ảnh (nếu upload) hoặc chấp nhận link
    public function setImageFromRequest($imageInput)
    {
        if ($imageInput instanceof UploadedFile) {
            return $imageInput->store('combo', 'public');
        }

        // Nếu là link hoặc null
        return $imageInput;
    }

    // Thay ảnh: xóa ảnh cũ (nếu là file lưu trên storage/public), lưu ảnh mới và cập nhật model
    public function replaceImage($imageInput)
    {
        // Xóa ảnh cũ nếu là file local
        if ($this->hinh_anh && !filter_var($this->hinh_anh, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($this->hinh_anh)) {
                Storage::disk('public')->delete($this->hinh_anh);
            }
        }

        $new = $this->setImageFromRequest($imageInput);
        $this->hinh_anh = $new;
        $this->save();
        return $this->hinh_anh;
    }

    public function deleteImage()
    {
        if ($this->hinh_anh && !filter_var($this->hinh_anh, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists($this->hinh_anh)) {
                Storage::disk('public')->delete($this->hinh_anh);
            }
        }
        $this->hinh_anh = null;
        $this->save();
    }

    // Trả về giá áp dụng dựa trên số đêm và loại phòng áp dụng trong combo
    public function calculateComboPriceForStay($roomType, $nights)
    {
        // Nếu combo định giá theo ngày lưu trú cố định
        if ($this->so_dem_luu_tru) {
            return $this->gia_combo;
        }

        // Nếu combo có giá phòng định mức, áp dụng cho số đêm
        if ($this->gia_phong_dinh_muc) {
            return $this->gia_phong_dinh_muc * $nights;
        }

        // Fallback: trả giá combo nếu có
        return $this->gia_combo ?? 0;
    }
}
