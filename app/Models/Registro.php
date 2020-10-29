<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='Registro';
    public $timestamps = false;
    protected $primaryKey = 'idRegistro';
    protected $fillable = [
        'idRegistro',
        'nombres',
        'cedula',
        'provincia',
        'canton',
        'ciudad',
        'apodo',
        'usuario',
        'clave',
        'idTipo',
        'carrera',
        'avatar',
        'correo',
        'celular',
        'facebook',
        'telefono',
        'idAlumno',
        'token',
    ];
}
