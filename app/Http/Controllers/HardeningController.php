<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use App\User;
use Illuminate\Support\Facades\Validator;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\Permission;

use Carbon\Carbon;

use Auth;
use DateTime;

use App\Persona;


use App\Politica;
use App\Perfil;
use App\Regla;
use App\PerfilRegla;
use App\Cliente;
use App\ClientePerfil;
use App\Escaneo;

use Datatables;

class HardeningController extends Controller
{

  public function listado_politicas(){
    return view("listados.listado_politicas");
  }

  public function buscar_politicas(){
    return Datatables::of(\DB::table('perfiles')
                          ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
                          ->select('id_perfil', 
                                   //'politicas.nombre as politica_nombre', 
                                   \DB::raw('CONCAT(politicas.descripcion," (",politicas.nombre,")") as politica_nombre'),
                                   \DB::raw('CONCAT(perfiles.title," (Id: ",perfiles.profile_id,")") as perfil_title'),
                                   'perfiles.description as perfil_description',
                                    'perfiles.tipo as perfil_tipo', 'perfiles.activo as perfil_activo',
                                     \DB::raw('IF(perfiles.activo = 1, "Activo", "Inactivo") as estado'))
                          ->orderBy('id_perfil', 'DESC')
                          ->get()
                        )->make(true);
  }

  public function form_nueva_politica(){
    return view("formularios.form_nueva_politica");
  }

  public function nueva_politica(Request $request){
      $nombre = $_FILES["file_politica"]["name"]; //Nombre de nuestro archivo
      $url_temp = $_FILES["file_politica"]["tmp_name"]; //Ruta temporal a donde se carga el archivo
      $ruta_archivo = "/var/www/html/hardening/public/oscap/politicas"; //Carpeta donde subiremos nuestros archivos

      //Ruta donde se guardara el archivo, usamos str_replace para reemplazar los "\" por "/"
      $url_target = str_replace('\\', '/', $ruta_archivo) . '/' . $nombre;

      //Si la carpeta no existe, la creamos
      /*if (!file_exists($url_insert)) {
          mkdir($url_insert, 0777, true);
      };*/

      //Registramos la política agregada
        $politica = new Politica;
        $politica->ruta_archivo=$url_target;
        $politica->tipo=$request->input("tipo");
        $politica->tipo_politica="Default";
        $politica->nombre=$nombre;
        $politica->descripcion=$request->input("descripcion");
        $politica->id_usuario_updated=Auth::user()->id;
        $politica->activo=1;
        $politica->save();

      //Obtenemos los títulos y perfiles contenidos en la política
      $salida_perfiles = shell_exec("oscap info /var/www/html/hardening/public/oscap/politicas/ssg-ubuntu1804-ds.xml | grep -E '^\s*(Title:|Id:)' | sed 's/^[[:space:]]*//'");
      $delimitador = "Title:";
      $perfiles = explode($delimitador, $salida_perfiles);

      //Tomamos cada perfil
      foreach ($perfiles as $perfil) {
        if ($perfil != "") {
          //Obtenemos el ID del perfil
          $busqueda = "Id:";
          $posicion = strpos($perfil, $busqueda);
          if ($posicion !== false) {
              $title = strstr($perfil, $busqueda, true);
              $profile_id = strval(rtrim(substr($perfil, $posicion+3)));
              //Borramos espacios en blanco
              $profile_id = preg_replace('/\s+/', '', $profile_id);
              //Obtenemos el campo description
              $description = shell_exec("echo $(xmlstarlet sel -t -v \"//xccdf-1.2:Profile[@id='$profile_id']/xccdf-1.2:description\" -n $url_target)");
              //Regsitramos los perfiles
              $perfil = new Perfil;
              $perfil->id_politica=$politica->id_politica;
              $perfil->profile_id=$profile_id;
              $perfil->title=$title;
              $perfil->description=$description;
              $perfil->tipo="Default";
              $perfil->id_usuario_updated=Auth::user()->id;
              $perfil->activo=1;
              $perfil->save();
            } else {
             echo "Perfiles no encontrados.";
          }
        }
      }

      //Obtenemos las reglas
      $salida_rules = shell_exec("echo $(xmlstarlet sel -t -m \"//xccdf-1.2:Rule\" -v \"@id\" -o \"¡\" -v \"xccdf-1.2:title\" -o \"µ\" -v \"xccdf-1.2:description\" -o \"¿\" -n $url_target)");
      $delimitador = "¿";
      $rules = explode($delimitador, $salida_rules);

      //Tomamos cada regla
      foreach ($rules as $rule) {
        if ($rule != "") {
          //Obtenemos los datos de la regla
          $busqueda_adm = "¡";
          $posicion_adm = strpos($rule, $busqueda_adm);
          if ($posicion !== false) {
              //Obtenemos el rule_id
              $rule_id = strstr($rule, $busqueda_adm, true);
              //Borramos espacios en blanco
              $rule_id = preg_replace('/\s+/', '', $rule_id);
              //Obtenemos el title
              $busqueda_miu = "µ";
              $posicion_miu = strpos($rule, $busqueda_miu);
              $title=substr(strstr($rule, $busqueda_miu, true), $posicion_adm+2);

              //Obtenemos el campo description
              $description=substr($rule, $posicion_miu+2);

              //Regsitramos la regla solo en caso de que el $rule_id sea distinto de vacio
              if ($rule_id != "") {
                $regla = new Regla;
                $regla->id_politica=$politica->id_politica;
                $regla->type="Rule";
                $regla->id_elemento=$rule_id;
                $regla->title=$title;
                $regla->description=$description;
                $regla->id_usuario_updated=Auth::user()->id;
                $regla->activo=1;
                $regla->save();
              }
          } else {
            echo "Reglas no encontradas.";
          }
        }
      }
      
      //Movemos el archivo de la carpeta temporal a la carpeta objetivo y verificamos si fue exitoso
      if (move_uploaded_file($url_temp, $url_target)) {
        $mensaje_exito = "La política " . htmlspecialchars(basename($nombre)) . " fue cargada correctamente.";
        return view("listados.listado_politicas")
          ->with("mensaje_exito", $mensaje_exito);
      } else {
        return view("listados.listado_politicas")
          ->with("mensaje_error", "Error al cargar la política");
      } 
  }

  public function form_nuevo_perfil(){
    $politicas = \DB::table('politicas')
      ->select('id_politica', 'nombre', 'descripcion')
      ->where('tipo_politica', 'Default')
      ->where('activo', 1)
      ->get();

    return view("formularios.form_nuevo_perfil")
      ->with('politicas', $politicas);
  }

  public function nuevo_perfil(Request $request){
      //Construimos y tomamos las variables
      $fecha = date("Y-m-d-H-i");
      
      //Creamos el perfil
      $perfil = new Perfil;
      $perfil->id_politica=$request->input("id_politica");
      $perfil->profile_id=$request->input("profile_id");
      $perfil->title=$request->input("title");
      $perfil->description=$request->input("description");
      $perfil->tipo="Custom";
      $perfil->id_politica_base=$request->input("id_politica");
      $perfil->id_usuario_updated=Auth::user()->id;
      $perfil->activo=1;
      $perfil->save();

      //Crear registros en la tabla perfiles_reglas, copiando todas las reglas de la política base
      //Seleccionamos las reglas que forman parte de la politica base
      $politica_base_reglas = \DB::table('reglas')
        ->select('id_regla', 'id_elemento')
        ->where('id_politica', $request->input("id_politica"))
        ->where('activo', 1)
        ->get();

      //Realizamos un registro para cada regla
      foreach ($politica_base_reglas as $politica_base_regla) {
        //REgsitramos solo en caso de que el id_elemento sea distinto de vacio
        if ($politica_base_regla->id_elemento != "") {
          $perfil_regla = new PerfilRegla;
          $perfil_regla->id_perfil=$perfil->id_perfil;
          $perfil_regla->id_regla=$politica_base_regla->id_regla;
          $perfil_regla->habilitada=0;
          $perfil_regla->id_usuario_updated=Auth::user()->id;
          $perfil_regla->activo=1;
          $perfil_regla->save();
        }
      }      
      return view("listados.listado_politicas")
        ->with("mensaje_exito", "Perfil creado correctamente.");     
  }

  public function form_editar_reglas($id_perfil){
      //Tomamos las reglas que conforman el perfil customizado
      $reglas = \DB::table('perfiles_reglas')
        ->join('reglas', 'perfiles_reglas.id_regla', '=', 'reglas.id_regla')
        ->select('id_perfil_regla', 'id_perfil', 'perfiles_reglas.id_regla', 'habilitada', 'id_elemento', 'title', 'description')
        ->where('id_perfil', $id_perfil)
        ->where('perfiles_reglas.activo', 1)
        ->get();

      //Tomamos otras variables para el formulario
      $politica_base_title = \DB::table('perfiles')
        ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
        ->where('id_perfil', $id_perfil)
        ->value('nombre');

      $politica_base_description = \DB::table('perfiles')
      ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
      ->where('id_perfil', $id_perfil)
      ->value('descripcion');

      $title = \DB::table('perfiles')
      ->where('id_perfil', $id_perfil)
      ->value('title');

      $description = \DB::table('perfiles')
      ->where('id_perfil', $id_perfil)
      ->value('description');

      return view("formularios.form_editar_reglas")
        ->with('reglas', $reglas)
        ->with('politica_base_title', $politica_base_title)
        ->with('politica_base_description', $politica_base_description)
        ->with('title', $title)
        ->with('description', $description);
  }


  public function guardado_preventivo_reglas(Request $request){
    //Tomamos la fecha actual
    $date = new Carbon();
    $hoy = Carbon::now();
    $timestamp = $hoy->format('Y-m-d H:i:s');

    //Tomamos las variables
    $solicitud = $request->all();
    $key_anterior = "";
    $key_anterior_longitud = -1;
    $value_anterior = 0;
    $id_perfil_regla = 0;
    //dd($solicitud);
    foreach ($solicitud as $key => $value){
      //Recorremos todo el array y vamos pasando de variables $key_aterior a $key_actual para compararlas
      //Si son iguales, significa que $anterior tiene el check habilitado, caso contrario está deshabilitado y seguimos recorriendo el array
      $key_actual = substr($key, 0, $key_anterior_longitud);    
      
      //Actualizamos los registros 
      
      if ($key_actual == $key_anterior) {
        //echo "Regla ".$value_anterior." HAB"."<br>";
        //Actualizamos el registro
        \DB::table('perfiles_reglas')
        ->where('id_perfil_regla', $id_perfil_regla)
        ->update(['habilitada' => 1,
                  'updated_at' => $timestamp,
                  'id_usuario_updated' => Auth::user()->id]);
      }
      else {
        //echo (int)$id_perfil_regla;
        if (is_int($id_perfil_regla) && $id_perfil_regla != 0) {
          //echo "Regla ".$value_anterior." DES"."<br>";
          //Actualizamos el registro
        \DB::table('perfiles_reglas')
        ->where('id_perfil_regla', $id_perfil_regla)
        ->update(['habilitada' => 0,
                  'updated_at' => $timestamp,
                  'id_usuario_updated' => Auth::user()->id]);
        }
      }
      
      //Actualizamos la variable anterior
      $id_perfil_regla = (int)$value; //VOlvemos la cadena en int, si es texto lo convierte en 0
      $value_anterior = $value;
      $key_anterior = $key;
      $key_anterior_longitud = strlen($key); 
    }
  }

  public function listado_clientes(){
    return view("listados.listado_clientes");
  }

  public function buscar_clientes(){
    return Datatables::of(\DB::table('clientes')
                          ->select('id_cliente', 'nombre', 'direccion_ip', 'descripcion', 
                          \DB::raw('IF(activo = 1, "Activo", "Inactivo") as estado'))
                          ->orderBy('id_cliente', 'DESC')
                          ->get()
                        )->make(true);
  }

  public function form_nuevo_cliente(){
    $perfiles = \DB::table('perfiles')
      ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
      ->select('perfiles.id_perfil', 'politicas.descripcion as politica_descripcion', 'politicas.nombre as politica_nombre', 
                'perfiles.title as perfil_title', 'perfiles.profile_id as perfil_id')
      ->where('perfiles.activo', 1)
      ->get();
    return view("formularios.form_nuevo_cliente")
      ->with('perfiles', $perfiles);
  }


  public function nuevo_cliente(Request $request){
    //Encriptamos el usuario y contraseña
    $usuario_encripted = encrypt($request->input("usuario"));
    $contrasena_encripted = encrypt($request->input("contrasena"));
    //Creamos el cliente
    $cliente = new Cliente;
    $cliente->nombre=$request->input("nombre");
    $cliente->descripcion=$request->input("descripcion");
    $cliente->direccion_ip=$request->input("direccion_ip");
    $cliente->usuario=$usuario_encripted;
    $cliente->contrasena=$contrasena_encripted;
    $cliente->id_usuario_updated=Auth::user()->id;
    $cliente->activo=1;
    $cliente->save();

    foreach ($request->perfiles_asignados as $key => $value) {
      //Registramos los perfiles seleccioandos
      $cliente_perfil = new ClientePerfil;
      $cliente_perfil->id_cliente=$cliente->id_cliente;
      $cliente_perfil->id_perfil=$value;
      $cliente_perfil->id_usuario_updated=Auth::user()->id;
      $cliente_perfil->activo=1;
      $cliente_perfil->save();
    }

    return view("listados.listado_clientes")
      ->with("mensaje_exito", "Cliente creado correctamente.");     
  }

  public function form_editar_cliente($id_cliente){
      //Tomamos las variables para el formulario
      $nombre = \DB::table('clientes')
        ->where('id_cliente', $id_cliente)
        ->value('nombre');
      
      $descripcion = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('descripcion');
        
      $direccion_ip = \DB::table('clientes')
        ->where('id_cliente', $id_cliente)
        ->value('direccion_ip');

      $usuario_encripted = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('usuario');
      $usuario = decrypt($usuario_encripted);

      $contrasena_encripted = \DB::table('clientes')
        ->where('id_cliente', $id_cliente)
        ->value('contrasena');
      $contrasena = decrypt($contrasena_encripted);

      $perfiles = \DB::table('perfiles')
        ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
        ->select('perfiles.id_perfil', 'politicas.descripcion as politica_descripcion', 'politicas.nombre as politica_nombre', 
                  'perfiles.title as perfil_title', 'perfiles.profile_id as perfil_id')
        ->where('perfiles.activo', 1)
        ->get();

      $perfiles_activos = \DB::table('clientes_perfiles')
        ->where('id_cliente', $id_cliente)
        ->where('activo', 1)
        ->get();

      return view("formularios.form_editar_cliente")
        ->with('id_cliente', $id_cliente)
        ->with('nombre', $nombre)
        ->with('descripcion', $descripcion)
        ->with('direccion_ip', $direccion_ip)
        ->with('usuario', $usuario)
        ->with('contrasena', $contrasena)
        ->with('perfiles', $perfiles)
        ->with('perfiles_activos', $perfiles_activos);
  }

  public function editar_cliente(Request $request){
    //Tomamos la fecha actual
      $date = new Carbon();
      $hoy = Carbon::now();
      $timestamp = $hoy->format('Y-m-d H:i:s');

    //Encriptamos el usuario y contraseña
    $usuario_encripted = encrypt($request->input("usuario"));
    $contrasena_encripted = encrypt($request->input("contrasena"));
    
    //Actualizamos el registro
    \DB::table('clientes')
    ->where('id_cliente', $request->input("id_cliente"))
    ->update(['nombre' => $request->input("nombre"),
              'descripcion' => $request->input("descripcion"),
              'direccion_ip' => $request->input("direccion_ip"),
              'usuario' => $usuario_encripted,
              'contrasena' => $contrasena_encripted,
              'updated_at' => $timestamp,
              'id_usuario_updated' => Auth::user()->id]);

    //Desactivamos (borramos) los registros previos a la edición
    \DB::table('clientes_perfiles')
      ->where('id_cliente', $request->input("id_cliente"))
      ->where('activo', 1)
      ->update(['activo' => 0,
                'updated_at' => $timestamp,
                'id_usuario_updated' => Auth::user()->id]);

    foreach ($request->perfiles_asignados as $key => $value) {
        //Registramos los perfiles seleccioandos
        $cliente_perfil = new ClientePerfil;
        $cliente_perfil->id_cliente=$request->input("id_cliente");
        $cliente_perfil->id_perfil=$value;
        $cliente_perfil->id_usuario_updated=Auth::user()->id;
        $cliente_perfil->activo=1;
        $cliente_perfil->save();
    }

    return view("listados.listado_clientes")
      ->with("mensaje_exito", "Cliente modificado correctamente.");     
  }

  public function listado_clientes_perfiles(){
    return view("listados.listado_clientes_perfiles");
  }

  public function buscar_clientes_perfiles(){
    return Datatables::of(\DB::table('clientes_perfiles')
                      ->join('clientes', 'clientes_perfiles.id_cliente', '=', 'clientes.id_cliente')
                      ->join('perfiles', 'clientes_perfiles.id_perfil', '=', 'perfiles.id_perfil')
                      ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
                      ->select('id_cliente_perfil',  'clientes.nombre', 'clientes.direccion_ip', 'clientes.descripcion',
                          \DB::raw('CONCAT(politicas.descripcion," <br>(",politicas.nombre,")") as politica'),
                          \DB::raw('CONCAT(perfiles.title," <br>(Id: ",perfiles.profile_id,")") as perfil'))
                      ->orderBy('id_cliente_perfil', 'DESC')
                      ->where('clientes_perfiles.activo', 1)
                      ->get()
                        )->make(true);
  }

  public function form_nuevo_escaneo($id_cliente_perfil){
      //Tomamo el $id_cliente
      $id_cliente = \DB::table('clientes_perfiles')
        ->where('id_cliente_perfil', $id_cliente_perfil)
        ->value('id_cliente');

      //Tomamos las variables para el formulario
      $nombre = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('nombre');
      
      $descripcion = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('descripcion');
        
      $direccion_ip = \DB::table('clientes')
        ->where('id_cliente', $id_cliente)
        ->value('direccion_ip');

      $usuario_encripted = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('usuario');
      $usuario = decrypt($usuario_encripted);

      $contrasena_encripted = \DB::table('clientes')
        ->where('id_cliente', $id_cliente)
        ->value('contrasena');
      $contrasena = decrypt($contrasena_encripted);

      $perfiles = \DB::table('perfiles')
        ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
        ->select('perfiles.id_perfil', 'politicas.descripcion as politica_descripcion', 'politicas.nombre as politica_nombre', 
                  'perfiles.title as perfil_title', 'perfiles.profile_id as perfil_id')
        ->where('perfiles.activo', 1)
        ->get();

      $perfiles_activos = \DB::table('clientes_perfiles')
        ->where('id_cliente', $id_cliente)
        ->where('activo', 1)
        ->get();

      return view("formularios.form_nuevo_escaneo")
        ->with('id_cliente_perfil', $id_cliente_perfil)
        ->with('id_cliente', $id_cliente)
        ->with('nombre', $nombre)
        ->with('descripcion', $descripcion)
        ->with('direccion_ip', $direccion_ip)
        ->with('usuario', $usuario)
        ->with('contrasena', $contrasena)
        ->with('perfiles', $perfiles)
        ->with('perfiles_activos', $perfiles_activos);
  }

  public function nuevo_escaneo(Request $request){
    //Armamos el script expect a ejecutar para correr oscap y pasar la contraseña sin interacción del usuario
    //Tomamos las variables
    $date = new Carbon();
    $hoy = Carbon::now();
    $timestamp = $hoy->format('Y-m-d_H-i-s');
    
    $id_perfil = \DB::table('clientes_perfiles')
                          ->where('id_cliente_perfil', $request->input("id_cliente_perfil"))
                          ->value('id_perfil');

    $tipo_perfil = \DB::table('perfiles')
                          ->where('id_perfil', $id_perfil)
                          ->value('tipo');

    $profile_id = \DB::table('perfiles')
                    ->where('id_perfil', $id_perfil)
                    ->value('profile_id');

    $id_politica = \DB::table('perfiles')
                    ->where('id_perfil', $id_perfil)
                    ->value('id_politica');
                  
    $ruta_politica = \DB::table('politicas')
                    ->where('id_politica', $id_politica)
                    ->value('ruta_archivo');
    
    //Seteamos las variables
    $exect_script_nombre = $timestamp."_id_cliente_perfil_".$request->input("id_cliente_perfil")."_exect.exp";
    $result_nombre = $timestamp."_id_cliente_perfil_".$request->input("id_cliente_perfil")."_result.xml";
    $report_nombre = $timestamp."_id_cliente_perfil_".$request->input("id_cliente_perfil")."_report.html";

    //Si el escaneo es con un perfil "Custom", copiamos la política y la editamos agregando el perfil 
    if ($tipo_perfil == "Custom") {
      //Primero replicamos la política base
        $id_politica_base = \DB::table('perfiles')
                  ->where('id_perfil', $id_perfil)
                  ->value('id_politica_base');

        $ruta_archivo_politica_base = \DB::table('politicas')
              ->where('id_politica', $id_politica_base)
              ->value('ruta_archivo');

        $nombre_politica_base = \DB::table('politicas')
              ->where('id_politica', $id_politica_base)
              ->value('nombre');

        $nombre_politica_copiada = substr($nombre_politica_base, 0, -4)."_custom_".$timestamp.".xml";
        $ruta_politica_copiada = "/var/www/html/hardening/public/oscap/politicas/".$nombre_politica_copiada;

        $politica_replicada = shell_exec("cp ".$ruta_archivo_politica_base." ".$ruta_politica_copiada);

        //Tomamos los datos de la política base y Creamos el registro para la nueva politica
        $tipo_politica_base = \DB::table('politicas')
                ->where('id_politica', $id_politica_base)
                ->value('tipo');

        $descripcion_politica_base = \DB::table('politicas')
                ->where('id_politica', $id_politica_base)
                ->value('tipo');

        $politica = new Politica;
        $politica->ruta_archivo=$ruta_politica_copiada;
        $politica->tipo=$tipo_politica_base;
        $politica->tipo_politica="Custom";
        $politica->nombre=$nombre_politica_copiada;
        $politica->descripcion=$descripcion_politica_base;
        $politica->id_usuario_updated=Auth::user()->id;
        $politica->activo=1;
        $politica->save();

        //Damos de baja la política custom anterior y actualizamos el id_politica del perfil
        \DB::table('politicas')
              ->where('id_politica', $id_politica)
              ->where('tipo_politica', "Custom")
              ->update(['activo' => "0",
                        'updated_at' => $timestamp,
                        'id_usuario_updated' => Auth::user()->id]);

        \DB::table('perfiles')
                ->where('id_perfil', $id_perfil)
                ->update(['id_politica' => $politica->id_politica,
                          'updated_at' => $timestamp,
                          'id_usuario_updated' => Auth::user()->id]);

      //Tomamos las reglas activas para el perfil dado
        $reglas_seleccionadas = \DB::table('perfiles_reglas')
                ->select('reglas.id_elemento')
                ->join('reglas', 'perfiles_reglas.id_regla', '=', 'reglas.id_regla')
                ->where('perfiles_reglas.id_perfil', $id_perfil)
                ->where('perfiles_reglas.habilitada', "1")
                ->where('perfiles_reglas.activo', "1")
                ->get();

      //Armamos el texto a agregar a la politica
      $profile_title = \DB::table('perfiles')
                ->where('id_perfil', $id_perfil)
                ->value('title');

      $profile_description = \DB::table('perfiles')
                ->where('id_perfil', $id_perfil)
                ->value('description');

      $reglas_a_agregar_a_politica = "<xccdf-1.2:Profile id=\"$profile_id\">";
      $reglas_a_agregar_a_politica = $reglas_a_agregar_a_politica."\n"."<xccdf-1.2:title override=\"true\">".$profile_title."</xccdf-1.2:title>";
      $reglas_a_agregar_a_politica = $reglas_a_agregar_a_politica."\n"."<xccdf-1.2:description override=\"true\">".$profile_description."</xccdf-1.2:description>";

      foreach ($reglas_seleccionadas as $regla_seleccionada) {
        if ($regla_seleccionada->id_elemento != "") {
          $reglas_a_agregar_a_politica = $reglas_a_agregar_a_politica."\n"."<xccdf-1.2:select idref=\"".$regla_seleccionada->id_elemento."\" selected=\"true\"/>";
        }
      }

      $reglas_a_agregar_a_politica = $reglas_a_agregar_a_politica."\n"."</xccdf-1.2:Profile>";

      //Insertamos el perfil y las reglas a la politica copiada
      // Leemos el contenido del archivo
      $contenidoExistente = file_get_contents($ruta_politica_copiada);

      // Definir la ubicación descupes de la cual se agregara el texto
      //$ubicacionDeseada = "      </xccdf-1.2:Profile>";
      $ubicacionDeseada = "</xccdf-1.2:Profile>";

      // Dividir el contenido existente en un array de líneas
      //$lineas = explode("\n", $contenidoExistente);
      $lineas = array_map('trim', explode("\n", $contenidoExistente));


      // Encontrar la ubicación deseada
      $indice = array_search($ubicacionDeseada, $lineas);

      // Verificar si se encontró la ubicación deseada
      if ($indice !== false) {
          // Insertar el nuevo contenido en el lugar deseado
          array_splice($lineas, $indice + 1, 0, $reglas_a_agregar_a_politica);

          // Convertir el array de líneas de nuevo a una cadena
          $nuevoContenido = implode("\n", $lineas);

          // Escribir el contenido actualizado en el archivo
          file_put_contents($ruta_politica_copiada, $nuevoContenido);

          //Actualizamos la $ruta_politica
          $ruta_politica = $ruta_politica_copiada;
          //echo "Contenido agregado exitosamente.";
      } else {
          //echo "Ubicación deseada no encontrada en el archivo.";
      }
    }

    //Ejecutamos el escaneo
    // Armamos el script .exp para su ejecucion automatica
    $comando = "touch /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre;
      $script_expect = shell_exec($comando);
    $script_expect = shell_exec("echo '#!/usr/bin/expect -f' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);
      $comando = "echo 'spawn oscap-ssh ".$request->input("usuario")."@".$request->input("direccion_ip")." 22 xccdf eval --profile ".$profile_id." --results /var/www/html/hardening/public/oscap/resultados/".$result_nombre." --report /var/www/html/hardening/public/oscap/resultados/".$report_nombre." ".$ruta_politica."' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre;
    $script_expect = shell_exec($comando);
    $expectCommand = "expect \"".$request->input("usuario")."@".$request->input("direccion_ip")."'s password:\" {";
      $filePath = '/var/www/html/hardening/public/oscap/temporales/'.$exect_script_nombre;
      file_put_contents($filePath, $expectCommand . PHP_EOL, FILE_APPEND);
    $expectCommand = "  send \"".$request->input("contrasena")."\\r\"";
      $filePath = '/var/www/html/hardening/public/oscap/temporales/'.$exect_script_nombre;
      file_put_contents($filePath, $expectCommand . PHP_EOL, FILE_APPEND);
    $script_expect = shell_exec("echo '}' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);
    $script_expect = shell_exec("echo 'expect eof' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);
    $script_expect = shell_exec("echo 'interact' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);

    //Una vez creado el script, lo ejecutamos
    $script_expect = shell_exec("expect /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);

    //Obtenemos el result_id
    $salida_result = shell_exec("oscap info /var/www/html/hardening/public/oscap/resultados/".$result_nombre." | grep -E '^\s*(Result ID:)' | sed 's/^[[:space:]]*//'");
    $result_id = substr($salida_result, 11);
    //Quitamos los espacios en blanco
    $result_id = preg_replace('/\s+/', '', $result_id);

    //Guardamos los datos de la ejecución del escaneo
    $escaneo = new Escaneo;
    $escaneo->id_cliente_perfil = $request->input("id_cliente_perfil");
    $escaneo->notas = $request->input("notas");
    $escaneo->ruta_archivo_xml = "/var/www/html/hardening/public/oscap/resultados/".$result_nombre;
    $escaneo->ruta_archivo_html = "/var/www/html/hardening/public/oscap/resultados/".$report_nombre;
    $escaneo->timestamp_escaneo = $timestamp;
    $escaneo->result_id = $result_id;
    $escaneo->id_usuario_updated=Auth::user()->id;
    $escaneo->activo=1;
    $escaneo->save();   
    
    return view("listados.listado_escaneos")
        ->with("mensaje_exito", "Escaneo ejecutado exitosamente.");
  }

  public function listado_escaneos(){
    return view("listados.listado_escaneos");
  }

  public function buscar_escaneos(){
    return Datatables::of(\DB::table('escaneos')
                      ->join('clientes_perfiles', 'escaneos.id_cliente_perfil', '=', 'clientes_perfiles.id_cliente_perfil')
                      ->join('clientes', 'clientes_perfiles.id_cliente', '=', 'clientes.id_cliente')
                      ->join('perfiles', 'clientes_perfiles.id_perfil', '=', 'perfiles.id_perfil')
                      ->join('politicas', 'perfiles.id_politica', '=', 'politicas.id_politica')
                      ->select('escaneos.id_escaneo', 'escaneos.id_cliente_perfil',
                          \DB::raw('CONCAT("IP: ",clientes.direccion_ip," <br> Cliente: ",clientes.nombre," <br> Descripción: ",clientes.descripcion) as cliente'),
                          \DB::raw('CONCAT(politicas.descripcion," <br>(",politicas.nombre,")") as politica'),
                          \DB::raw('CONCAT(perfiles.title," <br>(Id: ",perfiles.profile_id,")") as perfil'),
                          \DB::raw('CONCAT("Fecha y hora: ", escaneos.timestamp_escaneo," <br> Notas: ", escaneos.notas) as escaneo'),
                          //\DB::raw('CONCAT(escaneos.timestamp_escaneo," <br> Notas: ", escaneos.notas) as hardening'))
                          \DB::raw('IF(escaneos.hardening_ejecutado = 0, 
                                    "No ejecutado", 
                                    CONCAT("Último hardening ejecutado: ", escaneos.timestamp_ultimo_hardening)
                                    ) 
                                  as hardening'))
                      ->orderBy('id_cliente_perfil', 'DESC')
                      ->where('escaneos.activo', 1)
                      ->get()
                        )->make(true);
  }

  public function form_resultados_escaneo($id_escaneo){
    //Obtenemos la ruta del resultado del escaneo
    $ruta_archivo_html = \DB::table('escaneos')
                ->where('id_escaneo', $id_escaneo)
                ->value('ruta_archivo_html');

    return view("formularios.form_resultados_escaneos")
          ->with('ruta_archivo_html', $ruta_archivo_html);
  }

  public function form_hardening($id_escaneo){
    $id_cliente = \DB::table('escaneos')
        ->join('clientes_perfiles', 'escaneos.id_cliente_perfil', '=', 'clientes_perfiles.id_cliente_perfil')
        ->where('id_escaneo', $id_escaneo)
        ->value('id_cliente');

    $direccion_ip = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('direccion_ip');

    $usuario_encripted = \DB::table('clientes')
    ->where('id_cliente', $id_cliente)
    ->value('usuario');
    $usuario = decrypt($usuario_encripted);

    $contrasena_encripted = \DB::table('clientes')
      ->where('id_cliente', $id_cliente)
      ->value('contrasena');
    $contrasena = decrypt($contrasena_encripted);

    return view("confirmaciones.form_hardening")
          ->with('direccion_ip', $direccion_ip)
          ->with('usuario', $usuario)
          ->with('contrasena', $contrasena)
          ->with('id_escaneo', $id_escaneo);
  }


  public function hardening(Request $request){
    $request->input("usuario");

    //Generamos el script de remediacion
    $date = new Carbon();
    $hoy = Carbon::now();
    $timestamp = $hoy->format('Y-m-d_H-i-s');
        
    $script_remediacion_nombre = $timestamp."_id_escaneo_".$request->input("id_escaneo").".sh";
    
    $result_id = \DB::table('escaneos')
          ->where('id_escaneo', $request->input("id_escaneo"))
          ->value('result_id');

    $ruta_archivo_xml = \DB::table('escaneos')
          ->where('id_escaneo', $request->input("id_escaneo"))
          ->value('ruta_archivo_xml');

    
    $comando = "oscap xccdf generate fix --fix-type bash --output /var/www/html/hardening/public/oscap/scripts-remediacion/".$script_remediacion_nombre." --result-id ".$result_id." ".$ruta_archivo_xml;
    $script_sh = shell_exec($comando);


    //Armamos el script para pasar el archivo de remediación al servidor remoto
    $exect_script_nombre = $timestamp."_id_escaneo_".$request->input("id_escaneo")."_pasar_remediacion.exp";
    $comando = "touch /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre;
    $script_expect = shell_exec($comando);
    $script_expect = shell_exec("echo '#!/usr/bin/expect -f' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);
    $comando = "echo 'spawn scp /var/www/html/hardening/public/oscap/scripts-remediacion/".$script_remediacion_nombre." ".$request->input("usuario")."@".$request->input("direccion_ip").":/home/".$request->input("usuario")."' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre;
    $script_expect = shell_exec($comando);
    $expectCommand = "expect \"".$request->input("usuario")."@".$request->input("direccion_ip")."'s password:\" {";
      $filePath = '/var/www/html/hardening/public/oscap/temporales/'.$exect_script_nombre;
      file_put_contents($filePath, $expectCommand . PHP_EOL, FILE_APPEND);
    $expectCommand = "  send \"".$request->input("contrasena")."\\r\"";
      $filePath = '/var/www/html/hardening/public/oscap/temporales/'.$exect_script_nombre;
      file_put_contents($filePath, $expectCommand . PHP_EOL, FILE_APPEND);
    $script_expect = shell_exec("echo '}' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);
    $script_expect = shell_exec("echo 'expect eof' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);
    $script_expect = shell_exec("echo 'interact' >> /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);

    //Una vez creado el script, lo ejecutamos
    $script_expect = shell_exec("expect /var/www/html/hardening/public/oscap/temporales/".$exect_script_nombre);

    //Ejecutamos el script remotamente como root
    $comando = "sshpass -p ".$request->input("contrasena")." ssh -t ".$request->input("usuario")."@".$request->input("direccion_ip")." \"echo ".$request->input("contrasena")." | sudo -S sh /home/".$request->input("usuario")."/".$script_remediacion_nombre." 2>&1 \"";
    $resutlado_script_sh = shell_exec($comando);
    
    //Borramos del servidor remoto el script enviado y ejecutado
    $comando = "sshpass -p ".$request->input("contrasena")." ssh -t ".$request->input("usuario")."@".$request->input("direccion_ip")." \"echo ".$request->input("contrasena")." | sudo -S rm /home/".$request->input("usuario")."/".$script_remediacion_nombre."\"";
    $script_expect = shell_exec($comando);

    //Actualizamos el registro del escaneo
    $ruta_archivo_hardening = "/var/www/html/hardening/public/oscap/scripts-remediacion/".$script_remediacion_nombre;
    \DB::table('escaneos')
    ->where('id_escaneo', $request->input("id_escaneo"))
    ->update(['ruta_archivo_hardening' => $ruta_archivo_hardening,
              'hardening_ejecutado' => "1",
              'timestamp_ultimo_hardening' => $timestamp,
              'resultado_hardening' => $resutlado_script_sh,
              'updated_at' => $timestamp,
              'id_usuario_updated' => Auth::user()->id]);


    //Devolvemos la vsita con el resultado del comando
    return view("listados.listado_escaneos")
            ->with("mensaje_exito", "Hardening ejecutado.");
  }

  public function form_resultados_hardening($id_escaneo){
    //Obtenemos la ruta del archivo hardening
    $ruta_archivo_hardening = \DB::table('escaneos')
                ->where('id_escaneo', $id_escaneo)
                ->value('ruta_archivo_hardening');

    //Obtenemos solo el nombre del archivo hardening
    $ultima_aparicion = strrchr($ruta_archivo_hardening, "/");
    $archivo_hardening = substr($ultima_aparicion, 1);
    //Obtenemos la ruta del resultado del escaneo
    $resultado_hardening = \DB::table('escaneos')
                ->where('id_escaneo', $id_escaneo)
                ->value('resultado_hardening');

    return view("formularios.form_resultados_hardening")
          ->with('resultado_hardening', $resultado_hardening)
          ->with('archivo_hardening', $archivo_hardening);
  }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

}
