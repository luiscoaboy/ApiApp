<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\models\Reto;
use App\models\TipoJuego;
use App\models\Registro;
use App\models\RegistroReto;
use App\models\Preguntas;
use App\models\PreguntasReto;
use App\models\Respuestas;
use App\models\RespuestasPregunta;
use App\models\RegistroRespuesta;
use App\models\ResultadoReto;

class RetoController extends Controller
{
    ////Permite crear un nuevo reto
    public function CreacionReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idCreador' => 'required',
            'idTipoReto' => 'required',
            'descripcion' => 'required',
            'tema' => 'required',
            //'puntos' => 'required',
            'tiempoMinutos' => 'required',
            'distribucion' => 'required',
            'fechaFin' => 'required',
            'numeroIntentos' => 'required',
            'numeroPreguntas' => 'required',
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Creador=Registro::find($request->idCreador);
        if (!$Creador) {
            return response()->json(['Error' => 'No se ha podido encontrar al creador del reto'], 418);
        }
        $TipoReto=TipoJuego::find($request->idTipoReto);
        if (!$TipoReto) {
            return response()->json(['Error' => 'No se ha podido encontrar el tipo de reto'], 418);
        }

        $InputReto['idCreador']=$request->idCreador;
        $InputReto['idTipoReto']=$request->idTipoReto;
        $InputReto['tema']=$request->tema;
        $InputReto['descripcion']=$request->descripcion;
        //$InputReto['puntos']=$request->puntos;
        $InputReto['feedback']='N/A';
        $InputReto['estado']='1';
        $InputReto['tiempoMinutos']=$request->tiempoMinutos;
        $InputReto['distribucion']=$request->distribucion;
        $InputReto['fecha_fin']=$request->fechaFin;
        $InputReto['numeroIntentos']=$request->numeroIntentos;
        $InputReto['numeroPreguntas']=$request->numeroPreguntas;

        $Reto= Reto::create($InputReto);
        if (!$Reto) {
            return response()->json(['Error' => 'No se ha podido comprobar la creacion del reto'], 400);
        }
        return response()->json(['Success' => $Reto], 200);

    }
    ////Permite modificar un reto en especifico
    public function ModificarReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto'=> 'required',
            //'idCreador' => 'required',
            //'idTipoReto' => 'required',
            'descripcion' => 'required',
            'tema' => 'required',
            //'puntos' => 'required',
            'tiempoMinutos' => 'required',
            'distribucion' => 'required',
            'fechaFin' => 'required',
            'numeroIntentos' => 'required',
            'numeroPreguntas' => 'required',
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Reto= Reto::find($request->idReto);
        if($Reto){ 
            //$Reto->idCreador=$request->idCreador;
            //$Reto->idTipoReto=$request->idTipoReto;
            $Reto->tema=$request->tema;
            $Reto->descripcion=$request->descripcion;
            //$Reto->puntos=$request->puntos;
            //$Reto['feedback']='N/A';
            //$Reto['estado']='0';
            $Reto->tiempoMinutos=$request->tiempoMinutos;
            $Reto->distribucion=$request->distribucion;
            $Reto->fecha_fin=$request->fechaFin;
            $Reto->numeroIntentos=$request->numeroIntentos;
            $Reto->numeroPreguntas=$request->numeroPreguntas;

            $Reto->save();
            return response()->json(['Success' => $Reto], 200);
        }
        else{
            return response()->json(['Error' => 'No se ha podido comprobar la creacion del reto'], 400);
        }
    }
    ///Crear api para eliminar logicamante un reto
    ///Para reactivar reto
    /////Permite modificar el estado actual de un reto
    public function CambEstdReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto' => 'required',
            'estado'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Reto= Reto::find($request->idReto);
        if ($Reto) {
            $Reto->estado=$request->estado;
            //$Reto->estado="1";
            $Reto->save();
        }
        else{
            return response()->json(['Error' => 'No se pudo encontrar reto a modificar el estado'], 418);
        }
        return response()->json(['Success' => 'Se modificó el estado correctamente'], 200);
    }

    ////Funcion que permite listar todos los retos y los estudiantes asignados de una distribucion en especifico
    public function ListarRetos(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idRegistro' => 'required',
            'distribucion'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Registro=Registro::find($request->idRegistro);
        if (!$Registro) {
            return response()->json(['Error' => 'No se pudo encontrar registro'], 418);
        }
        $TipoReto=DB::connection('appJuegos')->table('TipoRegistro')
        ->where('TipoRegistro.IdTipoRegistro', $Registro->idTipo)
        ->select('*')
        ->first(); 
        if($TipoReto->idTipoRegistro==1){
        $Retos=DB::connection('appJuegos')->table('Reto')
                    ->where('Reto.distribucion', $request->distribucion)
                    //->where('Reto.estado','0')
                    ->join('Registro','Reto.idCreador','Registro.idRegistro')
                    ->select('Reto.*',
                            //'Registro.cedula as Estudiantes'
                )->get();
                //return response()->json(['Success' => $this->ObtenerEstReto($Retos)], 200);
                $Retos=$this->ObtenerEstReto($Retos);
        }
        else if($TipoReto->idTipoRegistro==2){
            $Retos=DB::connection('appJuegos')->table('Reto')
                    ->where('Reto.distribucion', $request->distribucion)
                    //->where('Reto.estado','0')
                    ->join('RegistroReto','Reto.id','RegistroReto.idReto')
                    ->join('Registro','RegistroReto.idRegistro','Registro.idRegistro')
                    ->select('Reto.*',
                    //'Reto.id as Estudiantes'
                )->get();
                $Retos=$this->ObtenerEstReto($Retos);
        }
        return response()->json(['Success' => $Retos], 200);
    }

    ////Funcion que permite listar los retos asignados a un estudiante en especifico
    public function ListarRetosEstudiantes(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idRegistro' => 'required',
            'idTipoReto'=>'required',
            //'distribucion'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Registro=Registro::find($request->idRegistro);
        if (!$Registro) {
            return response()->json(['Error' => 'No se pudo encontrar registro'], 418);
        }
        $TipoReto=TipoJuego::find($request->idTipoReto);
        if (!$TipoReto) {
            return response()->json(['Error' => 'Error en base de datos, no se encuentra el tipo de reto'], 418);
        }
        $vacios[]=array();
        ////Recordar para la distribucion si se quiere agrupar
        $Retos=DB::connection('appJuegos')->table('RegistroReto')
            ->where('RegistroReto.idRegistro', $request->idRegistro)
            ->join('Reto','RegistroReto.idReto','Reto.id')
            //->join('PreguntasReto','Reto.id','PreguntasReto.idReto')
            
            ->where('Reto.idTipoReto',$request->idTipoReto)
            ->where('Reto.estado',1)
            //->where('preg','>',0)
            
            //->where('Reto.distribucion',$request->distribucion)   
            ->select('Reto.*')
                //'Reto.id as Estudiantes')
                ->get();
            $Tamano=sizeof($Retos);
            for ($i=0; $i < $Tamano; $i++) { 
                $NumeroIntentos=0;
                $ResultadoReto=ResultadoReto::where('idRegistro',$request->idRegistro)
                ->where('idReto',$Retos[$i]->id)
                ->orderBy('idResultado', 'DESC')->first();
                if($ResultadoReto){
                    $NumeroIntentos=$ResultadoReto->numeroIntentos;
                }

                $Preguntas=DB::table('PreguntasReto')
                ->where('PreguntasReto.idReto', $Retos[$i]->id)
                ->join('Preguntas','PreguntasReto.idPregunta','Preguntas.id')
                ->select('Preguntas.*')
                ->inRandomOrder()->take($Retos[$i]->numeroPreguntas)
                ->get();
                if($Preguntas=='[]'){
                    unset ($Retos[$i]);
                    //
                }else{
                    $Retos[$i]->Preguntas=$this->ObtenerRespuestas($Preguntas);
                    $Retos[$i]->numeroIntentosRestantes=$NumeroIntentos;
                }
                
            }

            // foreach($vacios as $vacio){
                
            // }
            return response()->json(['Success' => $Retos], 200);
    }
    ////Funcion que permite asignar y desasignar estudiantes a un reto en especifico
    public function AsignarEstudiantesReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto' => 'required',
            'estudiantes'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }

        $Estudiantes = str_replace('[', '', $request->estudiantes);
        $Estudiantes = str_replace(']', '', $Estudiantes);
        $Alumnos = explode(',',$Estudiantes);
        $listaAlumnos=array();
        //return response()->json(['Success' => $request->idReto], 200);
        $Reto= Reto::find($request->idReto);
        if(!$Reto)
            {
                return response()->json(['Success' => 'No se hicieron cambios, no existe el reto asignado'], 200);
            }
        $RegistroRetos=RegistroReto::where('idReto','=',$Reto->id)->select('id')->get();
        foreach ($Alumnos as $Alumno) {
            ////Buscar por cedula
            $Estudiante=Registro::where('cedula','=',$Alumno)->first();
            if($Estudiante){
                $RegistroRetoC=RegistroReto::where('idReto','=',$Reto->id)->where('idRegistro','=',$Estudiante->idRegistro)
                    ->select('id')
                    ->first();
                    
                if(!$RegistroRetoC){
                    $InputRegistroReto['idReto']=$request->idReto;
                    $InputRegistroReto['idRegistro']=$Estudiante->idRegistro;
                    $RegistroRetoC=RegistroReto::create($InputRegistroReto);
                    //return response()->json(['Success' => $RegistroRT], 200);
                    // $RegistroRt1->id
                }
                array_push($listaAlumnos,$RegistroRetoC->id);     
            }
        }
        foreach ($RegistroRetos as $RegRetos) {
            $existeLista=in_array($RegRetos->id, $listaAlumnos, true); // true
            if(!$existeLista){
                $RegistroRt1=RegistroReto::find($RegRetos->id);
                $RegistroRt1->delete();
            }
        //return response()->json(['Success' =>$listaAlumnos], 200);
        }
        return response()->json(['Success' => 'Se han registrado los cambios de asignacion correctamente'], 200);
    }

    ////Funcion que permite quitar la asignacion de una lista de estudiantes a un reto en especifico
    public function QuitarAsignacion(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto' => 'required',
            'estudiantes'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Estudiantes = str_replace('[', '', $request->estudiantes);
        $Estudiantes = str_replace(']', '', $Estudiantes);
        $Alumnos = explode(',',$Estudiantes);
        $comprobacion="";
        foreach ($Alumnos as $Alumno) {
            ///validar para que se eliminen los asignados 
            $Estudiante=Registro::where('cedula','=',$Alumno)->first();
            if($Estudiante){
                $comprobacion="registro";
                $Reto= Reto::find($request->idReto);
                if($Reto)
                {
                    $comprobacion="reto";
                    $RegistroReto=RegistroReto::where('idReto','=',$Reto->id)->
                    where('idRegistro','=',$Estudiante->idRegistro)
                            ->first();
                    //return response()->json(['Success' => RegistroReto], 200);
                    if($RegistroReto){
                        $RegistroReto->delete();
                        $comprobacion="registroReto";
                    }
                }
                
            }
        }
        return response()->json(['Success' => 'Se han quitado los estudiantes correctamente'], 200);   
    }

    ////Funcion estatica para ligar los estudiantes asignados a un reto en especifico
    public static function ObtenerEstReto($Retos)
    {
        $Tamano=sizeof($Retos);
        //$Info=$Retos;
        //return response()->json(['success' => $Publicaciones[$Tamano-1]], 200);
        for ($i=0; $i < $Tamano; $i++) { 
            $Autores=DB::table('RegistroReto')
            ->where('RegistroReto.idReto', $Retos[$i]->id)
            ->join('Registro','RegistroReto.idRegistro','Registro.idRegistro')
            ->select('Registro.cedula')
            ->get();

            $Retos[$i]->Estudiantes=$Autores;
        }
        return $Retos;
        //ver si ese reto ya esta asignado o no y listado de estudiantes a quien fue asignado
    }
    ////Función que permite guardar una nueva pregunta y sus respectivas respuestas
    public function RegistrarPreguntasReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto' => 'required',
            'idRegistro'=>'required',
            'descripcion'=>'required',
            'respuestas'=>'',
            'correcta'=>'required|integer'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Respuestas = str_replace('[', '', $request->respuestas);
        $Respuestas = str_replace(']', '', $Respuestas);
        $ListaRepuestas = explode(',',$Respuestas);
        //$listaAlumnos=array();
        $Registro=Registro::find($request->idRegistro);
        if (!$Registro) {
            return response()->json(['Error' => 'No se ha podido localizar el registro'],418);
        }
        if ($Registro->idTipo==1) {
            //$InputPreguntas['idReto']=$request->idReto;
            $InputPreguntas['idRegistro']=$request->idRegistro;
            $InputPreguntas['descripcion']=$request->descripcion;
            $Preguntas=Preguntas::create($InputPreguntas);
            if($Preguntas){ 
                $InputPreguntasReto['idPregunta']=$Preguntas->id;
                $InputPreguntasReto['idReto']=$request->idReto;
                $PreguntasReto=PreguntasReto::create($InputPreguntasReto);
                if ($PreguntasReto) {
                    $Tamano=sizeof($ListaRepuestas);
                    for ($i=0; $i < $Tamano; $i++){
                        $InputRespuesta['descripcion']=$ListaRepuestas[$i];
                        if($i==$request->correcta){
                            $InputRespuesta['esCorrecta']='1';
                        }else{
                            $InputRespuesta['esCorrecta']='0';
                        }
                        $ObjRespuesta=Respuestas::create($InputRespuesta);
                        if($ObjRespuesta){
                            $InputRespuestasPregunta['idRespuesta']=$ObjRespuesta->id;
                            $InputRespuestasPregunta['idPregunta']=$Preguntas->id;
                            $PreguntaRespuesta=RespuestasPregunta::create($InputRespuestasPregunta);
                        }
                    }
                    // $InputRespuesta
                    return response()->json(['Success' => 'Se ha creado la pregunta con sus respuestas correctamente'],200);
                }
                else{
                    return response()->json(['Error' => 'No se pudo completar el registro'],418);
                }
            }
            else{
                return response()->json(['Error' => 'No se pudo crear la pregunta'],418);
            }
        }
        else{
            return response()->json(['Error' => 'No autorizado'],401);
        }

    }

    // public function RegistrarRespuestaPregunta(Request $request)
    // {
        
    // }
    ///Lista todas las preguntas de un reto en especifico
    public function ListarPreguntas(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto' => 'required',
            // 'idRegistro'=>'required',
            // 'descripcion'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $PreguntasConRespuestas=DB::connection('appJuegos')->table('PreguntasReto')
        ->where('PreguntasReto.idReto', $request->idReto)
        ->join('Preguntas','PreguntasReto.idPregunta','Preguntas.id')
        ->select('Preguntas.*')
        ->get();
        $PreguntasConRespuestas=$this->ObtenerRespuestas($PreguntasConRespuestas);

        return response()->json(['Success' => $PreguntasConRespuestas], 200);

    }

    ////Funcion estatica para ligar las respuestas con su respetiva pregunta
    public static function ObtenerRespuestas($PreguntasConRespuestas)
    {
        $Tamano=sizeof($PreguntasConRespuestas);
        //$Info=$Retos;
        //return response()->json(['success' => $Publicaciones[$Tamano-1]], 200);
        for ($i=0; $i < $Tamano; $i++) { 
            $Respuestas=DB::table('RespuestasPregunta')
            ->where('RespuestasPregunta.idPregunta', $PreguntasConRespuestas[$i]->id)
            ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
            ->select('Respuestas.*')
            ->get();

            $PreguntasConRespuestas[$i]->Respuestas=$Respuestas;
        }
        return $PreguntasConRespuestas;
    }

    
    ////Función que permite modificar una pregunta y sus respectivas respuestas
    public function ModificarPregunta(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idPregunta'=>'',
            'idReto' => 'required',
            'idRegistro'=>'required',
            'descripcion'=>'required',
            'respuestas'=>'',
            'correcta'=>'required|integer'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Respuestas = str_replace('[', '', $request->respuestas);
        $Respuestas = str_replace(']', '', $Respuestas);
        $ListaRepuestas = explode(',',$Respuestas);

        $Registro=Registro::find($request->idRegistro);
        if (!$Registro) {
            return response()->json(['Error' => 'No se ha podido localizar el registro'],418);
        }
        if ($Registro->idTipo==1) {
            $Pregunta=Preguntas::find($request->idPregunta);
            if($Pregunta){
                $Pregunta->descripcion=$request->descripcion;
                $Pregunta->idRegistro=$request->idRegistro;
                $Pregunta->save();
                $Reto= Reto::find($request->idReto);
                if($Reto){
                    $PreguntasReto=PreguntasReto::where('idPregunta',$Pregunta->id)->first();
                    if($PreguntasReto){
                        $PreguntasReto->idReto=$request->idReto;
                        $RespuestasPregunta=RespuestasPregunta::where('idPregunta',$Pregunta->id)
                        ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
                        ->select('RespuestasPregunta.*')->get();
                        if($RespuestasPregunta!="[]"){
                            $Tamano=sizeof($RespuestasPregunta);
                            for ($i=0; $i < $Tamano; $i++) { 
                                $Respuesta=Respuestas::find($RespuestasPregunta[$i]->idRespuesta);
                                $Respuesta->descripcion=$ListaRepuestas[$i];
                                if($i==$request->correcta){
                                    $Respuesta->esCorrecta='1';
                                }
                                else{
                                    $Respuesta->esCorrecta='0';
                                }
                                $PreguntasReto->save();
                                $Respuesta->save();
                            }
                            return response()->json(['Success' => 'Modificaciones en pregunta realizadas correctamente'],200);
                        }
                        else{
                            return response()->json(['Error' => 'consulta de respuestas'],418);
                        }
                        
                    }
                    else{
                        return response()->json(['Error' => 'No se pudo encontrar la relacion entre pregunta y reto a modificar'],418);
                    }
                }
                else{
                    return response()->json(['Error' => 'No se pudo encontrar el reto que se pretende asignar a la pregunta'],418);
                }
            }
            else{
                return response()->json(['Error' => 'No se pudo encontrar la pregunta'],418);
            }
        }
        else{
            return response()->json(['Error' => 'No autorizado'],401);
        }
    }

    ////Funcion que cambia la respuesta correcta de una pregunta en especifica
    public function CambiarRepuestaPregunta(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idPregunta' => 'required',
            'idRespuesta'=>'required',
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Pregunta=Preguntas::find($request->idPregunta);
        if($Pregunta){
            $RespuestasPregunta=RespuestasPregunta::where('idPregunta',$Pregunta->id)
                ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
                ->select('RespuestasPregunta.*')->get();
                if($RespuestasPregunta!="[]"){
                    foreach($RespuestasPregunta as $RespuestaPregunta){
                        $Respuesta=Respuestas::find($RespuestaPregunta->idRespuesta);
                        if($Respuesta->id==$request->idRespuesta){
                            $Respuesta->esCorrecta='1';
                        }
                        else{
                            $Respuesta->esCorrecta='0';
                        }
                        $Respuesta->save();
                    }
                    $Respuestas=DB::table('RespuestasPregunta')
                        ->where('RespuestasPregunta.idPregunta', $Pregunta->id)
                        ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
                        ->select('Respuestas.*')
                        ->get();
                    $Pregunta->Respuestas=$Respuestas;
                    return response()->json(['Success' => $Pregunta], 200);
                    //return response()->json(['Success' => 'Se ha modificado correctamente la respuesta de esa pregunta'], 200);
                }
                else{
                    return response()->json(['Error' => 'No se pudieron encontrar las respuestas de la pregunta'], 418);
                }
        }
        else{
            return response()->json(['Error' => 'No se ha podido encontrar la pregunta a cambiar la respuesta'], 418);
        }

    }

    ////Funcion que elimina una pregunta
    public function EliminarPregunta(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idPregunta' => 'required',
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Pregunta=Preguntas::find($request->idPregunta);
        if($Pregunta){
            $PreguntasReto=PreguntasReto::where('idPregunta',$Pregunta->id)->first();
            if($PreguntasReto){
                $RespuestasPregunta=RespuestasPregunta::where('idPregunta',$Pregunta->id)
                ->select('RespuestasPregunta.*')->get();
                foreach ($RespuestasPregunta as $key=>$RespuestaPregunta) {
                    $RespPregunta=RespuestasPregunta::find($RespuestaPregunta->id);
                    if($RespPregunta){
                        $Respuesta=Respuestas::find($RespuestaPregunta->idRespuesta);
                        if($Respuesta){
                            $RespPregunta->delete();
                            $Respuesta->delete();
                        }
                    }
                    
                }
                $PreguntasReto->delete();
                $Pregunta->delete();
                return response()->json(['Success' => 'Se ha eliminado la pregunta y sus respuestas correctamente'], 200);
            }
            else{
                return response()->json(['Error' => 'No se pudo relacionar la pregunta y el reto'], 418);
            }
        }
        else{
            return response()->json(['Error' => 'No se pudo encontrar la pregunta a eliminar'], 418);
        }
    }
    ////Ruta para ver las preguntas y respuestas de todos los Retos
    ////No usada
    public function ObtenerPreRespReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idRegistro' => 'required',
            'distribucion'=>'required',
            'idTipoReto'=> 'required',
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Retos=DB::connection('appJuegos')->table('Reto')
            ->where('Reto.distribucion', $request->distribucion)
            ->where('Reto.estado','1')
            ->where()
            ->join('RegistroReto','Reto.id','RegistroReto.idReto')
            ->where('RegistroReto.idRegistro',$request->idRegistro)
            ->select('Reto.*',
                    //'Reto.id as Estudiantes'
            )->get();
            $Retos=$this->ObtenerEstReto($Retos);
        //return response()->json(['Success' => $this->ObtenerEstReto($Retos)], 200);
        
        //$Info=$Retos;
        //return response()->json(['success' => $Publicaciones[$Tamano-1]], 200);
        
        //return $Retos;
    }

    public function GuardarRespuestaDeEstudiante(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idRegistro' => 'required',
            'idPregunta'=>'required',
            'idRespuesta'=> 'required',
            'esCorrecta'=> 'required',
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        $Registro=Registro::find($request->idRegistro);
        if(!$Registro){
            return response()->json(['Error' => 'No se pudo encontrar al estudiante'], 418);
        }
        $Pregunta=Preguntas::find($request->idPregunta);
        if(!$Pregunta){
            return response()->json(['Error' => 'No se pudo encontrar la pregunta a guardar'], 418);
        }
        $Respuesta=Respuestas::find($request->idRespuesta);
        if(!$Respuesta){
            return response()->json(['Error' => 'No se pudo encontrar la respuesta a guardar'], 418);
        }
        $RegistroRespuesta=RegistroRespuesta::where('idPregunta',$request->idPregunta)->first();
        if($RegistroRespuesta){
            return response()->json(['Error' => 'No se puede responder a la misma pregunta dos veces'], 418);
        }
        $InputRespuestas['idRegistro']=$request->idRegistro;
        $InputRespuestas['idPregunta']=$request->idPregunta;
        $InputRespuestas['idRespuesta']=$request->idRespuesta;
        $InputRespuestas['esCorrecta']=$request->esCorrecta;
        $Respuestas=RegistroRespuesta::create($InputRespuestas);
        if($Respuestas){
            return response()->json(['Success' => 'Respuestas almacenadas correctamente'], 200);
        }
        else{
            return response()->json(['Error' => 'Ocurrió un error al almacenar las respuestas'], 418);
        }

    }

    public function GuardarRespuestasDeEstudiantes(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto'=>'required',
            'idRegistro' => 'required',
            // 'preguntas'=>'required',
            // 'respuestas'=> 'required',
            // 'correctas'=> 'required',
            'tiempo'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
        //return response()->json(['Success' => $request->all()], 200);
        $NumeroIntento=0;
        //return response()->json($Respuestas);
        $Reto= Reto::find($request->idReto);
        if(!$Reto){
            return response()->json(['Error' => 'No se pudo encontrar el reto'], 418);
        }
        $ResultadoReto=ResultadoReto::where('idRegistro',$request->idRegistro)
        ->where('idReto',$request->idReto)
        ->orderBy('idResultado', 'DESC')->first();
        if($ResultadoReto){
            if($ResultadoReto->numeroIntentos>=$Reto->numeroIntentos){
                return response()->json(['Error' => 'El estudiante ya ha hecho el máxmimo de intentos permitidos'], 418);
            }
            $NumeroIntento=$ResultadoReto->numeroIntentos;
        }
        $PuntosBase=100;
        $PuntosBonus=50;
        $PuntosFinales=0;
        $Tiempo=(intval($Reto->tiempoMinutos))*60;
        $TiempoTomado=$request->tiempo;
        $PuntosPorTiempo=$PuntosBonus/$Tiempo;
        $PuntosPorPregunta=$PuntosBase/$Reto->numeroPreguntas;

        $Registro=Registro::find($request->idRegistro);
        if(!$Registro){
            return response()->json(['Error' => 'No se pudo encontrar al estudiante'], 418);
        }
        if($request->preguntas==""||$request->respuestas==""||$request->correctas==""){

        }else{
            $Preguntas = explode(',',$request->preguntas);
            $Respuestas = explode(',',$request->respuestas);
            $Rcorrectas = explode(',',$request->correctas);
            $Tamano=0;
            if(sizeof($Preguntas)==sizeof($Respuestas)&&sizeof($Preguntas)==sizeof($Rcorrectas)){
                $Tamano=sizeof($Preguntas);
                for($i=0; $i < $Tamano; $i++) { 
                    $InputRespuestas['idRegistro']=$request->idRegistro;
                    $InputRespuestas['idPregunta']=$Preguntas[$i];
                    $InputRespuestas['idRespuesta']=$Respuestas[$i];
                    $InputRespuestas['esCorrecta']=$Rcorrectas[$i];
                    RegistroRespuesta::create($InputRespuestas);
                    if($InputRespuestas['esCorrecta']==1){
                        $PuntosFinales+=$PuntosPorPregunta;
                    }
                }
            }
            else{
                return response()->json(['Error' => 'Las listas no coinciden en tamaño'], 418);
            }
        }
        
        ////validar si llegan vacios las listas de preguntas, respuestas, retos
        $PuntosFinales+=($PuntosBonus-($PuntosPorTiempo*$TiempoTomado));

        //return response()->json(['Success' => $PuntosFinales],200);


        $InputResultados['idReto']=$request->idReto;
        $InputResultados['idRegistro']=$request->idRegistro;
        $InputResultados['puntaje']=$PuntosFinales;
        $InputResultados['tiempo']=$TiempoTomado/60;
        $InputResultados['numeroIntentos']=$NumeroIntento+1;
        $ResultadoReto=ResultadoReto::create($InputResultados);

        
        ///Crear podium, almacenar y revisar 
        return response()->json(['Success' => 'Se han almacenado '.$Tamano.' respuestas correctamente'], 200);
        
    }


    public static function CalcularPuntaje(Type $var = null)
    {
        # code...
    }
}
