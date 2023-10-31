<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\User;
use Illuminate\Support\Facades\Validator;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\Permission;

use Auth;
use DateTime;

use App\Persona;

use App\Personal;
use App\Unidad;
use App\Cargo;
use App\Usada;
use App\Calificacion;
use App\Gestion;

use Datatables;
class UsuariosController extends Controller
{
  public function listado_usuarios(){
      //presenta un listado de usuarios paginados de 10 en 10
  	$usuarios=User::where('activo', 1)->paginate(10);
  	return view("listados.listado_usuarios")->with("usuarios",$usuarios);
  }

  public function buscar_usuario(){
    return Datatables::of(\DB::table('users')
                          ->select(
                              'id as id_usuario', 'name',
                              \DB::raw('(SELECT CONCAT(nombre, " ", paterno, " ", materno) FROM personas WHERE id_persona = users.id_persona ORDER BY id_persona DESC LIMIT 1) as persona'),
                              \DB::raw('(SELECT GROUP_CONCAT(r.description) FROM role_user AS ru
                                                                   LEFT JOIN roles AS r
                                                                        ON ru.role_id = r.id
                                                                    WHERE ru.user_id = users.id
                                                                      AND users.activo = 1
                                                                    GROUP BY ru.user_id)
                              as roles')
                            )
                          ->where('activo', 1)
                          //->select('*', 'id_unidad_funcional_tipo as tipo')
                          ->get()
                         )->make(true);
  }


  public function form_nuevo_usuario_buscar(){
      return view("formularios.form_nuevo_usuario_buscar");
  }

  public function form_nuevo_usuario($id_persona){
      //Verificar si ya existe el usuario
      //$usuario=User::find($id);
      //$roles=Role::all();
      $roles = \DB::table('roles')
      ->where('estado', 1)
      ->get();
      return view("formularios.form_nuevo_usuario")
          //->with("usuario",$usuario)
  	      ->with("roles",$roles)
  	      ->with("id_persona",$id_persona);
  }

  public function nuevo_usuario(Request $request){
    $usuario_existe = \DB::table('users')
    ->where('name', $request->input("name"))
    ->where('activo', 1)
    ->value('id');

    //Si ya existe enviamos el error
    if($usuario_existe != ""){
      $mensaje_error = "EL usuario no se pudo registrar debido a que ya existe un registro con nombre de usuario ".$request->input("name");
      $usuarios=User::where('activo', 1)->paginate(100);
      return view("listados.listado_usuarios")
        ->with("usuarios",$usuarios)
        ->with("mensaje_error",$mensaje_error);
    }
    else {
      $usuario=new User;
      $usuario->name=strtolower($request->input("name"));
      $usuario->email = strtolower($request->input("name"));
      $usuario->password= bcrypt($request->input("password"));
      $usuario->id_persona=$request->id_persona;
      $usuario->activo=1;

      $usuario->save();

      $roles = $request->rol;
      if (!empty($roles)) {
        for ($i=0; $i < count($roles); $i++) {
          \DB::table('role_user')->insert([
              ['role_id' => $roles[$i],
               'user_id' => $usuario->id]
          ]);
        }
      }
      //Devolvemos a la pantalla con el mensaje exitoso
      $usuarios=User::where('activo', 1)->paginate(100);
      return view("listados.listado_usuarios")
        ->with("usuarios",$usuarios)
        ->with("mensaje_exito","Usuario creado exitosamente.");
    }
  }

  public function form_editar_usuario($id){
      $usuario=User::find($id);
      //$roles=Role::all();
      $roles = \DB::table('roles')
      ->where('estado', 1)
      ->get();
      return view("formularios.form_editar_usuario")
        ->with("usuario",$usuario)
  	    ->with("roles",$roles);
  }

  public function form_editar_mis_datos_usuario(){
      $usuario=User::find(Auth::user()->id);

      return view("formularios.form_editar_mis_datos_usuario")
        ->with("usuario",$usuario);
  }

  public function editar_mis_datos_acceso(Request $request){
      $idusuario=Auth::user()->id;
      $usuario=User::find($idusuario);

      if ($usuario->name != $request->name) {
          if( User::where('name', '=', $request->name)->where('activo', 1)->exists()){
              return view("mensajes.mensaje_error")->with("msj","El nombre de usuario ya se encuentra registrado y en uso.") ;
          }
      }

      $usuario->name=$request->input("name");
      $usuario->email=$request->input("password");
      $usuario->password= bcrypt( $request->input("password") );
      if( $usuario->save()){
          return view("mensajes.msj_usuario_actualizado")->with("msj","Usuario actualizado correctamente")->with("idusuario",$idusuario) ;
      }
      else
      {
          return view("mensajes.mensaje_error")->with("msj","Hubo un error al editar el usuario, intentarlo nuevamente.") ;
      }
  }

  public function editar_acceso(Request $request){
      $idusuario=$request->input("id_usuario");
      $usuario=User::find($idusuario);

      if ($usuario->name != $request->name) {
          if( User::where('name', '=', $request->name)->where('activo', 1)->exists()){
              return view("mensajes.mensaje_error")->with("msj","El nombre de usuario ya se encuentra registrado y en uso.") ;
          }
      }

      $usuario->name=$request->input("name");
      $usuario->email=$request->input("password");
      $usuario->password= bcrypt( $request->input("password") );
      if( $usuario->save()){
          return view("mensajes.msj_usuario_actualizado")->with("msj","Usuario actualizado correctamente")->with("idusuario",$idusuario) ;
      }
      else
      {
          return view("mensajes.mensaje_error")->with("msj","Hubo un error al editar el usuario, intentarlo nuevamente.") ;
      }
  }

  public function asignar_rol($idusu,$idrol){
      $usuario=User::find($idusu);
      $usuario->assignRole($idrol);
      $usuario=User::find($idusu);
      $rolesasignados=$usuario->getRolesDescription();
      return json_encode ($rolesasignados);
  }

  public function quitar_rol($idusu,$idrol){
      $usuario=User::find($idusu);
      $usuario->revokeRole($idrol);
      $rolesasignados=$usuario->getRolesDescription();
      return json_encode ($rolesasignados);
  }

  public function form_borrado_usuario($id){
    if($id == 1){
      return view("mensajes.mensaje_error")->with("msj","No se puede eliminar al usuario Super Administrador");
    }

    $usuario=User::find($id);
    return view("confirmaciones.form_borrado_usuario")->with("usuario",$usuario);
  }

  public function borrar_usuario(Request $request){
    $idusuario=$request->input("id_usuario");
    $usuario=User::find($idusuario);
    $usuario->activo="0";

    if($usuario->save()){
        //Borrar los roles que tenia asignado el usuario
        //DB::delete('delete from student where id = ?',[$id]);fabri
        $roles=Role::all();
        foreach ($roles as $rol) {
          $usuario->revokeRole($rol->id);
        }
       return view("mensajes.msj_usuario_borrado")->with("msj","Usuario borrado correctamente") ;
    }
    else    {
      return view("mensajes.mensaje_error")->with("msj","Hubo un error al borrar el usuario.");
    }
  }


































/*
NO BORRAR, PUEDE SERIVIR PARA GESTION DE ROLES Y PERMISOS

public function form_agregar_usuario(){
    //carga el formulario para agregar un nuevo usuario
    return view("formularios.form_agregar_usuario");
}

public function form_nuevo_usuario_anterior(){
    //carga el formulario para agregar un nuevo usuario
    $cargos=Cargo::all();
    $unidades=Unidad::all();
    $roles=Role::all();

    return view("formularios.form_nuevo_usuario")->with("roles", $roles)
            ->with('unidades', $unidades)
            ->with('cargos', $cargos);
}

public function reporte_usuarios(){
    $usuarios=User::where('activo', 1)->paginate(100);
    $vac_tomadas =Usada::all();
    // $cas=Calificacion::all();
    $usuarios = \DB::table('personas')
        ->join('users', 'personal.cedula', '=', 'users.ci')
        // ->where('personal.fechabaja', '>', '0000-00-00')
        ->select('users.id as id_usuario', 'personal.fechaingreso', 'personal.item',
                'users.nombre', 'users.paterno','users.materno', 'users.ci',
                'personal.fechabaja', 'personal.haber', 'areas.*',
                'unidades.nombre as unidad', 'unidades.id as idunidad',
         'direcciones.nombre as direccion')
        ->get();
        return view("listados.reporte_usuarios")->with("usuarios",$usuarios)
        ->with('vac_tomadas', $vac_tomadas);
}

// public function form_agregar_usuario(){
//     //carga el formulario para agregar un nuevo usuario
//     $unidades=Unidad::all();
//     $roles=Role::all();
//     return view("formularios.form_agregar_usuario")->with("roles",$roles)->with('unidades', $unidades);
// }

public function listado_usuarios_duo(){
    //presenta un listado de usuarios paginados de 100 en 100
    $usuarios=User::where('activo', 1)->paginate(100);
    $vac_tomadas =Usada::all();
    $ultima_gestion = Gestion::orderBy('id', 'asc')->get();
    $cas = Gestion::all();
    $usuarios = \DB::table('personal')
        ->join('users', 'personal.cedula', '=', 'users.ci')
        ->join('areas', 'personal.idarea', '=', 'areas.idarea')
        ->join('unidades', 'areas.idunidad', '=', 'unidades.id')
        ->join('direcciones', 'areas.iddireccion', '=', 'direcciones.id')
        ->where('personal.fechabaja', '>', '0000-00-00')
        ->select('users.id as id_usuario', 'personal.fechaingreso', 'personal.item',
                'users.nombre', 'users.paterno','users.materno', 'users.ci',
                'personal.fechabaja', 'personal.haber', 'areas.*',
                'unidades.nombre as unidad', 'unidades.id as idunidad',
         'direcciones.nombre as direccion')
        ->get();
        return view("listados.listado_usuarios_duo")->with("usuarios",$usuarios)
        ->with('cas', $cas)
        ->with('vac_tomadas', $vac_tomadas)
        ->with('ultima_gestion',$ultima_gestion);
}

public function buscar_usuario_duo(Request $request){
	$dato=$request->input("dato_buscado");
    $vac_tomadas =Usada::all();
    $cas = Gestion::all();
    $usuarios = \DB::table('personal')
        ->join('users', 'personal.cedula', '=', 'users.ci')
        ->join('areas', 'personal.idarea', '=', 'areas.idarea')
        ->join('unidades', 'areas.idunidad', '=', 'unidades.id')
        ->join('direcciones', 'areas.iddireccion', '=', 'direcciones.id')
        ->where('personal.fechabaja', '>', '0000-00-00')
        ->where("users.nombre","like","%".$dato."%")
        ->orwhere("users.paterno","like","%".$dato."%")
        ->orwhere("users.materno","like","%".$dato."%")
        ->orwhere("users.ci","like","%".$dato."%")
        ->select('users.id as id_usuario', 'personal.fechaingreso', 'personal.item',
                'users.nombre', 'users.paterno','users.materno', 'users.ci',
                'personal.fechabaja', 'personal.haber', 'areas.*',
                'unidades.nombre as unidad', 'unidades.id as idunidad',
         'direcciones.nombre as direccion')
        ->get();
        return view("listados.listado_usuarios_duo")->with("usuarios",$usuarios)
        ->with('vac_tomadas', $vac_tomadas)->with('cas', $cas);
}

public function form_nuevo_rol(){
    //carga el formulario para agregar un nuevo rol
    $roles=Role::all();
    return view("formularios.form_nuevo_rol")->with("roles",$roles);
}

public function form_nuevo_permiso(){
    //carga el formulario para agregar un nuevo permiso
     $roles=Role::all();
     $permisos=Permission::all();
    return view("formularios.form_nuevo_permiso")->with("roles",$roles)->with("permisos", $permisos);
}

public function crear_usuario(Request $request){
    //crea un nuevo usuario en el sistema
	$reglas=[  'nombre' => 'required',
               'paterno' => 'required_without:materno',
               'materno' => 'required_without:paterno',
               'ci' => 'required|unique:users',
               'telefono' => 'required|numeric',
               'password' => 'required|min:4|confirmed',
               'email' => 'required|email|unique:users',
               'fechaingreso' => 'required',
            //    'item' => 'required|numeric|min:1',
            //    'haber' => 'required|numeric|min:1',
            ];

	$mensajes=['nombre.required' => 'El nombre es obligatorio',
               'paterno.required' => 'El apellido es obligatorio',
               'materno.required' => 'El apellido es obligatorio',
               'ci.required' => 'El C.I. es obligatorio',
               'ci.unique' => 'El C.I. ya se encuentra registrado',
               'telefono.numeric' => 'El telefono debe contener solo numeros',
               'password.min' => 'El password debe tener al menos 4 caracteres',
               'email.unique' => 'El email ya se encuentra registrado en la base de datos',
               'fechaingreso.required' => 'La fecha de ingreso es obligatoria',
               'haber.numeric' => 'El haber debe contener solo numeros',
                ];

	$validator = Validator::make( $request->all(),$reglas,$mensajes );
	if( $validator->fails() ){
        $unidades=Unidad::all();
        $cargos=Cargo::all();
          return view("formularios.form_nuevo_usuario")
            ->with('unidades', $unidades)
            ->with('cargos', $cargos)
            ->withErrors($validator)
            ->withInput($request->flash());
	}

	$usuario=new User;
    $usuario->name=strtoupper( $request->input("nombre"));
    $usuario->email=$request->input("email");
    $usuario->password= bcrypt( $request->input("password") );
    $usuario->ci=$request->input("ci");
	$usuario->nombre=strtoupper($request->input("nombre") ) ;
    $usuario->paterno=strtoupper($request->input("paterno") ) ;
    $usuario->materno=strtoupper($request->input("materno") ) ;
    $usuario->telefono=$request->input("telefono");

    if($usuario->save())
    {
        $usuario=User::find($usuario->id);
        $usuario->assignRole(4);//Funcionario

        if($request->input('tipoUsuario') == 3){
            $usuario=User::find($usuario->id);
            $usuario->assignRole(3);//Jefe de Unidad
        }

        $personal=new Personal;
        $personal->cedula=$request->input('ci');
        $personal->domicilio=strtoupper($request->input("domicilio"));
        // $personal->item=$request->input("item");
        $personal->idarea=$request->input("area");
        $personal->id_cargo=$request->input("cargo");
        $personal->fechaingreso=$request->input("fechaingreso");
        // $personal->haber=$request->input("haber");

        if($personal->save()){
        // return json_encode ($rolesasignados);

        $gestion=new Gestion;
        $ingreso = $request->input("fechaingreso");
        $hasta = suma_anios($ingreso, 1);

        $inicio = new DateTime($request->input("fechaingreso"));
        $hasta = new DateTime($hasta);
        $antiguedad = $inicio->diff($hasta);
        $a = $antiguedad->y. 'a ';
        $m = $antiguedad->m. 'm ';
        $d = $antiguedad->d. 'd ';

        $gestion->id_usuario=$usuario->id;
        $gestion->desde=$ingreso;
        $gestion->hasta=$hasta;
        $gestion->vigencia=suma_anios($ingreso, 3);
        $gestion->year=0;
        $gestion->month=0;
        $gestion->day=0;
        $gestion->computo=0;
        $gestion->saldo=escala($a, $m, $d);

        $gestion->save();

        return view("mensajes.msj_usuario_creado")->with("msj","Usuario agregado correctamente") ;
        }

    }
    else
    {
        return view("mensajes.mensaje_error")->with("msj","...Hubo un error al agregar ;...") ;
    }

}


public function crear_rol(Request $request){

    $reglas=[    'rol_nombre' => 'required|alpha',
                 'rol_slug' => 'required|unique:roles,slug',
                 'rol_descripcion' => 'required',
            ];

    $mensajes=[  'rol_nombre.alpha' => 'solo se permiten letras en el nombre, sin espacios , ni simbolos',
                 'rol_slug.unique' => 'el slug debe ser unico',
                 'rol_descripcion.required' => 'la descripcion es obligatoria',
            ];

    $validator = Validator::make( $request->all(),$reglas,$mensajes );
    if( $validator->fails() ){

        return new JsonResponse($validator->errors(), 422);
    }

   $rol=new Role;
   $rol->name=$request->input("rol_nombre") ;
   $rol->slug=$request->input("rol_slug") ;
   $rol->description=$request->input("rol_descripcion") ;
    if($rol->save())
    {
        return view("mensajes.msj_rol_creado")->with("msj","Rol agregado correctamente") ;
    }
    else
    {
        return view("mensajes.mensaje_error")->with("msj","...Hubo un error al agregar ;...") ;
    }
}

public function crear_permiso(Request $request){

   $permiso=new Permission;
   $permiso->name=$request->input("permiso_nombre") ;
   $permiso->slug=$request->input("permiso_slug") ;
   $permiso->description=$request->input("permiso_descripcion") ;
    if($permiso->save())
    {
        return view("mensajes.msj_permiso_creado")->with("msj","Permiso creado correctamente") ;
    }
    else
    {
        return view("mensajes.mensaje_error")->with("msj","...Hubo un error al agregar ;...") ;
    }
}

public function asignar_permiso(Request $request){

    $roleid=$request->input("rol_sel");
    $idper=$request->input("permiso_rol");
    $rol=Role::find($roleid);
    $rol->assignPermission($idper);

    if($rol->save())
    {
        return view("mensajes.msj_permiso_creado")->with("msj","Permiso asignado correctamente") ;
    }
    else
    {
        return view("mensajes.mensaje_error")->with("msj","...Hubo un error al agregar ;...") ;
    }
}






public function buscar_usuario(Request $request){
	$dato=$request->input("dato_buscado");
    $usuarios=User::join('personas', 'users.id_persona', 'personas.id_persona')
    ->where("name","like","%".$dato."%")
    ->orwhere("nombre","like","%".$dato."%")
    ->orwhere("paterno","like","%".$dato."%")
    ->orwhere("materno","like","%".$dato."%")
    ->orwhere("cedula_identidad","like","%".$dato."%")
    ->orwhere("users.email","like","%".$dato."%")
    ->select('users.*','personas.nombre', 'personas.paterno', 'personas.materno', 'personas.cedula_identidad')
    ->paginate(100);
	return view('listados.listado_usuarios')->with("usuarios",$usuarios);
}
















public function quitar_permiso($idrole,$idper){

    $role = Role::find($idrole);
    $role->revokePermission($idper);
    $role->save();

    return "ok";
}


public function borrar_rol($idrole){

    $role = Role::find($idrole);
    $role->delete();
    return "ok";
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
}

public function ObtieneUsuarioMd5 ($circ, $distrito, $recinto)
{
    $circ = $circ."-".$distrito."-".$recinto;
    $numero = 0;

    $exp_reg="[^A-Z0-9]";
    $longitud = 4;
    $codigo = substr(preg_replace($exp_reg, "", md5($circ)).preg_replace($exp_reg, "", md5($distrito)).preg_replace($exp_reg, "", md5($recinto)),
    0, $longitud);

    // $codigo = strtoupper(chr($i));
    $username = strtolower($circ."-".$codigo);

    while (User::where('name', '=', $username)->exists()) { // user found
        $username=$username.$numero;
        $numero++;
    }
    return $username;
}
*/
}
