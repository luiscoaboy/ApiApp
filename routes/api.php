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
// header('Access-Control-Allow-Origin: *');
// header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
////Ruta para probar///No es importante
Route::get('/listar', [PerfilIdiomasController::class, 'Listar']);

//Route::post('/buscarEstu',[RetoController::class, 'BuscarEstudiantes']);
//Permite crear un nuevo registro de reto
Route::post('/crearReto',[RetoController::class, 'CreacionReto']);
//Route::post('/eliminarReto',[RetoController::class, 'EliminarReto']);
//permite modificar un reto
Route::post('/modificarReto',[RetoController::class, 'ModificarReto']);
//Lista los retos en base a un usuario y distribucion
Route::post('/listarRetos',[RetoController::class, 'ListarRetos']);
//Permite asignar un estudiante o estudiantes a un reto
Route::post('/asigEstReto',[RetoController::class, 'AsignarEstudiantesReto']);
//Permite eliminar la asignacion de un estudiante o estudiantes a un reto
Route::post('/quitEstudReto',[RetoController::class, 'QuitarAsignacion']);
//Permite modificar el estado de un reto
Route::post('/modEstdReto',[RetoController::class, 'CambEstdReto']);//Nueva ruta
//Permite crear una pregunta en base a un reto en 
Route::post('/crearPreguntaReto',[RetoController::class, 'RegistrarPreguntasReto']);//Nueva ruta
//Lista las preguntas asignadas a un reto
Route::post('/listarPreguntas',[RetoController::class, 'ListarPreguntas']);
//Lista los retos que un estudiante tiene asignado
Route::post('/listarRetosEstd',[RetoController::class, 'ListarRetosEstudiantes']);
//Elimina un pregunta y sus respectivas respuestas
Route::post('/eliminPreg',[RetoController::class, 'EliminarPregunta']);
//Modica una pregunta y sus respuestas
Route::post('/modPregResp',[RetoController::class, 'ModificarPregunta']);
//Modifica la respuesta correcta de una pregunta 
Route::post('/modRespCorrecta',[RetoController::class, 'CambiarRepuestaPregunta']);
//Guarda las respuesta elegidas de un estudiante
Route::post('/guardRespEstud',[RetoController::class, 'GuardarRespuestaDeEstudiante']);
//Guarda las respuesta de una lista de estudiantes
Route::post('/guardRespEstudts',[RetoController::class, 'GuardarRespuestasDeEstudiantes']);
