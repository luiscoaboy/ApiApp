<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reto extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='Reto';
    //public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idCreador',
        'idTipoReto',
        'tema',
        'descripcion',
        'puntos',
        'feedback',
        'tiempoSegundos',
        'distribucion',
        'eliminado',
        'estado',
        'fecha_fin',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';
}
