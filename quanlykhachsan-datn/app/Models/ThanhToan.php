<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhToan extends Model
{
    protected $table = 'thanhtoan';
    protected $primaryKey = 'id_thanhtoan';
    public $timestamps = false;

    protected $fillable = [
        'id_datphong',
        'ngay_thanh_toan',
        'so_tien',
        'vnp_transaction_no',
        'vnp_response_code',
        'hinh_thuc',
        'ghi_chu',
        'loai_thanh_toan'
    ];

    public function datphong()
    {
        return $this->belongsTo(DatPhong::class, 'id_datphong', 'id_datphong');
    }
}
