<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\PerfilIdiomas;

class PerfilIdiomasController extends Controller
{
    public function Listar()
    {
        return response()->json(PerfilIdiomas::all());
    }
}
