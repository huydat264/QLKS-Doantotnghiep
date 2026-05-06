<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}
