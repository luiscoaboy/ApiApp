<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='Preguntas';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'descripcion',
        'idRegistro',
        'eliminado',
    ];
}
