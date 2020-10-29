<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestasPregunta extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='RespuestasPregunta';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idPregunta',
        'idRespuesta',
        'esCorrecta',
        'idReto',
        'eliminado',
    ];
}
