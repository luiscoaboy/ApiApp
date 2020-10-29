<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\models\Reto;
use App\models\TipoJuego;
use App\models\Registro;
use App\models\RegistroReto;

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
            //'feedback' => 'required',
            'tiempoSegundos' => 'required',
            'distribucion' => 'required',
            'fechaFin' => 'required',
            //'estudiantes' =>''
        ]);
        if ($Validator->fails()) {
            return response()->json(['Error' => $Validator->errors()], 418);
        }

        // $Estudiantes = str_replace('[', '', $request->estudiantes);
        // $Estudiantes = str_replace(']', '', $Estudiantes);
        // $Alumnos = explode(',',$Estudiantes);

        $Creador=Registro::find($request->idCreador);
        if (!$Creador) {
            return response()->json(['Error' => 'No se ha podido encontrar el creador'], 418);
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
        $InputReto['tiempoSegundos']=$request->tiempoSegundos;
        $InputReto['distribucion']=$request->distribucion;
        $InputReto['fecha_fin']=$request->fechaFin;

        $Reto= Reto::create($InputReto);
        if (!$Reto) {
            return response()->json(['Error' => 'No se ha podido comprobar la creacion del reto'], 400);
        }
        // foreach ($Alumnos as $Alumno) {
        //     $InputRegistroReto['idReto']=$Alumno->idReto;
        //     $InputRegistroReto['idRegistro']=$Alumno->idRegistro;
        //     RegistroReto::create($InputRegistroReto);
        // }
        return response()->json(['Success' => $Reto], 200);

    }

    public function EliminarReto(Request $request)
    {
        
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
                    ->join('Registro','Reto.idCreador','Registro.idRegistro')
                    ->select('Reto.*',
                    //'Reto.id as Estudiantes'
                )->get();
                $Retos=$this->ObtenerEstReto($Retos);
        }
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
        $NoEncontrados='';
        //return response()->json(['Success' => $request->idReto], 200);
        foreach ($Alumnos as $Alumno) {
            ////Buscar por cedula
            $Estudiante=Registro::where('cedula','=',$Alumno)->first();
            if($Estudiante){
                $Reto= Reto::find($request->idReto);
                if($Reto)
                {
                    $RegistroRetoC=RegistroReto::where('idReto','=',$Reto->id)->where('idRegistro','=',$Estudiante->idRegistro)
                        ->first();
                       //return response()->json(['Success' => $RegistroRetoC], 200);
                    if(!$RegistroRetoC){
                        $InputRegistroReto['idReto']=$request->idReto;
                        $InputRegistroReto['idRegistro']=$Estudiante->idRegistro;
                        RegistroReto::create($InputRegistroReto);
                    }
                }
                

                    
            }
            // else{
            //     $NoEncontrados=$Alumno.' '.$NoEncontrados;
            // }
        }
        // if (!$NoEncontrados=='') {
        //     $mensaje['Mensaje']="No se pudieron registrar los siguientes estudiantes";
        //     $mensaje['Estudiantes']=$NoEncontrados;
        //     return response()->json(['Success' => $mensaje], 206);
        // }
        return response()->json(['Success' => 'Se han registrado todos los estudiantes correctamente'], 200);
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
        foreach ($Alumnos as $Alumno) {
            ///validar para que se eliminen los asignados 
            $Estudiante=Registro::find($Alumno);
            if($Estudiante){
                $RegistroReto=RegistroReto::where('idReto','=',$Reto->id)->where('idRegistro','=',$Estudiante->idRegistro)
                        ->first();
                if($RegistroReto){
                $RegistroReto->delete();
                }
            }
        }
        return response()->json(['Success' => 'Se han quitado los elumnos correctamente'], 200);
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
    
}
