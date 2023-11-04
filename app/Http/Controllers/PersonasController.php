<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Datatables;
use DateTime;
use Image;
use Auth;
use App\User;
use App\Persona;
use App\Recinto;
use Carbon\Carbon;

class PersonasController extends Controller
{
    public function form_nueva_persona(){
        return view("formularios.form_nueva_persona");
    }


    public function nueva_persona(Request $request){
        //Verificamos que no exista un registro con la ci dada
        $persona_existe = \DB::table('personas')
        ->where('cedula_identidad', $request->input("cedula"))
        ->value('id_persona');

        //Si ya existe enviamos el error
        if($persona_existe != ""){
          $mensaje_error = "La persona no se pudo registrar debido a que ya existe un registro con cédula de identidad ".$request->input("cedula");
          return $this->devolver_listado_personas("error", $mensaje_error);
        }
        else {
          $persona=new Persona;

          $persona->nombre=ucwords(strtolower($request->input("nombres")));
          $persona->paterno=ucwords(strtolower($request->input("paterno")));
          $persona->materno=ucwords(strtolower($request->input("materno")));
          $persona->cedula_identidad=$request->input("cedula");
          $persona->complemento_cedula=strtoupper($request->input("complemento"));
          $persona->expedido=strtoupper($request->input("expedido"));
          $persona->fecha_nacimiento=$request->input("nacimiento");
          $persona->telefono_celular=$request->input("telefono");
          $persona->telefono_referencia="0";
          // $persona->direccion=ucwords(strtolower($request->input("direccion")));
          $persona->direccion="";
          $persona->email="";
          $persona->fecha_registro=date('Y-m-d');
          $persona->activo=1;
          $persona->id_responsable_registro=Auth::user()->id;
        }

        //Si se puede registrar, enviamos el mensaje correspondiente
        if($persona->save())
        {
          return $this->devolver_listado_personas("exito", "Persona registrada exitosamente.");
        }
        else {
          return $this->devolver_listado_personas("error", "La persona no pudo ser registrada.");
        }
    }

    public function listado_personas(){
        $id_usuario = Auth::user()->id;
        $rol = \DB::table('role_user')
        ->join('roles', 'role_user.role_id', 'roles.id')
        ->where('user_id', $id_usuario)
        ->first();

        return view("listados.listado_personas")
        ->with('rol', $rol);
    }

    //Función para ser usadas por otras funciones, para devlver la vista listado_personas con algun mensaje
    private function devolver_listado_personas($mensaje_tipo, $mensaje){
        $id_usuario = Auth::user()->id;
        $rol = \DB::table('role_user')
        ->join('roles', 'role_user.role_id', 'roles.id')
        ->where('user_id', $id_usuario)
        ->first();

        if ($mensaje_tipo == "exito") {
          return view("listados.listado_personas")
          ->with('rol', $rol)
          ->with('mensaje_exito', $mensaje);
        }
        else {
          return view("listados.listado_personas")
          ->with('rol', $rol)
          ->with('mensaje_error', $mensaje);
        }
    }

    public function buscar_persona(){
        return Datatables::of(Persona::select('*',
        \DB::raw('(SELECT DATE_FORMAT(fecha_nacimiento, "%d/%m/%Y" )) as fecha_nacimiento'),
        \DB::raw('CONCAT(personas.paterno," ",personas.materno," ",personas.nombre) as nombre_completo'),
        \DB::raw('CONCAT(personas.cedula_identidad," ", personas.complemento_cedula," - ", personas.expedido) as cedula_identidad')
        )
        //->orderBy('id_persona', 'DESC')
        ->where('id_persona', '!=', 1)
        ->get())->make(true);
    }

    public function form_editar_mis_datos_persona(){
        $persona = Persona::find(Auth::user()->id_persona);
        return view("formularios.form_editar_mis_datos_persona")
              ->with('persona', $persona);
    }

    public function editar_mis_datos_persona(Request $request){
      $id_persona = Auth::user()->id_persona;
      $persona = Persona::find($id_persona);

      //Verificamos que no exista un registro con la ci dada
      if ($request->input("cedula") != $persona->cedula_identidad || $request->input("complemento") != $persona->complemento_cedula || $request->input("expedido") != $persona->expedido) {
          $cedulas = \DB::table('personas')
          ->select('cedula_identidad')
          ->where('cedula_identidad', $request->input("cedula"))
          ->where('complemento_cedula', $request->input("complemento"))
          ->where('expedido', $request->input("expedido"))
          ->distinct()
          ->get();
      }else{
          $cedulas = [];
      }

      //Si ya existe enviamos el error
      if (count($cedulas) > 0) {
        $mensaje_error = "Los datos de la persona no se pudieron editar debido a que ya existe un registro con cédula de identidad ".$request->input("cedula");
        return $this->devolver_listado_personas("error", $mensaje_error);
      }else{
          $persona->nombre=ucwords(strtolower($request->input("nombres")));
          $persona->paterno=ucwords(strtolower($request->input("paterno")));
          $persona->materno=ucwords(strtolower($request->input("materno")));
          $persona->cedula_identidad=$request->input("cedula");
          $persona->complemento_cedula=strtoupper($request->input("complemento"));
          $persona->expedido=$request->input("expedido");
          $persona->fecha_nacimiento=$request->input("nacimiento");
          $persona->telefono_celular=$request->input("telefono");
          $persona->id_responsable_registro=Auth::user()->id;

          //Si se puede registrar, enviamos el mensaje correspondiente
          if($persona->save())
          {
              //Tomamos la fecha actual
              $date = new Carbon();
              $hoy = Carbon::now();
              //Tomamos el primer y ultimo dia del mes actual
              $primer_dia_del_mes = date("Y-m-d", strtotime("01-".strftime("%m-%Y", strtotime($hoy))));
              $ultimo_dia_del_mes = date("Y-m-t", strtotime("01-".strftime("%m-%Y", strtotime($hoy))));
              //Tomamos el mes actual y el anterior
              $meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
              //Tomamos el mes numeral, quitando el 0 si corresponde (devuelve enero->01)
              if (strftime("%m", strtotime($hoy))[0] == "0") {$mes_numeral = substr(strftime("%m", strtotime($hoy)), 1);}
              else {$mes_numeral = strftime("%m", strtotime($hoy));}
              $mes_actual = $meses[$mes_numeral];
              $mes_anterior_numeral = date('m', strtotime('-1 month')) ;
              if ($mes_anterior_numeral[0] == "0") {$mes_anterior_numeral = substr($mes_anterior_numeral, 1);}
              $mes_anterior = $meses[$mes_anterior_numeral];

              //Obtenemos el la suma de cobros hasta el mes anterior
              $cobros_anteriores = \DB::table('cobros')
                              ->join('cobros_avisos_conceptos', 'cobros_avisos_conceptos.id_cobro_aviso', '=', 'cobros.id_cobro_aviso')
                              ->where('transaccion_fecha', '<', $primer_dia_del_mes)
                              ->where('cobros.activo',1)
                              ->where('cobros_avisos_conceptos.activo',1)
                              ->sum('cobros_avisos_conceptos.monto');

              //Obtenemos el la suma de ingresos hasta el mes anterior
              $ingresos_anteriores = \DB::table('ingresos')
                              ->where('fecha', '<', $primer_dia_del_mes)
                              ->where('activo',1)
                              ->sum('monto');

              //Obtenemos el la suma de gastos hasta el mes anterior
              $gastos_anteriores = \DB::table('gastos')
                              ->where('fecha', '<', $primer_dia_del_mes)
                              ->where('activo',1)
                              ->sum('monto');

              //Obtenemos el saldo inicial
              $saldo_inicial = \DB::table('saldos_iniciales')
                              ->where('activo',1)
                              ->value('monto');

              //Obtenemos el valor de saldo hasta el mes anterior
              $saldo_hasta_mes_anterior = $saldo_inicial + $cobros_anteriores + $ingresos_anteriores - $gastos_anteriores;

              //Obtenemos el la suma de cobros del mes actual
              $cobros_actuales = \DB::table('cobros')
                              ->join('cobros_avisos_conceptos', 'cobros_avisos_conceptos.id_cobro_aviso', '=', 'cobros.id_cobro_aviso')
                              ->where('transaccion_fecha', '>=', $primer_dia_del_mes)
                              ->where('transaccion_fecha', '<=', $ultimo_dia_del_mes)
                              ->where('cobros.activo',1)
                              ->where('cobros_avisos_conceptos.activo',1)
                              ->sum('cobros_avisos_conceptos.monto');

              //Obtenemos el la suma de ingresos del mes actual
              $ingresos_actuales = \DB::table('ingresos')
                              ->where('fecha', '>=', $primer_dia_del_mes)
                              ->where('fecha', '<=', $ultimo_dia_del_mes)
                              ->where('activo',1)
                              ->sum('monto');

              //Obtenemos el la suma de gastos hasta el mes actual
              $gastos_actuales = \DB::table('gastos')
                              ->where('fecha', '>=', $primer_dia_del_mes)
                              ->where('fecha', '<=', $ultimo_dia_del_mes)
                              ->where('activo',1)
                              ->sum('monto');

              //Obtenemos el saldo actual
              $saldo_actual = $saldo_hasta_mes_anterior + $cobros_actuales + $ingresos_actuales - $gastos_actuales;

              //Tomamos los avisos de cobranza sin pagar
              $nro_cobros_avisos_sin_pagar = \DB::table('cobros_avisos')
                              ->where('id_persona_responsable_pago', Auth::user()->id_persona)
                              ->where('pagado',0)
                              ->where('activo',1)
                              ->count('id_cobro_aviso');

              //Tomamos el monto de dinero adeudado
              $total_sin_pagar = \DB::table('cobros_avisos')
                              ->join('cobros_avisos_conceptos', 'cobros_avisos.id_cobro_aviso', '=', 'cobros_avisos_conceptos.id_cobro_aviso')
                              ->where('cobros_avisos.id_persona_responsable_pago', Auth::user()->id_persona)
                              ->where('cobros_avisos.pagado',0)
                              ->where('cobros_avisos.activo',1)
                              ->where('cobros_avisos_conceptos.activo',1)
                              ->sum('monto');

              return view('home')
                ->with("mes_actual", $mes_actual)
                ->with("mes_anterior", $mes_anterior)
                ->with("saldo_hasta_mes_anterior", $saldo_hasta_mes_anterior)
                ->with("cobros_actuales", $cobros_actuales)
                ->with("ingresos_actuales", $ingresos_actuales)
                ->with("gastos_actuales", $gastos_actuales)
                ->with("saldo_actual", $saldo_actual)
                ->with("nro_cobros_avisos_sin_pagar", $nro_cobros_avisos_sin_pagar)
                ->with("total_sin_pagar", $total_sin_pagar)
                ->with("mensaje_exito", "Datos editados exitosamente.");
          }
          else {
            //Tomamos la fecha actual
            $date = new Carbon();
            $hoy = Carbon::now();
            //Tomamos el primer y ultimo dia del mes actual
            $primer_dia_del_mes = date("Y-m-d", strtotime("01-".strftime("%m-%Y", strtotime($hoy))));
            $ultimo_dia_del_mes = date("Y-m-t", strtotime("01-".strftime("%m-%Y", strtotime($hoy))));
            //Tomamos el mes actual y el anterior
            $meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            //Tomamos el mes numeral, quitando el 0 si corresponde (devuelve enero->01)
            if (strftime("%m", strtotime($hoy))[0] == "0") {$mes_numeral = substr(strftime("%m", strtotime($hoy)), 1);}
            else {$mes_numeral = strftime("%m", strtotime($hoy));}
            $mes_actual = $meses[$mes_numeral];
            $mes_anterior_numeral = date('m', strtotime('-1 month')) ;
            if ($mes_anterior_numeral[0] == "0") {$mes_anterior_numeral = substr($mes_anterior_numeral, 1);}
            $mes_anterior = $meses[$mes_anterior_numeral];

            //Obtenemos el la suma de cobros hasta el mes anterior
            $cobros_anteriores = \DB::table('cobros')
                            ->join('cobros_avisos_conceptos', 'cobros_avisos_conceptos.id_cobro_aviso', '=', 'cobros.id_cobro_aviso')
                            ->where('transaccion_fecha', '<', $primer_dia_del_mes)
                            ->where('cobros.activo',1)
                            ->where('cobros_avisos_conceptos.activo',1)
                            ->sum('cobros_avisos_conceptos.monto');

            //Obtenemos el la suma de ingresos hasta el mes anterior
            $ingresos_anteriores = \DB::table('ingresos')
                            ->where('fecha', '<', $primer_dia_del_mes)
                            ->where('activo',1)
                            ->sum('monto');

            //Obtenemos el la suma de gastos hasta el mes anterior
            $gastos_anteriores = \DB::table('gastos')
                            ->where('fecha', '<', $primer_dia_del_mes)
                            ->where('activo',1)
                            ->sum('monto');

            //Obtenemos el saldo inicial
            $saldo_inicial = \DB::table('saldos_iniciales')
                            ->where('activo',1)
                            ->value('monto');

            //Obtenemos el valor de saldo hasta el mes anterior
            $saldo_hasta_mes_anterior = $saldo_inicial + $cobros_anteriores + $ingresos_anteriores - $gastos_anteriores;

            //Obtenemos el la suma de cobros del mes actual
            $cobros_actuales = \DB::table('cobros')
                            ->join('cobros_avisos_conceptos', 'cobros_avisos_conceptos.id_cobro_aviso', '=', 'cobros.id_cobro_aviso')
                            ->where('transaccion_fecha', '>=', $primer_dia_del_mes)
                            ->where('transaccion_fecha', '<=', $ultimo_dia_del_mes)
                            ->where('cobros.activo',1)
                            ->where('cobros_avisos_conceptos.activo',1)
                            ->sum('cobros_avisos_conceptos.monto');

            //Obtenemos el la suma de ingresos del mes actual
            $ingresos_actuales = \DB::table('ingresos')
                            ->where('fecha', '>=', $primer_dia_del_mes)
                            ->where('fecha', '<=', $ultimo_dia_del_mes)
                            ->where('activo',1)
                            ->sum('monto');

            //Obtenemos el la suma de gastos hasta el mes actual
            $gastos_actuales = \DB::table('gastos')
                            ->where('fecha', '>=', $primer_dia_del_mes)
                            ->where('fecha', '<=', $ultimo_dia_del_mes)
                            ->where('activo',1)
                            ->sum('monto');

            //Obtenemos el saldo actual
            $saldo_actual = $saldo_hasta_mes_anterior + $cobros_actuales + $ingresos_actuales - $gastos_actuales;

            //Tomamos los avisos de cobranza sin pagar
            $nro_cobros_avisos_sin_pagar = \DB::table('cobros_avisos')
                            ->where('id_persona_responsable_pago', Auth::user()->id_persona)
                            ->where('pagado',0)
                            ->where('activo',1)
                            ->count('id_cobro_aviso');

            //Tomamos el monto de dinero adeudado
            $total_sin_pagar = \DB::table('cobros_avisos')
                            ->join('cobros_avisos_conceptos', 'cobros_avisos.id_cobro_aviso', '=', 'cobros_avisos_conceptos.id_cobro_aviso')
                            ->where('cobros_avisos.id_persona_responsable_pago', Auth::user()->id_persona)
                            ->where('cobros_avisos.pagado',0)
                            ->where('cobros_avisos.activo',1)
                            ->where('cobros_avisos_conceptos.activo',1)
                            ->sum('monto');

            return view('home')
              ->with("mes_actual", $mes_actual)
              ->with("mes_anterior", $mes_anterior)
              ->with("saldo_hasta_mes_anterior", $saldo_hasta_mes_anterior)
              ->with("cobros_actuales", $cobros_actuales)
              ->with("ingresos_actuales", $ingresos_actuales)
              ->with("gastos_actuales", $gastos_actuales)
              ->with("saldo_actual", $saldo_actual)
              ->with("nro_cobros_avisos_sin_pagar", $nro_cobros_avisos_sin_pagar)
              ->with("total_sin_pagar", $total_sin_pagar)
              ->with("mensaje_error", "Error al modificar los datos.");
          }
      }
    }

    public function form_editar_persona($id_persona){
        $persona = Persona::find($id_persona);

        return view("formularios.form_editar_persona")
        ->with('persona', $persona);
    }

    public function editar_persona(Request $request){
      $id_persona = $request->input("id_persona");
      $persona = Persona::find($id_persona);

      //Verificamos que no exista un registro con la ci dada
      if ($request->input("cedula") != $persona->cedula_identidad || $request->input("complemento") != $persona->complemento_cedula || $request->input("expedido") != $persona->expedido) {
          $cedulas = \DB::table('personas')
          ->select('cedula_identidad')
          ->where('cedula_identidad', $request->input("cedula"))
          ->where('complemento_cedula', $request->input("complemento"))
          ->where('expedido', $request->input("expedido"))
          ->distinct()
          ->get();
      }else{
          $cedulas = [];
      }

      //Si ya existe enviamos el error
      if (count($cedulas) > 0) {
        $mensaje_error = "Los datos de la persona no se pudieron editar debido a que ya existe un registro con cédula de identidad ".$request->input("cedula");
        return $this->devolver_listado_personas("error", $mensaje_error);
      }else{
          $persona->nombre=ucwords(strtolower($request->input("nombres")));
          $persona->paterno=ucwords(strtolower($request->input("paterno")));
          $persona->materno=ucwords(strtolower($request->input("materno")));
          $persona->cedula_identidad=$request->input("cedula");
          $persona->complemento_cedula=strtoupper($request->input("complemento"));
          $persona->expedido=$request->input("expedido");
          $persona->fecha_nacimiento=$request->input("nacimiento");
          $persona->telefono_celular=$request->input("telefono");
          $persona->id_responsable_registro=Auth::user()->id;

          //Si se puede registrar, enviamos el mensaje correspondiente
          if($persona->save())
          {
            return $this->devolver_listado_personas("exito", "Datos editados exitosamente.");
          }
          else {
            return $this->devolver_listado_personas("error", "Los datos de la persona no pudieron ser modificados.");
          }
      }
    }


    public function form_baja_persona($id_persona){
        /*if(\Auth::user()->isRole('admin')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }*/
        //carga el formulario para agregar un nueva persona

        $persona = Persona::find($id_persona);

        return view("formularios.form_baja_persona")
        ->with('persona', $persona);
    }

    public function baja_persona(Request $request){
        $id_persona = $request->input("id_persona");
        $persona = Persona::find($id_persona);
        $persona->activo = 0;

        if ($persona->save()) {
            return "ok";
        } else {
            return "failed";
        }
    }

    public function form_alta_persona($id_persona){
        $persona = Persona::find($id_persona);

        return view("confirmaciones.form_alta_persona")
        ->with('persona', $persona);
    }

    public function alta_persona(Request $request){
        /*if(\Auth::user()->isRole('admin')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }*/
        $id_persona = $request->input("id_persona");
        $persona = Persona::find($id_persona);
        $persona->activo = 1;

        if ($persona->save()) {
            return "ok";
        } else {
            return "failed";
        }
    }

    public function consultaPersonaRegistradaCi($ci){
        $personas = \DB::table('personas')
        ->select('*',
                 \DB::raw('CONCAT(personas.paterno," ",personas.materno," ",personas.nombre) as nombre_completo'),
                 \DB::raw('CONCAT(personas.cedula_identidad," ", personas.complemento_cedula," - ", personas.expedido) as cedula_identidad')
        )
        ->where('cedula_identidad', 'LIKE', "%$ci%")
        ->where('id_persona', '!=', 1)
        ->orderBy('fecha_registro', 'desc')
        ->orderBy('id_persona', 'desc')
        ->get();

        return $personas;
    }


    public function consultaPersonaRegistradaNombre($nombre){
        $personas = \DB::table('personas')
        ->select('*',
                 \DB::raw('CONCAT(personas.paterno," ",personas.materno," ",personas.nombre) as nombre_completo'),
                 \DB::raw('CONCAT(personas.cedula_identidad," ", personas.complemento_cedula," - ", personas.expedido) as cedula_identidad')
        )
        ->where('nombre', 'LIKE', "%$nombre%")
        ->where('id_persona', '!=', 1)
        ->orwhere('paterno', 'LIKE', "%$nombre%")
        ->where('id_persona', '!=', 1)
        ->orwhere('materno', 'LIKE', "%$nombre%")
        ->where('id_persona', '!=', 1)
        ->orderBy('fecha_registro', 'desc')
        ->orderBy('id_persona', 'desc')
        ->get();

        return $personas;
    }






























/*


    public function form_agregar_persona(){
        //carga el formulario para agregar un nueva persona

        if(\Auth::user()->isRole('registrador')==false && \Auth::user()->isRole('admin')==false && \Auth::user()->isRole('responsable_circunscripcion')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }

        $circunscripciones = \DB::table('recintos')
        ->select('circunscripcion')
        ->distinct()
        ->orderBy('circunscripcion', 'asc')
        ->get();

        $origenes = \DB::table('origen')
        ->where('activo', 1)
        ->get();

        $roles = \DB::table('roles')
        // ->where('id', '>=', 15)
        ->where('nivel', '!=', 0)
        ->get();

        $casas =  \DB::table('casas_campana')
        ->where('casas_campana.activo', 1)
        ->orderBy('circunscripcion', 'asc')
        ->orderBy('distrito', 'asc')
        ->orderBy('id_casa_campana', 'asc')
        ->get();

        $vehiculos = \DB::table('transportes')
        ->where('transportes.activo', 1)
        ->orderBy('id_transporte', 'asc')
        ->get();

        $evidencias = \DB::table('tipo_evidencias')
        ->where('estado', 1)
        ->orderBy('id')
        ->get();


        return view("formularios.form_agregar_persona")
        ->with('circunscripciones', $circunscripciones)
        ->with('origenes', $origenes)
        ->with('roles', $roles)
        ->with('casas', $casas)
        ->with('vehiculos', $vehiculos)
        ->with('evidencias', $evidencias);
    }

    public function agregar_persona(Request $request){

        if(\Auth::user()->isRole('registrador')==false && \Auth::user()->isRole('admin')==false && \Auth::user()->isRole('responsable_circunscripcion')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }

        $circunscripciones_listado = \DB::table('recintos')
        ->select('circunscripcion')
        ->distinct()
        ->orderBy('circunscripcion', 'asc')
        ->get();

        $origenes_listado = \DB::table('origen')
        ->where('activo', 1)
        ->get();

        $roles_listado = \DB::table('roles')
        // ->where('id', '>=', 15)
        ->where('nivel', '!=', 0)
        ->get();

        $casas_listado =  \DB::table('casas_campana')
        ->where('casas_campana.activo', 1)
        ->orderBy('circunscripcion', 'asc')
        ->orderBy('distrito', 'asc')
        ->orderBy('id_casa_campana', 'asc')
        ->get();

        $vehiculos_listado = \DB::table('transportes')
        ->where('transportes.activo', 1)
        ->orderBy('id_transporte', 'asc')
        ->get();

        $evidencias_listado = \DB::table('tipo_evidencias')
        ->where('estado', 1)
        ->orderBy('id')
        ->get();

        if($request->input("nombres") == ''){
            return 'nombres';
        }elseif($request->input("nacimiento") == ''){
            return 'nacimiento';
        }elseif ($request->input("telefono") == '') {
            return 'telefono';
        // }elseif ($request->input("direccion") == '') {
        //     return 'direccion';
        // }elseif ($request->input("grado_compromiso") == '') {
        //     return 'grado_compromiso';
        }elseif ($request->input("id_origen") == '') {
            return 'origen';
        }elseif ($request->input("id_sub_origen") == '') {
            return 'Sub Origen';
        }elseif ($request->input("titularidad") == '') {
            return 'titularidad';
        // }elseif ($request->input("informatico") == '') {
        //     return 'informatico';
        }elseif ($request->input("recinto") == '') {
            return 'recinto';
        }elseif ($request->input("rol_slug") == '') {
            return 'rol';
        }elseif($request->input("rol_slug") == 'conductor' && $request->input("id_vehiculo") == ""){
            return "id_vehiculo";
        }elseif ($request->input("rol_slug") == 'registrador' && $request->input("id_casa_campana") == "") {
            return "id_casa_campana";
        }elseif ($request->input("rol_slug") == 'responsable_mesa' && !$request->has("mesas")) {
            return "mesas";
        }elseif ($request->input("rol_slug") == 'responsable_recinto' && $request->input("recinto") == "") {
            return "recinto";
        }elseif ($request->input("rol_slug") == 'responsable_distrito' && $request->input("recinto") == "") {
            return "recinto";
        }elseif ($request->input("rol_slug") == 'responsable_circunscripcion' && $request->input("recinto") == "") {
            return "recinto";
        }else{}

        $reglas=[
            'archivo'  => 'mimes:jpg,jpeg,gif,png,bmp | max:2048000'
            ];

        $mensajes=[
        'archivo.mimes' => 'El archivo debe ser un archivo con formato: jpg, jpeg, gif, png, bmp',
        'archivo.max' => 'El archivo Supera el tamaño máximo permitido',
        ];

        $validator = Validator::make( $request->all(),$reglas,$mensajes );
        if( $validator->fails() ){
            $circunscripciones = \DB::table('recintos')

            ->select('circunscripcion')
            ->distinct()
            ->orderBy('circunscripcion', 'asc')
            ->get();

            $origenes = \DB::table('origen')
            ->where('activo', 1)
            ->get();

            $roles = \DB::table('roles')
            // ->where('id', '>=', 15)
            ->where('nivel', '!=', 0)
            ->get();

            $casas =  \DB::table('casas_campana')
            ->where('casas_campana.activo', 1)
            ->orderBy('circunscripcion', 'asc')
            ->orderBy('distrito', 'asc')
            ->orderBy('id_casa_campana', 'asc')
            ->get();

            $vehiculos = \DB::table('transportes')
            ->where('transportes.activo', 1)
            ->orderBy('id_transporte', 'asc')
            ->get();

            $evidencias = \DB::table('tipo_evidencias')
            ->where('estado', 1)
            ->orderBy('id')
            ->get();


            return view("formularios.form_agregar_persona")
            ->with('circunscripciones', $circunscripciones)
            ->with('origenes', $origenes)
            ->with('roles', $roles)
            ->with('casas', $casas)
            ->with('vehiculos', $vehiculos)
            ->with('evidencias', $evidencias)
            ->withErrors($validator)
            ->withInput($request->flash());
        }

        $cedulas = \DB::table('personas')
        ->select('cedula_identidad')
        ->where('cedula_identidad', $request->input("cedula"))
        ->where('complemento_cedula', $request->input("complemento"))
        ->where('expedido', $request->input("expedido"))
        ->distinct()
        ->get();

        if ($request->paterno == "" && $request->materno == "") {
            return "apellido";
        }else{
            if (count($cedulas) > 0) {
                return "cedula_repetida";
            }else{
                if($request->recinto != ""){
                    $persona=new Persona;

                    $persona->nombre=ucwords(strtolower($request->input("nombres")));
                    $persona->paterno=ucwords(strtolower($request->input("paterno")));
                    $persona->materno=ucwords(strtolower($request->input("materno")));
                    $persona->cedula_identidad=$request->input("cedula");
                    $persona->complemento_cedula=strtoupper($request->input("complemento"));
                    $persona->expedido="LP";
                    $persona->fecha_nacimiento=$request->input("nacimiento");
                    $persona->telefono_celular=$request->input("telefono");
                    // $persona->telefono_referencia=$request->input("telefono_ref");
                    $persona->telefono_referencia="0";
                    // $persona->direccion=ucwords(strtolower($request->input("direccion")));
                    $persona->direccion="";
                    $persona->email="";
                    $persona->grado_compromiso=4;
                    $persona->fecha_registro=date('Y-m-d');
                    $persona->activo=1;
                    $persona->asignado=1;
                    $persona->id_recinto=$request->input("recinto");
                    $persona->id_origen=$request->input("id_origen");
                    $persona->id_sub_origen=$request->input("id_sub_origen");
                    $persona->id_responsable_registro=Auth::user()->id;
                    // $persona->titularidad=$request->input("titularidad");
                    // $persona->informatico=$request->input("informatico");
                    $persona->titularidad="TITULAR";
                    $persona->informatico="SI";
                    $persona->evidencia=$request->input("evidencia");

                    $persona->id_rol=15;

                    //Subimos el archivo
                    if($request->file('archivo') != ""){
                        $tiempo_actual = new DateTime(date('Y-m-d H:i:s'));
                        $archivo = $request->file('archivo');
                        $mime = $archivo->getMimeType();
                        $extension=strtolower($archivo->getClientOriginalExtension());

                        $nuevo_nombre="R-".$request->input("recinto")."-CI-".$request->input("cedula")."-".$tiempo_actual->getTimestamp();

                        $file = $request->file('archivo');

                        $image = Image::make($file->getRealPath());

                        //reducimos la calidad y cambiamos la dimensiones de la nueva instancia.
                        $image->resize(1280, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                        });
                        $image->orientate();

                        $rutadelaimagen="../storage/media/evidencias/".$nuevo_nombre;

                        if ($image->save($rutadelaimagen)){


                        //Redirigimos a la vista form_votar_presidencial

                        $persona->archivo_evidencia=$rutadelaimagen;

                        }
                        else{
                            return view("mensajes.msj_error")->with("msj","Ocurrio un error al subir la imagen");
                        }
                    }

                    if($persona->save())
                    {
                        $persona = Persona::find($persona->id_persona);
                        $recinto = Recinto::find($request->input("recinto"));

                        $username = $this->ObtieneUsuario($persona->id_persona);
                        // $persona->id_rol =$request->input("id_rol");

                        $usuario=new User;
                        $usuario->name=$username;
                        // $email=strtolower($persona->nombre.$persona->paterno.$persona->materno).'@'.$username;
                        $usuario->email = $username;
                        $usuario->password= bcrypt($username);
                        $usuario->id_persona=$persona->id_persona;
                        $usuario->activo=1;

                        if($request->input("rol_slug") == ''){
                            //rol delegado del Partido
                            return 'rol';
                        }elseif($request->input("rol_slug") == 'militante'){
                            //rol delegado del Partido
                            if ($usuario->save()) {

                                $rol = \DB::table('roles')
                                ->where('roles.slug', $request->input("rol_slug"))
                                ->first();

                                // Cambiando el rol de persona
                                $persona->id_rol = $rol->id;
                                //Asignando rol
                                $usuario->assignRole($rol->id);

                                if ($persona->save()) {
                                    // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                    return view("formularios.form_agregar_persona")
                                    ->with('circunscripciones', $circunscripciones_listado)
                                    ->with('origenes', $origenes_listado)
                                    ->with('roles', $roles_listado)
                                    ->with('casas', $casas_listado)
                                    ->with('vehiculos', $vehiculos_listado)
                                    ->with('evidencias', $evidencias_listado);
                                } else {
                                    // si no se guarda el update
                                }

                            } else {
                                //si el usuario no se guarda
                                return "failed usuario;";
                            }
                        }elseif ($request->input("rol_slug") == 'conductor') {
                            // rol Conductor
                            if ($request->input("id_vehiculo") != "") {
                                //Si el usuario es creado correctamente modificamos su rol

                                if ($usuario->save()) {

                                    $rol = \DB::table('roles')
                                    ->where('roles.slug', $request->input("rol_slug"))
                                    ->first();
                                        // agregando el rol conductor a persona;
                                    $persona->id_rol = $rol->id;
                                    //Asignando rol el rol conductor al usuario
                                    $usuario->assignRole($rol->id);
                                    if ($persona->save()) {
                                        // creamos las relaciones usuario - transporte
                                        $usuario_transporte = new UsuarioTransporte();
                                        $usuario_transporte->id_usuario = $usuario->id;
                                        $usuario_transporte->id_transporte = $request->input("id_vehiculo");
                                        $usuario_transporte->activo = 1;
                                        if ($usuario_transporte->save()) {
                                            // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                            return view("formularios.form_agregar_persona")
                                            ->with('circunscripciones', $circunscripciones_listado)
                                            ->with('origenes', $origenes_listado)
                                            ->with('roles', $roles_listado)
                                            ->with('casas', $casas_listado)
                                            ->with('vehiculos', $vehiculos_listado)
                                            ->with('evidencias', $evidencias_listado);
                                        } else {
                                            # code...
                                        }
                                    } else {
                                        // si no se guarda el update
                                    }

                                } else {
                                    //si el usuario no se guarda
                                    return "failed usuario;";
                                }

                            } else {
                                return "id_vehiculo";
                            }
                            // fin Conductor
                        }elseif ($request->input("rol_slug") == 'registrador') {
                            // rol Registrador
                            if ($request->input("id_casa_campana") != "") {

                                //Si el usuario es creado correctamente modificamos su rol
                                if ($usuario->save()) {

                                    $rol = \DB::table('roles')
                                    ->where('roles.slug', $request->input("rol_slug"))
                                    ->first();

                                    // agregando el rol registrador en la tabla persona
                                    $persona->id_rol = $rol->id;

                                    //Asignando rol registrador en la tabla users
                                    $usuario->assignRole($rol->id);

                                    if ($persona->save()) {
                                        // creamos las relaciones usuario - recinto
                                        $usuario_casa_campana = new UsuarioCasaCampana();
                                        $usuario_casa_campana->id_usuario = $usuario->id;
                                        $usuario_casa_campana->id_casa_campana = $request->input("id_casa_campana");
                                        $usuario_casa_campana->activo = 1;
                                        if ($usuario_casa_campana->save()) {
                                            // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                            return view("formularios.form_agregar_persona")
                                            ->with('circunscripciones', $circunscripciones_listado)
                                            ->with('origenes', $origenes_listado)
                                            ->with('roles', $roles_listado)
                                            ->with('casas', $casas_listado)
                                            ->with('vehiculos', $vehiculos_listado)
                                            ->with('evidencias', $evidencias_listado);
                                        } else {
                                            return "failed usuario;";
                                        }
                                    } else {
                                        // si no se guarda el update
                                    }

                                } else {
                                    //si el usuario no se guarda
                                    return "failed usuario;";
                                }

                            } else {
                                return "id_casa_campana";
                            }
                            // fin Registrador
                        }elseif ($request->input("rol_slug") == 'call_center') {
                            //rol Call Center
                            //Si el usuario es creado correctamente modificamos su rol
                            if ($usuario->save()) {

                                $rol = \DB::table('roles')
                                ->where('roles.slug', $request->input("rol_slug"))
                                ->first();

                                // Cambiando el rol de persona
                                $persona->id_rol = $rol->id;
                                //Asignando rol
                                $usuario->assignRole($rol->id);

                                if ($persona->save()) {
                                    // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                    return view("formularios.form_agregar_persona")
                                    ->with('circunscripciones', $circunscripciones_listado)
                                    ->with('origenes', $origenes_listado)
                                    ->with('roles', $roles_listado)
                                    ->with('casas', $casas_listado)
                                    ->with('vehiculos', $vehiculos_listado)
                                    ->with('evidencias', $evidencias_listado);
                                } else {
                                    // si no se guarda el update
                                }

                            } else {
                                //si el usuario no se guarda
                                return "failed usuario;";
                            }
                        }elseif ($request->input("rol_slug") == 'responsable_mesa'){
                            //rol responsable_mesa
                            if ($request->has("mesas")) {

                                //Si el usuario es creado correctamente modificamos su rol
                                if ($usuario->save()) {

                                    $rol = \DB::table('roles')
                                    ->where('roles.slug', $request->input("rol_slug"))
                                    ->first();

                                    $persona->id_rol =$rol->id;
                                    //Asignando rol
                                    $usuario->assignRole($rol->id);

                                    if ($persona->save()) {
                                        // creamos las relaciones usuario - mesas
                                        foreach ($request->mesas as $value) {
                                            $usuario_mesa = new UsuarioMesa;
                                            $usuario_mesa->id_usuario = $usuario->id;
                                            $usuario_mesa->id_mesa = $value;
                                            $usuario_mesa->activo = 1;
                                            $usuario_mesa->save();
                                        }
                                        // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                        return view("formularios.form_agregar_persona")
                                        ->with('circunscripciones', $circunscripciones_listado)
                                        ->with('origenes', $origenes_listado)
                                        ->with('roles', $roles_listado)
                                        ->with('casas', $casas_listado)
                                        ->with('vehiculos', $vehiculos_listado)
                                        ->with('evidencias', $evidencias_listado);
                                    } else {
                                        // si no se guarda el update
                                    }

                                } else {
                                    //si el usuario no se guarda
                                    return "failed usuario;";
                                }

                            } else {
                                return "mesas";
                            }
                        //fin rol informarico
                        }elseif ($request->input("rol_slug") == 'responsable_recinto') {

                            // rol responsable recinto
                            if ($request->input("recinto") != "") {

                                //Si el usuario es creado correctamente modificamos su rol
                                if ($usuario->save()) {

                                    $rol = \DB::table('roles')
                                    ->where('roles.slug', $request->input("rol_slug"))
                                    ->first();

                                    // $persona->id_rol =$request->input("id_rol");
                                    $persona->id_rol =$rol->id;
                                    //Asignando rol
                                    $usuario->assignRole($rol->id);
                                    if ($persona->save()) {
                                        // creamos las relaciones usuario - recinto
                                        $usuario_recinto = new UsuarioRecinto;
                                        $usuario_recinto->id_usuario = $usuario->id;
                                        $usuario_recinto->id_recinto = $request->input("recinto");
                                        $usuario_recinto->activo = 1;
                                        if ($usuario_recinto->save()) {
                                            // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                            return view("formularios.form_agregar_persona")
                                            ->with('circunscripciones', $circunscripciones_listado)
                                            ->with('origenes', $origenes_listado)
                                            ->with('roles', $roles_listado)
                                            ->with('casas', $casas_listado)
                                            ->with('vehiculos', $vehiculos_listado)
                                            ->with('evidencias', $evidencias_listado);
                                        } else {
                                            # code...
                                        }
                                    } else {
                                        // si no se guarda el update
                                    }

                                } else {
                                    //si el usuario no se guarda
                                    return "failed usuario;";
                                }

                            } else {
                                return "recinto";
                            }
                            // finresponsable recinto
                        }elseif ($request->input("rol_slug") == 'responsable_distrito') {
                            //rol Responsable de Distrito

                            if ($request->input("recinto") != "") {

                                //Si el usuario es creado correctamente modificamos su rol
                                if ($usuario->save()) {

                                    $rol = \DB::table('roles')
                                    ->where('roles.slug', $request->input("rol_slug"))
                                    ->first();
                                        // $persona->id_rol =$request->input("id_rol");
                                    $persona->id_rol =$rol->id;
                                    //Asignando rol
                                    $usuario->assignRole($rol->id);

                                    if ($persona->save()) {
                                        // creamos las relaciones usuario - recinto
                                        $usuario_distrito = new UsuarioDistrito;
                                        $usuario_distrito->id_usuario = $usuario->id;
                                        $usuario_distrito->id_distrito = $recinto->distrito;
                                        $usuario_distrito->activo = 1;
                                        if ($usuario_distrito->save()) {
                                            // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                            return view("formularios.form_agregar_persona")
                                            ->with('circunscripciones', $circunscripciones_listado)
                                            ->with('origenes', $origenes_listado)
                                            ->with('roles', $roles_listado)
                                            ->with('casas', $casas_listado)
                                            ->with('vehiculos', $vehiculos_listado)
                                            ->with('evidencias', $evidencias_listado);
                                        } else {
                                            # code...
                                        }
                                    } else {
                                        // si no se guarda el update
                                    }

                                } else {
                                    //si el usuario no se guarda
                                    return "failed usuario;";
                                }

                            } else {
                                return "distrito";
                            }
                            //fin Responsable de Distrito
                        }elseif ($request->input("rol_slug") == 'responsable_circunscripcion') {
                            //rol Responsable Circunscripcion
                            if ($request->input("recinto") != "") {

                                //Si el usuario es creado correctamente modificamos su rol
                                if ($usuario->save()) {

                                    $rol = \DB::table('roles')
                                    ->where('roles.slug', $request->input("rol_slug"))
                                    ->first();
                                        // $persona->id_rol =$request->input("id_rol");
                                    $persona->id_rol =$rol->id;
                                    //Asignando rol
                                    $usuario->assignRole($rol->id);

                                    if ($persona->save()) {
                                        // creamos las relaciones usuario - recinto
                                        $usuario_circunscripcion = new UsuarioCircunscripcion;
                                        $usuario_circunscripcion->id_usuario = $usuario->id;
                                        $usuario_circunscripcion->id_circunscripcion = $recinto->circunscripcion;
                                        $usuario_circunscripcion->activo = 1;
                                        if ($usuario_circunscripcion->save()) {
                                            // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                                            return view("formularios.form_agregar_persona")
                                            ->with('circunscripciones', $circunscripciones_listado)
                                            ->with('origenes', $origenes_listado)
                                            ->with('roles', $roles_listado)
                                            ->with('casas', $casas_listado)
                                            ->with('vehiculos', $vehiculos_listado)
                                            ->with('evidencias', $evidencias_listado);
                                        } else {
                                            # code...
                                        }
                                    } else {
                                        // si no se guarda el update
                                    }

                                } else {
                                    //si el usuario no se guarda
                                    return "failed usuario;";
                                }

                            } else {
                                return "circunscripcion";
                            }
                            // fin Responsable Circunscripcion
                        }else{

                        }

                        // return view("mensajes.msj_enviado")->with("msj","enviado_crear_persona");
                        return view("formularios.form_agregar_persona")
                        ->with('circunscripciones', $circunscripciones_listado)
                        ->with('origenes', $origenes_listado)
                        ->with('roles', $roles_listado)
                        ->with('casas', $casas_listado)
                        ->with('vehiculos', $vehiculos_listado)
                        ->with('evidencias', $evidencias_listado);
                    }else{
                        return "failed";
                    }
                }
                else{
                    return "recinto";
                }
            }
        }
    }



    public function editar_asignacion_persona(Request $request){
        if(\Auth::user()->isRole('registrador')==false && \Auth::user()->isRole('admin')==false && \Auth::user()->isRole('responsable_circunscripcion')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }

        $id_persona = $request->input("id_persona");
        $persona = Persona::find($id_persona);

        if ($request->input("rol_slug") == '') {
            return 'rol';
        // }elseif ($request->input("grado_compromiso") == "") {
        //     return "grado_compromiso";
        }elseif ($request->input("recinto") == "") {
            return "recinto";
        }elseif($request->input("rol_slug") == 'conductor' && $request->input("id_vehiculo") == ""){
            return "id_vehiculo";
        }elseif ($request->input("rol_slug") == 'registrador' && $request->input("id_casa_campana") == "") {
            return "id_casa_campana";
        }elseif ($request->input("rol_slug") == 'responsable_mesa' && !$request->has("mesas")) {
            return "mesas";
        }elseif ($request->input("rol_slug") == 'responsable_recinto' && $request->input("recinto") == "") {
            return "recinto";
        }elseif ($request->input("rol_slug") == 'responsable_distrito' && $request->input("recinto") == "") {
            return "recinto";
        }elseif ($request->input("rol_slug") == 'responsable_circunscripcion' && $request->input("recinto") == "") {
            return "recinto";
        }else {
            # code...
        }

        if($request->recinto != ""){

            $persona->grado_compromiso=$request->input("grado_compromiso");

            $persona->id_origen=$request->input("id_origen");
            $persona->id_sub_origen=$request->input("id_sub_origen");
            $persona->id_responsable_registro=Auth::user()->id;
            // $persona->informatico=$request->input("informatico");
            $persona->titularidad=$request->input("titularidad");
            $recinto = Recinto::find($request->input("recinto"));
            $persona->evidencia=$request->input("evidencia");
            // Obteniendo los datos del Usuario segun el id_persona
            $usuario = \DB::table('users')
            ->where('id_persona', $request->input('id_persona'))
            ->first();
            //Cambiando el metodo de identificar usuario para usar el revoke
            $usuario=User::find($usuario->id);

            $rol = \DB::table('roles')
            ->where('roles.slug', $request->input("rol_slug"))
            ->first();

            $rol_actual = \DB::table('roles')
            ->where('id', $persona->id_rol)
            ->first();

            if ($persona->id_rol != $rol->id) {
                // si el rol cambia


                //Revocando el Rol de la tabla role_user
                $usuario->revokeRole($rol_actual->id);

                //Rol Actual a liberar
                if ($rol_actual->slug == 'militante') {
                    # militantes...
                }elseif ($rol_actual->slug == 'conductor') {
                    # conductor

                    //Quitando el rol de la relacion usuario_transporte
                    if (\DB::table('rel_usuario_transporte')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}
                }elseif ($rol_actual->slug == 'registrador') {
                    # Registrador

                    //Quitando el rol de la relacion usuario_casa_campaña
                    if (\DB::table('rel_usuario_campana')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}
                }elseif ($rol_actual->slug == 'call_center') {
                    # Call center
                }elseif ($rol_actual->slug == 'responsable_mesa') {
                    # ResponsableMesa
                    if (UsuarioMesa::where('id_usuario', $usuario->id)->delete()){}
                }elseif ($rol_actual->slug == 'responsable_recinto') {
                    # ResponsableRecinto
                    if (\DB::table('rel_usuario_recinto')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}
                }elseif ($rol_actual->slug == 'responsable_distrito') {
                    # ResponsableDistrito
                    if (\DB::table('rel_usuario_distrito')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}
                }elseif ($rol_actual->slug == 'responsable_circunscripcion') {
                    # ResponsableCircunscripcion
                    if (\DB::table('rel_usuario_circunscripcion')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}
                }  else {
                    # code...
                }

                $persona->id_recinto = $request->input("recinto");

                if($request->input("rol_slug") == 'militante'){
                    //rol delegado del Partido
                    $persona->id_rol = $rol->id;
                    if ($persona->save()) {
                        return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                    }
                }elseif ($request->input("rol_slug") == 'conductor') {
                    // rol Conductor
                    if ($request->input("id_vehiculo") != "") {
                        //Si el usuario es creado correctamente modificamos su rol

                        if ($usuario->save()) {

                            $rol = \DB::table('roles')
                            ->where('roles.slug', $request->input("rol_slug"))
                            ->first();
                                // agregando el rol conductor a persona;
                            $persona->id_rol = $rol->id;
                            //Asignando rol el rol conductor al usuario
                            $usuario->assignRole($rol->id);
                            if ($persona->save()) {
                                // creamos las relaciones usuario - transporte
                                $usuario_transporte = new UsuarioTransporte();
                                $usuario_transporte->id_usuario = $usuario->id;
                                $usuario_transporte->id_transporte = $request->input("id_vehiculo");
                                $usuario_transporte->activo = 1;
                                if ($usuario_transporte->save()) {
                                    return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                                } else {
                                    # code...
                                }
                            } else {
                                // si no se guarda el update
                            }

                        } else {
                            //si el usuario no se guarda
                            return "failed usuario;";
                        }

                    } else {
                        return "id_vehiculo";
                    }
                    // fin Conductor
                }elseif ($request->input("rol_slug") == 'registrador') {
                    // rol Registrador
                    if ($request->input("id_casa_campana") != "") {

                        //Si el usuario es creado correctamente modificamos su rol
                        if ($usuario->save()) {

                            $rol = \DB::table('roles')
                            ->where('roles.slug', $request->input("rol_slug"))
                            ->first();

                            // agregando el rol registrador en la tabla persona
                            $persona->id_rol = $rol->id;

                            //Asignando rol registrador en la tabla users
                            $usuario->assignRole($rol->id);

                            if ($persona->save()) {
                                // creamos las relaciones usuario - casa de campaña
                                $usuario_casa_campana = new UsuarioCasaCampana();
                                $usuario_casa_campana->id_usuario = $usuario->id;
                                $usuario_casa_campana->id_casa_campana = $request->input("id_casa_campana");
                                $usuario_casa_campana->activo = 1;
                                if ($usuario_casa_campana->save()) {
                                    return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                                } else {
                                    return "failed usuario;";
                                }
                            } else {
                                // si no se guarda el update
                            }

                        } else {
                            //si el usuario no se guarda
                            return "failed usuario;";
                        }

                    } else {
                        return "id_casa_campana";
                    }
                    // fin Registrador
                }elseif ($request->input("rol_slug") == 'call_center') {
                    //rol Call Center
                    //Si el usuario es creado correctamente modificamos su rol
                    if ($usuario->save()) {

                        $rol = \DB::table('roles')
                        ->where('roles.slug', $request->input("rol_slug"))
                        ->first();

                        // Cambiando el rol de persona
                        $persona->id_rol = $rol->id;
                        //Asignando rol
                        $usuario->assignRole($rol->id);

                        if ($persona->save()) {
                            return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                        } else {
                            // si no se guarda el update
                        }

                    } else {
                        //si el usuario no se guarda
                        return "failed usuario;";
                    }
                }elseif ($request->input("rol_slug") == 'responsable_mesa'){
                    //rol responsable_mesa
                    if ($request->has("mesas")) {

                        //Si el usuario es creado correctamente modificamos su rol
                        if ($usuario->save()) {

                            $rol = \DB::table('roles')
                            ->where('roles.slug', $request->input("rol_slug"))
                            ->first();

                            $persona->id_rol =$rol->id;
                            //Asignando rol
                            $usuario->assignRole($rol->id);

                            if ($persona->save()) {
                                // creamos las relaciones usuario - mesas
                                foreach ($request->mesas as $value) {
                                    $usuario_mesa = new UsuarioMesa;
                                    $usuario_mesa->id_usuario = $usuario->id;
                                    $usuario_mesa->id_mesa = $value;
                                    $usuario_mesa->activo = 1;
                                    $usuario_mesa->save();
                                }
                                return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                            } else {
                                // si no se guarda el update
                            }

                        } else {
                            //si el usuario no se guarda
                            return "failed usuario;";
                        }

                    } else {
                        return "mesas";
                    }
                //fin rol informarico
                }elseif ($request->input("rol_slug") == 'responsable_recinto') {

                    // rol responsable recinto
                    if ($request->input("recinto") != "") {

                        //Si el usuario es creado correctamente modificamos su rol
                        if ($usuario->save()) {

                            $rol = \DB::table('roles')
                            ->where('roles.slug', $request->input("rol_slug"))
                            ->first();

                            // $persona->id_rol =$request->input("id_rol");
                            $persona->id_rol =$rol->id;
                            //Asignando rol
                            $usuario->assignRole($rol->id);

                            if ($persona->save()) {
                                // creamos las relaciones usuario - recinto
                                $usuario_recinto = new UsuarioRecinto;
                                $usuario_recinto->id_usuario = $usuario->id;
                                $usuario_recinto->id_recinto = $request->input("recinto");
                                $usuario_recinto->activo = 1;
                                if ($usuario_recinto->save()) {
                                    return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                                } else {
                                    # code...
                                }
                            } else {
                                // si no se guarda el update
                            }

                        } else {
                            //si el usuario no se guarda
                            return "failed usuario;";
                        }

                    } else {
                        return "recinto";
                    }
                    // finresponsable recinto
                }elseif ($request->input("rol_slug") == 'responsable_distrito') {
                    //rol Responsable de Distrito
                    if ($request->input("recinto") != "") {

                        //Si el usuario es creado correctamente modificamos su rol
                        if ($usuario->save()) {

                            $rol = \DB::table('roles')
                            ->where('roles.slug', $request->input("rol_slug"))
                            ->first();
                                // $persona->id_rol =$request->input("id_rol");
                            $persona->id_rol =$rol->id;
                            //Asignando rol
                            $usuario->assignRole($rol->id);

                            if ($persona->save()) {
                                // creamos las relaciones usuario - recinto
                                $usuario_distrito = new UsuarioDistrito;
                                $usuario_distrito->id_usuario = $usuario->id;
                                $usuario_distrito->id_distrito = $recinto->distrito;
                                $usuario_distrito->activo = 1;
                                if ($usuario_distrito->save()) {
                                    return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                                } else {
                                    # code...
                                }
                            } else {
                                // si no se guarda el update
                            }

                        } else {
                            //si el usuario no se guarda
                            return "failed usuario;";
                        }

                    } else {
                        return "distrito";
                    }
                    //fin Responsable de Distrito
                }elseif ($request->input("rol_slug") == 'responsable_circunscripcion') {
                    //rol Responsable Circunscripcion
                    if ($request->input("recinto") != "") {

                        //Si el usuario es creado correctamente modificamos su rol
                        if ($usuario->save()) {

                            $rol = \DB::table('roles')
                            ->where('roles.slug', $request->input("rol_slug"))
                            ->first();
                                // $persona->id_rol =$request->input("id_rol");
                            $persona->id_rol =$rol->id;
                            //Asignando rol
                            $usuario->assignRole($rol->id);

                            if ($persona->save()) {
                                // creamos las relaciones usuario - circ
                                $usuario_circunscripcion = new UsuarioCircunscripcion;
                                $usuario_circunscripcion->id_usuario = $usuario->id;
                                $usuario_circunscripcion->id_circunscripcion = $recinto->circunscripcion;
                                $usuario_circunscripcion->activo = 1;
                                if ($usuario_circunscripcion->save()) {
                                    return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                                } else {
                                    # code...
                                }
                            } else {
                                // si no se guarda el update
                            }

                        } else {
                            //si el usuario no se guarda
                            return "failed usuario;";
                        }

                    } else {
                        return "circunscripcion";
                    }
                    // fin Responsable Circunscripcion
                }else{

                }


            } else {
                // Si el rol no cambia
                if ($persona->id_recinto != $request->input("recinto")) {
                    //Si el recinto cambia

                    //Rol Actual a liberar
                if ($request->input("rol_slug") == 'militante') {
                    # militantes...
                    $persona->id_recinto = $request->input("recinto");

                }elseif ($request->input("rol_slug") == 'conductor') {
                    # conductor
                    $persona->id_recinto = $request->input("recinto");

                }elseif ($request->input("rol_slug") == 'registrador') {
                    # Registrador
                    $usuario_casa_campana = new UsuarioCasaCampana();
                    $usuario_casa_campana->id_usuario = $usuario->id;
                    $usuario_casa_campana->id_casa_campana = $request->input("id_casa_campana");
                    $usuario_casa_campana->activo = 1;

                    if ($usuario_casa_campana->save()) {
                        $persona->id_recinto = $request->input("recinto");
                    }

                }elseif ($request->input("rol_slug") == 'call_center') {
                    # Call center
                    $persona->id_recinto = $request->input("recinto");

                }elseif ($request->input("rol_slug") == 'responsable_mesa') {
                    if (UsuarioMesa::where('id_usuario', $usuario->id)->delete()){}
                    # ResponsableMesa
                    foreach ($request->mesas as $value) {
                        $usuario_mesa = new UsuarioMesa;
                        $usuario_mesa->id_usuario = $usuario->id;
                        $usuario_mesa->id_mesa = $value;
                        $usuario_mesa->activo = 1;
                        $usuario_mesa->save();
                    }
                    $persona->id_recinto = $request->input("recinto");

                }elseif ($request->input("rol_slug") == 'responsable_recinto') {
                    # ResponsableRecinto
                    if (\DB::table('rel_usuario_recinto')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}

                    $usuario_recinto = new UsuarioRecinto;
                    $usuario_recinto->id_usuario = $usuario->id;
                    $usuario_recinto->id_recinto = $request->input("recinto");
                    $usuario_recinto->activo = 1;
                    if ($usuario_recinto->save()) {
                        $persona->id_recinto = $request->input("recinto");
                    }

                }elseif ($request->input("rol_slug") == 'responsable_distrito') {
                    # ResponsableDistrito
                    if (\DB::table('rel_usuario_recinto')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}

                    $usuario_distrito = new UsuarioDistrito;
                    $usuario_distrito->id_usuario = $usuario->id;
                    $usuario_distrito->id_distrito = $recinto->distrito;
                    $usuario_distrito->activo = 1;
                    if ($usuario_distrito->save()) {
                        $persona->id_recinto = $request->input("recinto");
                    }

                }elseif ($request->input("rol_slug") == 'responsable_circunscripcion') {
                    # ResponsableCircunscripcion
                    if (\DB::table('rel_usuario_circunscripcion')
                    ->where('id_usuario', $usuario->id)
                    ->delete()) {}
                    // creamos las relaciones usuario - circ
                    $usuario_circunscripcion = new UsuarioCircunscripcion;
                    $usuario_circunscripcion->id_usuario = $usuario->id;
                    $usuario_circunscripcion->id_circunscripcion = $recinto->circunscripcion;
                    $usuario_circunscripcion->activo = 1;
                    if ($usuario_circunscripcion->save()) {
                        $persona->id_recinto = $request->input("recinto");
                    }
                }  else {
                    # code...
                }

                $persona->id_recinto=$request->input("recinto");
                if($persona->save())
                {
                    return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                }else{
                    return "failed";
                }

                } else {
                    //Si el recinto no cambia

                    if ($request->input("rol_slug") == 'registrador') {
                        # Registrador
                        //Quitando la relacion usuario casa de campaña
                        if (\DB::table('rel_usuario_campana')
                        ->where('id_usuario', $usuario->id)
                        ->delete()) {}
                        //Agregando la relacion usuario casa de campaña
                        $usuario_casa_campana = new UsuarioCasaCampana();
                        $usuario_casa_campana->id_usuario = $usuario->id;
                        $usuario_casa_campana->id_casa_campana = $request->input("id_casa_campana");
                        $usuario_casa_campana->activo = 1;

                        if ($usuario_casa_campana->save()) {
                            $persona->id_recinto = $request->input("recinto");
                        }

                    }elseif ($request->input("rol_slug") == 'conductor') {
                        # Call center
                        //Revocando relacion usuario transporte
                        if (\DB::table('rel_usuario_transporte')
                        ->where('id_usuario', $usuario->id)
                        ->delete()) {}

                        // Agregando relacion usuario transporte
                        $usuario_transporte = new UsuarioTransporte();
                        $usuario_transporte->id_usuario = $usuario->id;
                        $usuario_transporte->id_transporte = $request->input("id_vehiculo");
                        $usuario_transporte->activo = 1;
                        if ($usuario_transporte->save()) {
                            return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                        }

                    }elseif ($request->input("rol_slug") == 'responsable_mesa') {
                        if (UsuarioMesa::where('id_usuario', $usuario->id)->delete()){}
                        # ResponsableMesa
                        foreach ($request->mesas as $value) {
                            $usuario_mesa = new UsuarioMesa;
                            $usuario_mesa->id_usuario = $usuario->id;
                            $usuario_mesa->id_mesa = $value;
                            $usuario_mesa->activo = 1;
                            $usuario_mesa->save();
                        }
                        $persona->id_recinto = $request->input("recinto");
                    }


                    if($persona->save())
                    {
                        return view("mensajes.msj_enviado")->with("msj","enviado_editar_persona");
                    }else{
                        return "failed";
                    }

                }

            }

        }
        else{
            return "recinto";
        }
    }


    public function editar_evidencia_persona(Request $request){

        // return $request->input("id_persona");

        $id_persona = $request->input("id_persona");
        $persona = Persona::find($id_persona);

        //Primero validamos el archivo
        $reglas=[
            'archivo'  => 'mimes:jpg,jpeg,gif,png,bmp | max:2048000'
            ];

        $mensajes=[
        'archivo.mimes' => 'El archivo debe ser un archivo con formato: jpg, jpeg, gif, png, bmp.',
        'archivo.max' => 'El archivo Supera el tamaño máximo permitido',
        ];

        $validator = Validator::make( $request->all(),$reglas,$mensajes );
        if( $validator->fails() ){

          return view("formularios.form_votar_presidencial_subir_imagen")
          ->with("persona",$persona)
          ->withErrors($validator)
          ->withInput($request->flash());
        }


        //Subimos el archivo
        if($request->file('archivo') != ""){
            $tiempo_actual = new DateTime(date('Y-m-d H:i:s'));
            $archivo = $request->file('archivo');
            $mime = $archivo->getMimeType();
            $extension=strtolower($archivo->getClientOriginalExtension());

            $nuevo_nombre="R-".$persona->id_recinto."-CI-".$persona->cedula_identidad."-".$tiempo_actual->getTimestamp();

            $file = $request->file('archivo');

            $image = Image::make($file->getRealPath());

            //reducimos la calidad y cambiamos la dimensiones de la nueva instancia.
            $image->resize(1280, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
            });
            $image->orientate();

            $rutadelaimagen="../storage/media/evidencias/".$nuevo_nombre;

            if ($image->save($rutadelaimagen)){


            //Redirigimos a la vista f

            $persona->archivo_evidencia=$rutadelaimagen;
            $persona->save();

            }
            else{
                return view("mensajes.msj_error")->with("msj","Ocurrio un error al subir la imagen");
            }
        }
        else{
            return $request->file('archivo');
        }
      }


    public function listado_personas_asignacion(){
        if(\Auth::user()->isRole('admin')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }
        $personas = [];
        return view("listados.listado_personas_asignacion")
        ->with('personas', $personas);
    }

    // public function buscar_persona_asignacion(Request $request){
    //     $dato = $request->input("dato_buscado");
    //     $personas = Persona::join('recintos', 'personas.id_recinto', 'recintos.id_recinto')
    //     ->leftjoin('users', 'personas.id_persona', 'users.id_persona')
    //     ->join('origen', 'personas.id_origen', 'origen.id_origen')
    //     ->leftjoin('sub_origen', 'personas.id_sub_origen', 'sub_origen.id_sub_origen')
    //     ->leftjoin('roles', 'personas.id_rol', 'roles.id')
    //     ->where("personas.nombre","like","%".$dato."%")
    //     ->orwhere("paterno","like","%".$dato."%")
    //     ->orwhere("materno","like","%".$dato."%")
    //     ->orwhere("cedula_identidad","like","%".$dato."%")
    //     ->orwhere("roles.slug","like","%".$dato."%")
    //     ->select('personas.*', 'recintos.id_recinto', 'recintos.nombre as nombre_recinto', 'recintos.circunscripcion', 'recintos.distrito',
    //     'recintos.zona', 'recintos.direccion as direccion_recinto',
    //     'origen.origen', 'sub_origen.nombre as sub_origen',
    //     'roles.name as nombre_rol',
    //     'users.activo as usuario_activo', 'users.name as codigo_usuario'
    //     )
    //     ->orderBy('id_persona', 'desc')
    //     // ->paginate(30);
    //     // return view('listados.listado_personas_asignacion')->with("personas",$personas);
    //     ->get();
    //     return $personas;
    // }

    public function buscar_persona_asignacion(){
        if(\Auth::user()->isRole('admin')==false){
            return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        }
        return Datatables::of(Persona::join('recintos', 'personas.id_recinto', 'recintos.id_recinto')
        ->leftjoin('users', 'personas.id_persona', 'users.id_persona')
        ->join('origen', 'personas.id_origen', 'origen.id_origen')
        ->leftjoin('sub_origen', 'personas.id_sub_origen', 'sub_origen.id_sub_origen')
        ->leftjoin('roles', 'personas.id_rol', 'roles.id')
        ->select('personas.*', 'recintos.id_recinto', 'recintos.nombre as nombre_recinto', 'recintos.circunscripcion', 'recintos.distrito',
        'recintos.zona', 'recintos.direccion as direccion_recinto',
        'origen.origen', 'sub_origen.nombre as sub_origen',
        'roles.name as nombre_rol',
        'users.activo as usuario_activo', 'users.name as codigo_usuario'
        )
        ->get())->make(true);
    }



    // public function buscar_persona(Request $request){
    //     $dato = $request->input("dato_buscado");
    //     $personas = Persona::join('recintos', 'personas.id_recinto', 'recintos.id_recinto')
    //     ->join('origen', 'personas.id_origen', 'origen.id_origen')
    //     ->leftjoin('sub_origen', 'personas.id_sub_origen', 'sub_origen.id_sub_origen')
    //     ->leftjoin('roles', 'personas.id_rol', 'roles.id')
    //     ->where("personas.nombre","like","%".$dato."%")
    //     ->orwhere("paterno","like","%".$dato."%")
    //     ->orwhere("materno","like","%".$dato."%")
    //     ->orwhere("cedula_identidad","like","%".$dato."%")
    //     ->select('personas.*', 'recintos.id_recinto', 'recintos.nombre as nombre_recinto', 'recintos.circunscripcion', 'recintos.distrito',
    //     'recintos.zona', 'recintos.direccion as direccion_recinto',
    //     'origen.origen', 'sub_origen.nombre as sub_origen',
    //     'roles.name as nombre_rol'
    //     )
    //     ->orderBy('fecha_registro', 'desc')
    //     ->orderBy('id_persona', 'desc')
    //     ->paginate(100);
    //     return view('listados.listado_personas')->with("personas",$personas);
    // }



    public function ConsultaSubOrigen($id_origen){
        $sub_origenes = \DB::table('sub_origen')
        ->where('id_origen', $id_origen)
        ->where('activo', 1)
        // ->distinct()
        ->orderBy('nombre')
        ->get();
        return $sub_origenes;
    }

    public function consultaUsuarioRegistrado($cedula){
        // if(\Auth::user()->isRole('registrador')==false && \Auth::user()->isRole('admin')==false && \Auth::user()->isRole('responsable_circunscripcion')==false){
        //     return view("mensajes.mensaje_error")->with("msj",'<div class="box box-danger col-xs-12"><div class="rechazado" style="margin-top:70px; text-align: center">    <span class="label label-success">#!<i class="fa fa-check"></i></span><br/>  <label style="color:#177F6B">  Acceso restringido </label>   </div></div> ') ;
        // }
        $personas = \DB::table('personas')
        ->join('recintos', 'personas.id_recinto', 'recintos.id_recinto')
        ->join('origen', 'personas.id_origen', 'origen.id_origen')
        ->leftjoin('sub_origen', 'personas.id_sub_origen', 'sub_origen.id_sub_origen')
        ->leftjoin('roles', 'personas.id_rol', 'roles.id')
        ->select('personas.*', 'recintos.id_recinto', 'recintos.nombre as nombre_recinto', 'recintos.circunscripcion', 'recintos.distrito',
                 'recintos.zona', 'recintos.direccion as direccion_recinto',
                 'origen.origen', 'sub_origen.nombre as sub_origen',
                 'roles.name as nombre_rol', 'roles.description',
                 \DB::raw('CONCAT(personas.paterno," ",personas.materno," ",personas.nombre) as nombre_completo'),
                 \DB::raw('CONCAT(personas.telefono_celular," - ", personas.telefono_referencia) as contacto'),
                 \DB::raw('CONCAT(personas.cedula_identidad," - ", personas.complemento_cedula) as ci'),
                 \DB::raw('CONCAT("C: ", recintos.circunscripcion," - Dist. Municipal: ", recintos.distrito," - Dist. OEP: ", recintos.distrito_referencial," - R: ", recintos.nombre) as recinto')
        )
        ->where('cedula_identidad', $cedula)
        ->orderBy('fecha_registro', 'desc')
        ->orderBy('id_persona', 'desc')
        ->get();

        return $personas;
    }

    public function ObtieneUsuario($id_persona){
        $persona = Persona::find($id_persona);

        $ci = $persona->cedula_identidad.$persona->complemento_cedula;
        $numero = 0;
        $username = $ci;
        while (User::where('name', '=', $username)->exists()) { // user found
            $username=$username+$numero;
            $numero++;
        }

        //Quitar espacios en blanco
        $username = str_replace(' ', '', $username);
        return $username;
    }*/
}
