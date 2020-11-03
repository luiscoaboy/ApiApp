<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreguntasReto extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='PreguntasReto';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idPregunta',
        'idReto',
        'eliminado',
    ];
}
