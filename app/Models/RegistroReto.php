<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroReto extends Model
{
    use HasFactory;
    protected $connection ='appJuegos';
    protected $table='RegistroReto';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'idReto',
        'idRegistro',
        'eliminado',
    ];
}
