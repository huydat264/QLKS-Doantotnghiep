<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dichvu';
    protected $primaryKey = 'id_dichvu';

    protected $fillable = [
        'ten_dich_vu',
        'gia_dich_vu',
        'loai_dich_vu',
        'anh_dich_vu'
    ];
}
