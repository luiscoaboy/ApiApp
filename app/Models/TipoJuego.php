<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoJuego extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='TipoJuego';
    public $timestamps = false;
    protected $primaryKey = 'idTipoJuego';
    protected $fillable = [
        'idTipoJuego','descripcion'
    ];
}
