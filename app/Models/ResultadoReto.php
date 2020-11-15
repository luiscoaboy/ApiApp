<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoReto extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='Resultado';
    public $timestamps = false;
    protected $primaryKey = 'idResultado';
    protected $fillable = [
        'idResultado',
        'idReto',
        'idRegistro',
        'puntaje',
        'tiempo',
        'numeroIntentos'
    ];
}
