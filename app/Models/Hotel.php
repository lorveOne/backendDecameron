<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{
    use HasFactory;
    protected $table = 'hotel';
    public $timestamps = false;
    protected $fillable = [
        'nit',
        'nombre',
        'direccion',
        'ciudad',
        'numHab'
        
    ];

}
