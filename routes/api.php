<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerfilIdiomasController;
use App\Http\Controllers\RetoController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/listar', [PerfilIdiomasController::class, 'Listar']);

Route::post('/buscarEstu',[RetoController::class, 'BuscarEstudiantes']);
Route::post('/crearReto',[RetoController::class, 'CreacionReto']);
Route::post('/eliminarReto',[RetoController::class, 'EliminarReto']);
Route::post('/listarRetos',[RetoController::class, 'ListarRetos']);
Route::post('/asigEstReto',[RetoController::class, 'AsignarEstudiantesReto']);
Route::post('/quitEstReto',[RetoController::class, 'QuitarAsignacion']);