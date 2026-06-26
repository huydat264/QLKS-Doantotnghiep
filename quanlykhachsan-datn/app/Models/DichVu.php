<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DichVu extends Model
{
    use HasFactory;

    protected $table = 'dichvu';
    protected $primaryKey = 'id_dichvu';
    public $timestamps = false;

    protected $fillable = [
        'ten_dich_vu',
        'gia_von',
        'gia',
        'hinh_anh',
        'mo_ta'
    ];
}
