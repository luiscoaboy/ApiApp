<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroRespuesta extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='RegistroRespuesta';
    public $timestamps = false;
    protected $primaryKey = 'idRegistroRespuesta';
    protected $fillable = [
        'idRegistroRespuesta',
        'idRegistro',
        'idPregunta',
        'idRespuesta',
        'numeroIntentos',
        'esCorrecta',
    ];
}
