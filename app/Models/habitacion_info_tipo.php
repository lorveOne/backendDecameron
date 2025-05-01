<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class habitacion_info_tipo extends Model
{
    use HasFactory;
    protected $table = 'habitacion_info_tipo';
    public $timestamps = false;
    protected $fillable = [
        'idHotel',
        'numHabi',
        'tipoHabi',
        'acomoda',
    ];
}
