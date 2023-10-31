<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/', function () {
    return redirect('/login');
});


Route::group(["middleware" => "apikey.validate"], function () {

    //Rutas
    Route::get('indexAPI', 'ServiciosController@indexAPI');
    Route::get('getResultados', 'ServiciosController@getResultados');

  });

Route::group(['middleware' => 'cors'], function () {
});

Route::group(['middleware' => 'auth'], function () {

  Route::get('/home', 'HomeController@index')->name('home');

  //Rutas que necesitan permiso para gestionar personas
  Route::group(['middleware' => ['permissionshinobi:gestion_de_personas']], function () {
      //Gestion de Personas
      Route::get('listado_personas', 'PersonasController@listado_personas');
      Route::resource('buscar_persona', 'PersonasController@buscar_persona');
      Route::get('form_nueva_persona', 'PersonasController@form_nueva_persona');
      Route::post('nueva_persona', 'PersonasController@nueva_persona');
      Route::get('form_editar_persona/{id_persona}', 'PersonasController@form_editar_persona');
      Route::post('editar_persona', 'PersonasController@editar_persona');
      Route::get('form_baja_persona/{id_persona}', 'PersonasController@form_baja_persona');
      Route::post('baja_persona', 'PersonasController@baja_persona');
      Route::get('form_alta_persona/{id_persona}', 'PersonasController@form_alta_persona');
      Route::post('alta_persona', 'PersonasController@alta_persona');
 });

  //Rutas que necesitan permiso para gestionar usuarios
  Route::group(['middleware' => ['permissionshinobi:gestion_de_usuarios']], function () {
     //Gestion de usuarios
     Route::get('/listado_usuarios', 'UsuariosController@listado_usuarios');
     Route::resource('buscar_usuario', 'UsuariosController@buscar_usuario');
     Route::get('form_nuevo_usuario_buscar', 'UsuariosController@form_nuevo_usuario_buscar');
     Route::get('form_nuevo_usuario/{id_persona}', 'UsuariosController@form_nuevo_usuario');
     Route::post('nuevo_usuario', 'UsuariosController@nuevo_usuario');
     Route::get('consultaPersonaRegistradaCi/{ci}', 'PersonasController@consultaPersonaRegistradaCi');
     Route::get('consultaPersonaRegistradaNombre/{nombre}', 'PersonasController@consultaPersonaRegistradaNombre');
     Route::get('form_editar_usuario/{id}', 'UsuariosController@form_editar_usuario');
     Route::post('editar_acceso', 'UsuariosController@editar_acceso');
     Route::post('borrar_usuario', 'UsuariosController@borrar_usuario');
     Route::get('form_borrado_usuario/{idusu}', 'UsuariosController@form_borrado_usuario');
     Route::get('confirmacion_borrado_usuario/{idusuario}', 'UsuariosController@confirmacion_borrado_usuario');

     //La gestion de roles requiere además un permiso adicional
     Route::group(['middleware' => ['permissionshinobi:gestion_de_roles']], function () {
       Route::get('asignar_rol/{idusu}/{idrol}', 'UsuariosController@asignar_rol');
       Route::get('quitar_rol/{idusu}/{idrol}', 'UsuariosController@quitar_rol');
     });
  });

  //Rutas que necesitan permiso para administrar hardenning politicas, perfiles y reglas 
  Route::group(['middleware' => ['permissionshinobi:gestion_de_agua']], function () {
    //Politicas, perfiles y reglas 
    Route::get('listado_politicas', 'HardeningController@listado_politicas');
    Route::resource('buscar_politicas', 'HardeningController@buscar_politicas');    
    Route::get('form_nueva_politica', 'HardeningController@form_nueva_politica');
    Route::post('nueva_politica', 'HardeningController@nueva_politica');
    Route::get('form_nuevo_perfil', 'HardeningController@form_nuevo_perfil');
    Route::post('nuevo_perfil', 'HardeningController@nuevo_perfil');
    Route::get('form_editar_reglas/{id}', 'HardeningController@form_editar_reglas');
    Route::post('guardado_preventivo_reglas', 'HardeningController@guardado_preventivo_reglas')->name('guardado_preventivo_reglas');
    Route::get('listado_clientes', 'HardeningController@listado_clientes');
    Route::resource('buscar_clientes', 'HardeningController@buscar_clientes');    
    Route::get('form_nuevo_cliente', 'HardeningController@form_nuevo_cliente');
    Route::post('nuevo_cliente', 'HardeningController@nuevo_cliente');
    Route::get('form_editar_cliente/{id}', 'HardeningController@form_editar_cliente');
    Route::post('editar_cliente', 'HardeningController@editar_cliente');
    Route::get('listado_clientes_perfiles', 'HardeningController@listado_clientes_perfiles');
    Route::resource('buscar_clientes_perfiles', 'HardeningController@buscar_clientes_perfiles');    
    Route::get('listado_escaneos', 'HardeningController@listado_escaneos');
    Route::get('form_nuevo_escaneo/{id}', 'HardeningController@form_nuevo_escaneo');
    Route::post('nuevo_escaneo', 'HardeningController@nuevo_escaneo');
    Route::resource('buscar_escaneos', 'HardeningController@buscar_escaneos');    
    Route::get('form_resultados_escaneo/{id}', 'HardeningController@form_resultados_escaneo');
    Route::get('form_hardening/{id}', 'HardeningController@form_hardening');
    Route::post('hardening', 'HardeningController@hardening');   
    Route::get('form_resultados_hardening/{id}', 'HardeningController@form_resultados_hardening');
  });

 //REVISAR...
 Route::get('import_contacto', 'Excel\ImportPersonaController@index')->name('import_contacto');
 Route::post('import_contacto', 'Excel\ImportPersonaController@import')->name('guardar_import_contacto');
});
