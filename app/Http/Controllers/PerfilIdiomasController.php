<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\PerfilIdiomas;
use App\Events\Test;

class PerfilIdiomasController extends Controller
{
    public function Listar()
    {
        event(new Test(PerfilIdiomas::all()));
        return response()->json(['success' => 'No se ha podido encontrar al creador del reto'], 200);
    }
}
