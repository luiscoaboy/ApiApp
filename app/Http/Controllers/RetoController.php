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

class RetoController extends Controller
{
    
    public function CreacionReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idCreador' => 'required',
            'idTipoReto' => 'required',
            'descripcion' => 'required',
            'tema' => 'required',
            'puntos' => 'required',
            'tiempoSegundos' => 'required',
            'distribucion' => 'required',
            'fechaFin' => 'required',
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
        $InputReto['puntos']=$request->puntos;
        $InputReto['feedback']='N/A';
        $InputReto['estado']='1';
        $InputReto['tiempoSegundos']=$request->tiempoSegundos;
        $InputReto['distribucion']=$request->distribucion;
        $InputReto['fecha_fin']=$request->fechaFin;

        $Reto= Reto::create($InputReto);
        if (!$Reto) {
            return response()->json(['Error' => 'No se ha podido comprobar la creacion del reto'], 400);
        }
        return response()->json(['Success' => $Reto], 200);

    }

    public function ModificarReto(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idReto'=> 'required',
            //'idCreador' => 'required',
            //'idTipoReto' => 'required',
            'descripcion' => 'required',
            'tema' => 'required',
            'puntos' => 'required',
            'tiempoSegundos' => 'required',
            'distribucion' => 'required',
            'fechaFin' => 'required',
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
        $Reto= Reto::find($request->idReto);
        if($Reto){ 
            //$Reto->idCreador=$request->idCreador;
            //$Reto->idTipoReto=$request->idTipoReto;
            $Reto->tema=$request->tema;
            $Reto->descripcion=$request->descripcion;
            $Reto->puntos=$request->puntos;
            //$Reto['feedback']='N/A';
            //$Reto['estado']='0';
            $Reto->tiempoSegundos=$request->tiempoSegundos;
            $Reto->distribucion=$request->distribucion;
            $Reto->fecha_fin=$request->fechaFin;
            $Reto->save();
            return response()->json(['Success' => $Reto], 200);
        }
        else{
            return response()->json(['Error' => 'No se ha podido comprobar la creacion del reto'], 400);
        }
    }
    ///Crear api para eliminar logicamante un reto
    ///Para reactivar reto
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

    public function ListarRetosEstudiantes(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idRegistro' => 'required',
            'idTipoReto'=>'required',
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
        $Retos=DB::connection('appJuegos')->table('RegistroReto')
            ->where('RegistroReto.idRegistro', $request->idRegistro)
            ->join('Reto','RegistroReto.idReto','Reto.id')
            ->where('Reto.idTipoReto',$request->idTipoReto)
            ->where('Reto.estado',1)   
            ->select('Reto.*',
                //'Reto.id as Estudiantes'
            )->get();
            return response()->json(['Success' => $Retos], 200);
    }

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

    public function BuscarEstudiantes(Request $request)
    {
        $TestInfo=DB::select("exec AdministracionAcademica.tesis.spgetAlumnos :parametro",
        array( 'parametro'=>$request->distribucion ));
        $Tamano=sizeof($TestInfo);
        //$items->where('number', '===', 2);
        // for ($j=0; $j < ; $j++) { 
            
        // }
        //$result=$TestInfo->where("Cedula","===",$request->cedula);
        //$TestInfo=DB::select('exec AdministracionAcademica.tesis.spgetAlumnos(?)',[$request->distribucion])->get();
        //'call store_procedure_function(?)'
        return response()->json(['Success' => $result], 200);
    }

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

    ///Ruta para modificar la repuesta correcta
    ///Ruta para modificar toda la pregunta con respuesta y todo

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
                        $PreguntasReto->save();
                        $RespuestasPregunta=RespuestasPregunta::where('idPregunta',$Pregunta->id)
                        ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
                        ->select('RespuestasPregunta.*')->get();
                        if($RespuestasPregunta!="[]"){
                            foreach($RespuestasPregunta as $RespuestaPregunta){
                                ////////////////Terminar para las preguntas
                            }
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

    public function CambiarRepuestaPregunta(Request $request)
    {
        $Validator = Validator::make($request->all(), [
            'idPregunta' => 'required',
            // 'idRegistro'=>'required',
            // 'descripcion'=>'required'
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }
    }

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
                
                $Respuestas=RespuestasPregunta::where('idPregunta',$Pregunta->id)
                ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
                ->select('Respuestas.*');
                $RespuestasPregunta=RespuestasPregunta::where('idPregunta',$Pregunta->id)
                ->join('Respuestas','RespuestasPregunta.idRespuesta','Respuestas.id')
                ->select('RespuestasPregunta.*');
                // $Tamano=sizeof($RespuestasPregunta);
                // for ($i=0; $i < $Tamano; $i++) { 
                //     //$Preguntas=Preguntas::where('id',$Pregunta->id)->get();
                // }
                // $RespuestasPregunta->delete();
                // $Respuestas->delete();
                // $PreguntasReto->delete();
                // $Pregunta->delete();
                return response()->json(['Success' => 'Se ha eliminado la pregunta y sus respuestas correctamente'], 200);
            }
        }
    }
    ////Ruta para ver cuantas preguntas tiene un reto
}
