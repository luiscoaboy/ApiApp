<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilIdiomas extends Model
{
    use HasFactory;
    protected $connection ='AdministracionAcademica';
    protected $table='perfil.Idiomas';

    protected $primaryKey = 'Id_idiomas';
    protected $fillable = [
        'Id_idiomas','idioma','Eliminado'
    ];
}
