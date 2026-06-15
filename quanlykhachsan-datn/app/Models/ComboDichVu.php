<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComboDichVu extends Model
{
    protected $table = 'combo_dichvu';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;
    protected $fillable = [
        'id_combo',
        'id_dichvu',
    ];
}
