<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuDungDichVu extends Model
{
    protected $table = 'sudungdichvu';
    protected $primaryKey = 'id_sudungdv';
    public $timestamps = false;
    protected $fillable = [
        'id_datphong',
        'id_dichvu',
        'so_luong',
        'ngay_su_dung',
        'thanh_tien',
    ];

    public function datphong()
    {
        return $this->belongsTo(DatPhong::class, 'id_datphong', 'id_datphong');
    }

    public function dichvu()
    {
        return $this->belongsTo(DichVu::class, 'id_dichvu', 'id_dichvu');
    }
}
