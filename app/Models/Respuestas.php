<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuestas extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='Respuestas';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'descripcion',
        'esCorrecta',
        'eliminado',
    ];
}
