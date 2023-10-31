$(document).ready(function(){

  if (document.getElementById("clock")) {

    var countDownDate = new Date("Mar 7, 2021 00:00:01").getTime();

    /* Update the count down every 1 second */
    var x = setInterval(function() {

      // Get today's date and time
      var now = new Date().getTime();

      // Find the distance between now and the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Display the result in the element with id="clock"
      document.getElementById("clock").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s Restantes";

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("clock").innerHTML = "Día decisivo";
      }
    }, 1000);
  }


//
  $('#btn_vaciar').click(function(){
    var div_resul="div_notificacion_sol";
    $.ajax({
      type: "POST",
      url: "truncate",
      data: {},
      success: function(resul)
      {
          if (resul == 'ok') {
            alertify.success('listo Bro!');
          }
      },
      error : function(xhr, status) {
          $("#"+div_resul+"").html('ha ocurrido un error al agregar el usuario, revise su conexion e intentelo nuevamente');
      }
    });
  });

    //CALENDARIO
    $("#tablajson tbody").html("");
    $("#div_calendar").hide()

    $('#btn-calendar').click(function(){
      $("#div_calendar").show();
      $('#btn-pdf').show()
      $('#btn-cancelar').show()
      limpiar();
      var id_sol = $("#id_solicitud").val();
      calendario();

      $("#btn-calendar").hide();
    });


    $('#btn-pdf').click(function(){
        id_sol = $("#id_solicitud").val()
        $.ajax({
          type:'get',
          url:"agregar_solicitud",
          data:{'id_sol':id_sol},
          success: function(result){
            if (result == 'error') {
              alert("No se pudo realizar la petición");
            }
            else if(result == 'vacio'){
              alert('No seleccionó ninguna fecha..!');
            }
            else if(result == 'ok'){
            recargar();
            // alert($("#id_solicitud").val());
            }
            else
            {
              alert("Ocurrió un error, revise su conexión");
            }
          }
      });
    });


      var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
      var f=new Date();
      // document.write(f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear());
      $("#hoy").text(f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear());

  });

  function refrescar(){
    timout=setTimeout(function(){
        location.reload();
    },3000,"JavaScript");//3 segundos
  }

  function recargar(){
    timout=setTimeout(function(){
        location.reload();
    },0,"JavaScript");//3 segundos
  }

  function refresh_calendar(){
    $('#btn_guarda_fecha').attr("disabled", false);
    var events = {
        url: 'calendar_datos',
        type: 'GET', // Send post data
        error: function() {
            alert('No se encontró ninguna fecha.');
        }
    };
  //   var events;
  //   $.ajax({
  //   type:'get',
  //   url:"calendar_datos",
  //   success: function(result){
  //       if (result == 'error') {
  //       alert("No se pudo realizar la petición");
  //       }
  //       else if(result == 'ok'){
  //           alert("ok petición");
  //       }
  //       else{
  //           defaultEvents = result;
  //       }
  //   }
  // });

    $('#calendar').fullCalendar('removeEventSource', events);
    $('#calendar').fullCalendar('addEventSource', events);
    $('#calendar').fullCalendar('refetchEvents');
  }

  function refresh_calendar_feriado(){
    $('#btn_guarda_fecha').attr("disabled", false);
    var events = {
        url: 'calendario_feriados',
        type: 'GET', // Send post data
        error: function() {
            alert('No se encontró ninguna fecha.');
        }
    };

    $('#calendario_feriados').fullCalendar('removeEventSource', events);
    $('#calendario_feriados').fullCalendar('addEventSource', events);
    $('#calendario_feriados').fullCalendar('refetchEvents');
  }

  function refresh_calendar_emergencias(id_sol){
    var events = {
        url: 'calendar_datos_emergencias/'+id_sol,
        type: 'GET', // Send post data
        error: function() {
            alert('No se encontró ninguna fecha.');
        }
    };

    $('#calendar_emergencias').fullCalendar('removeEventSource', events);
    $('#calendar_emergencias').fullCalendar('addEventSource', events);
    $('#calendar_emergencias').fullCalendar('refetchEvents');
  }


  function formato(fecha){
    return fecha.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
  }

  function editar_mis_datos(form){
    var urlraiz=$("#url_raiz_proyecto").val();
    if(form == 1){var miurl =urlraiz+"/form_editar_mis_datos_persona"; }
    if(form == 2){var miurl =urlraiz+"/form_editar_mis_datos_usuario"; }

  	$("#capa_modal").show();
  	$("#capa_formularios").show();
  	var screenTop = $(document).scrollTop();
  	$("#capa_formularios").css('top', screenTop);
    $("#capa_formularios").html($("#cargador_empresa").html());

      $.ajax({
      url: miurl
      }).done( function(resul)
      {
       $("#capa_formularios").html(resul);

      }).fail( function()
     {
      $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
     }) ;
  }

function verinfo_persona(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_persona/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_baja_persona/"+id+""; }
  if(form == 3){var miurl =urlraiz+"/form_alta_persona/"+id+""; }


  if (form == 30) {

    alertify.success('id_persona:'+id_persona);
    $.ajax({
      type:'POST',
      url:"liberar_responsabilidad", // sending the request to the same page we're on right now
      data:{'id_persona':id_persona},
         success: function(result){
              if (result == 'ok') {
                location.reload()
              }
              else{
                $(div_resul).html(result);
              }
          }
      })
  }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}

function  verinfo_usuario(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 0){var miurl =urlraiz+"/form_nuevo_usuario/"+id+""; }
  if(form == 1){var miurl =urlraiz+"/form_editar_usuario/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}

function verinfo_reglas(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_reglas/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}

function verinfo_cliente(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_cliente/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_nuevo_escaneo/"+id+""; }
  

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}

function verinfo_escaneo(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_resultados_escaneo/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_hardening/"+id+""; }
  if(form == 3){var miurl =urlraiz+"/form_resultados_hardening/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}


function verinfo_unidad_funcional(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_unidad_funcional/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_baja_unidad_funcional/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}

function verinfo_factruas_agua(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_factura_agua/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_baja_factura_agua/"+id+""; }
  if(form == 3){var miurl =urlraiz+"/form_alta_factura_agua/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;
}

function verinfo_cobros(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){
    var id_persona_responsable_pago = $("#id_persona_responsable_pago").val();
    var periodo_pago_mes = $("#periodo_pago_mes").val();
    var periodo_pago_ano = $("#periodo_pago_ano").val();
    var fecha_entrega = $("#fecha_entrega").val();
    var miurl =urlraiz+"/nuevo_cobro_aviso_y_editar/"+id_persona_responsable_pago+"/"+periodo_pago_mes+"/"+periodo_pago_ano+"/"+fecha_entrega+"";
    if(id_persona_responsable_pago == "" || periodo_pago_ano == "" || periodo_pago_mes == "" || fecha_entrega == ""){
      return alertify.error('Debe llenar todos los campos');
    }
  }

  if(form == 2){var miurl =urlraiz+"/form_editar_cobro_aviso/"+id+""; }
  if(form == 3){var miurl =urlraiz+"/form_baja_cobro_aviso/"+id+""; }
  if(form == 4){var miurl =urlraiz+"/form_alta_cobro_aviso/"+id+""; }
  if(form == 5){window.open('pdf_cobro_aviso/1');}
  if(form == 6){
    var periodo_pago_mes = $("#periodo_pago_mes").val();
    var periodo_pago_ano = $("#periodo_pago_ano").val();
    var fecha_entrega = $("#fecha_entrega").val();
    var miurl =urlraiz+"/form_nuevo_cobro_aviso_masivo_verifica_confirma/"+periodo_pago_mes+"/"+periodo_pago_ano+"/"+fecha_entrega+"";
    if(periodo_pago_ano == "" || periodo_pago_mes == "" || fecha_entrega == ""){
      return alertify.error('Debe llenar todos los campos');
    }
  }
  if(form == 7){
    var periodo_pago_mes = $("#periodo_pago_mes").val();
    var periodo_pago_ano = $("#periodo_pago_ano").val();
    var fecha_entrega = $("#fecha_entrega").val();
    var miurl =urlraiz+"/nuevo_cobro_aviso_masivo/"+periodo_pago_mes+"/"+periodo_pago_ano+"/"+fecha_entrega+"";
    if(periodo_pago_ano == "" || periodo_pago_mes == "" || fecha_entrega == ""){
      return alertify.error('Debe llenar todos los campos');
    }
  }
  if(form == 8){var miurl =urlraiz+"/form_nuevo_cobro/"+id+""; }
  if(form == 9){
    var id_cobro_aviso = $("#id_cobro_aviso").val();
    var transaccion_nro = $("#transaccion_nro").val();
    var transaccion_hora = $("#transaccion_hora").val();
    var transaccion_fecha = $("#transaccion_fecha").val();
    var miurl =urlraiz+"/nuevo_cobro/"+id_cobro_aviso+"/"+transaccion_nro+"/"+transaccion_fecha+"/"+transaccion_hora+"";
    if(id_cobro_aviso == "" || transaccion_nro == "" || transaccion_fecha == "" || transaccion_hora == ""){
      return alertify.error('Debe llenar todos los campos');
    }
  }
  if(form == 10){var miurl =urlraiz+"/form_editar_cobro/"+id+""; }
  if(form == 11){var miurl =urlraiz+"/form_baja_cobro/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;">...Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo...</span>');
   }) ;
}

function verinfo_gastos(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_gasto/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_baja_gasto/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;">...Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo...</span>');
   }) ;
}

function verinfo_ingresos(id, form){
  var urlraiz=$("#url_raiz_proyecto").val();
  if(form == 1){var miurl =urlraiz+"/form_editar_ingreso/"+id+""; }
  if(form == 2){var miurl =urlraiz+"/form_baja_ingreso/"+id+""; }

	$("#capa_modal").show();
	$("#capa_formularios").show();
	var screenTop = $(document).scrollTop();
	$("#capa_formularios").css('top', screenTop);
  $("#capa_formularios").html($("#cargador_empresa").html());

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;">...Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo...</span>');
   }) ;
}


$(document).on("click",".div_modal", function(e){
	$(this).hide();
	$("#capa_formularios").hide();
	$("#capa_formularios").html("");
})

$(document).on("click","#cerrar_modal", function(e){
  $("#capa_modal").hide();
  $("#capa_formularios").hide();
})

document.onkeydown = function(evt) {
  evt = evt || window.event;
  if (evt.keyCode == 27) {
    $("#capa_modal").hide();
    $("#capa_formularios").hide();
  }
};

function cargar_formulario(arg){
   var urlraiz=$("#url_raiz_proyecto").val();
   $("#capa_modal").show();
   $("#capa_formularios").show();
   var screenTop = $(document).scrollTop();
   $("#capa_formularios").css('top', screenTop);
   $("#capa_formularios").html($("#cargador_empresa").html());
   //if(arg==1){ var miurl=urlraiz+"/form_nuevo_usuario"; }
   if(arg==1){ var miurl=urlraiz+"/form_nueva_persona"; }
   if(arg==2){ var miurl=urlraiz+"/form_nuevo_rol"; }
   if(arg==3){ var miurl=urlraiz+"/form_nuevo_permiso"; }
   if(arg==4){ var miurl=urlraiz+"/form_nueva_gestion"; }
   if(arg==5){ var miurl=urlraiz+"/form_nueva_unidad_funcional"; }
   if(arg==6){ var miurl=urlraiz+"/form_nueva_factura_agua"; }
   if(arg==7){ var miurl=urlraiz+"/form_nuevo_cobro_aviso"; }
   if(arg==8){ var miurl=urlraiz+"/form_nuevo_cobro_aviso_masivo"; }
   if(arg==9){ var miurl=urlraiz+"/form_impresion_masiva_cobro_aviso"; }
   if(arg==10){ var miurl=urlraiz+"/form_nuevo_cobro_buscar"; }
   if(arg==11){ var miurl=urlraiz+"/form_nuevo_gasto"; }
   if(arg==12){ var miurl=urlraiz+"/form_detalle_movimientos"; }
   if(arg==13){ var miurl=urlraiz+"/form_deudores_morosos"; }
   if(arg==14){ var miurl=urlraiz+"/form_editar_datos_edificio"; }
   if(arg==15){ var miurl=urlraiz+"/form_repetir_gastos_fijos"; }
   if(arg==16){ var miurl=urlraiz+"/form_nuevo_usuario_buscar"; }
   if(arg==17){ var miurl=urlraiz+"/form_nuevo_ingreso"; }
   
   if(arg==101){ var miurl=urlraiz+"/form_nueva_politica"; }
   if(arg==102){ var miurl=urlraiz+"/form_nuevo_perfil"; }
   if(arg==103){ var miurl=urlraiz+"/form_nuevo_cliente"; }
   if(arg==104){ var miurl=urlraiz+"/form_nuevo_escaneo"; }


    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function()
   {
    $("#capa_formularios").html('<span style="color:#FA8E88;"> Ha ocurrido un error, revise su conexión a internet y vuelva a intentarlo.</span>');
   }) ;

}

$(document).on("submit",".formentrada",function(e){

  var id_sol = $("#id_solicitud").val();
  e.preventDefault();
  $('#btn_guarda_fecha').attr("disabled", true);
  $('#ModalAdd').modal('hide');
  $('#ModalEdit').modal('hide');

  var quien=$(this).attr("id");
  var formu=$(this);
  var varurl="";

  if(quien=="f_asignar_mesas_recinto"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_asignar_usuario_mesa"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_agregar_persona"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_editar_persona"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_editar_persona_asignacion"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_baja_persona"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}

  if(quien=="f_enviar_gastronomia"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_visitante"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_literatura"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_turismo"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_productores"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_enviar_artesania"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}

  if(quien=="f_editar_solicitud"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_editar_gestion"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";}
  if(quien=="f_crear_gestion"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";}
  if(quien=="f_editar_tiempo"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_agregar_fechas"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_modal";  }
  if(quien=="f_autorizar_solicitud"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";  }
  if(quien=="f_aprobar_solicitud"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";  }
  if(quien=="f_crear_solicitud"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";  }
  if(quien=="f_crear_usuario"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";  }
  if(quien=="f_crear_permiso"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";  }
  if(quien=="f_editar_usuario"){  var varurl=$(this).attr("action");  var div_resul="notificacion_E2";  }
  if(quien=="f_editar_acceso"){  var varurl=$(this).attr("action");  var div_resul="notificacion_E3";  }
  if(quien=="f_borrar_usuario"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";  }
  if(quien=="f_asignar_permiso"){  var varurl=$(this).attr("action");  var div_resul="capa_formularios";  }
  if(quien=="f_agregar_feriado"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";}
  if(quien=="f_editar_feriado"){  var varurl=$(this).attr("action");  var div_resul="div_notificacion_sol";  }


  if(quien=="f_guardado_preventivo_reglas"){  alertify.success("Reglas guardadas correctamente, puede continuar"); var varurl=$(this).attr("action");  }
  
  
  if(quien=="f_guardado_preventivo_agua_lecturas"){  alertify.success("Lecturas guardadas correctamente, puede continuar"); var varurl=$(this).attr("action");  }
  if(quien=="f_editar_factura_agua"){  alertify.success("Datos de la factura editados correctamente, puede continuar"); var varurl=$(this).attr("action");  }
  if(quien=="f_nuevo_cobro_aviso"){  alertify.success("Datos de la factura editados correctamente, puede continuar"); var varurl=$(this).attr("action");  }
  if(quien=="f_borrar_cobro_aviso_concepto"){
    alertify.success("Ítem eliminado correctamente");
    var varurl=$(this).attr("action");
  }



  $.ajax({
    // la URL para la petición
    url : varurl,
    data : formu.serialize(),
    type : 'POST',
    dataType : 'html',

    success : function(resul) {

      if(quien=="f_baja_persona"){
        if (resul == 'ok') {
          recargar();
        }
        else if(resul == 'failed'){
          $("#"+div_resul+"").html('ha ocurrido un error, revise su conexion e intentelo nuevamente');
        }
      }else if(quien=="f_enviar_agregar_persona" || quien=="f_enviar_editar_persona" || quien=="f_enviar_editar_persona_asignacion"){
        if (resul == 'failed') {
          alertify.success('Ocurrió un error, revise su conexión');
        }else if(resul == 'apellido'){
          alertify.error('Debe ingresar al menos un apellido');
        }else if(resul == 'cedula_repetida'){
          alertify.error('El número de Carnet ya se encuentra registrado!');
        }else if(resul == 'rol'){
        alertify.error('Seleccione una Tarea');
        }else if(resul == 'recinto'){
          alertify.error('Seleccione Circ. Distrito y Recinto');
        }else if(resul == 'id_vehiculo'){
          alertify.error('Seleccione un vehículo');
        }else if(resul == 'id_casa_campana'){
          alertify.error('Seleccione su Casa de Campaña');
        }else if(resul == 'mesas'){
          alertify.error('Seleccione las Mesas');
        }else if(resul == 'distrito'){
          alertify.error('Seleccione un Distrito');
        }else if(resul == 'circunscripcion'){
          alertify.error('Seleccione la Circunscripcion');
        }else{
          $("#"+div_resul+"").html(resul);
        }

      }else if(quien=="f_asignar_usuario_mesa" && resul == 'ok'){
        recargar();
      }else if(quien=="f_asignar_usuario_mesa" && resul != 'ok'){
        alertify.success(resul);
      }else if(quien=="f_editar_tiempo" && resul == 'ok'){
        $('#ModalEdit').modal('hide');
        refresh_calendar();
        refresh_calendar_emergencias(id_sol);
        estado_calendario(id_sol);
        $('#btn_edita_fecha').attr("disabled", false);
      }else if(quien=="f_editar_tiempo" && resul == 'diferente'){
        alertify.success('No puede editar otras solicitudes');
      }
      else if(quien=="f_editar_solicitud" && resul == 'ok'){

        refresh_calendar(id_sol);
        // estado_calendario(id_sol);
      }
      else if(quien=="f_agregar_feriado" && resul == 'ok'){
        refresh_calendar_feriado();
      }
      else if(quien=="f_editar_feriado" && resul == 'ok'){
        refresh_calendar_feriado();
      }
      else{
        // $('#capa_modal').modal('hide');
        $("#"+div_resul+"").html(resul);
      }

       },
    error : function(xhr, status) {
          $("#"+div_resul+"").html('ha ocurrido un error, revise su conexion e intentelo nuevamente');
    }
  });
})

$(document).on("submit",".form_crear_rol",function(e){
  e.preventDefault();
  var quien=$(this).attr("id");
  var formu=$(this);
  var varurl=$(this).attr("action");

   $("#div_notificacion_rol").html( $("#cargador_empresa").html());
   $(".form-group").removeClass("has-error");
   $(".help-block").text('');

  $.ajax({
    // la URL para la petición
    url : varurl,
    data : formu.serialize(),
    type : 'POST',
    dataType : "html",

    success : function(resul) {
      $("#capa_formularios").html(resul);
    },
    error : function(data) {
              var lb="";
              var errors = $.parseJSON(data.responseText);
               $.each(errors, function (key, value) {

                   $("#"+key+"_group").addClass( "has-error" );
                   $("#"+key+"_span").text(value);
               });

           $("#div_notificacion_rol").html('');
    }

  });
})

function asignar_rol(idusu){
   var idrol=$("#rol1").val();
   var urlraiz=$("#url_raiz_proyecto").val();
   $("#zona_etiquetas_roles").html($("#cargador_empresa").html());
   var miurl=urlraiz+"/asignar_rol/"+idusu+"/"+idrol+"";

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
      var etiquetas="";
      var roles=$.parseJSON(resul);
      $.each(roles,function(index, value) {
        etiquetas+= '<span class="label label-warning">'+value+'</span> ';
      })

     $("#zona_etiquetas_roles").html(etiquetas);

    }).fail( function()
    {
    $("#zona_etiquetas_roles").html('<span style="color:red;">...Error: Aun no ha agregado roles o revise su conexion...</span>');
    }) ;

}

function quitar_rol(idusu){
   var idrol=$("#rol2").val();
   var urlraiz=$("#url_raiz_proyecto").val();
   $("#zona_etiquetas_roles").html($("#cargador_empresa").html());
   var miurl=urlraiz+"/quitar_rol/"+idusu+"/"+idrol+"";

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
      var etiquetas="";
      var roles=$.parseJSON(resul);
      $.each(roles,function(index, value) {
        etiquetas+= '<span class="label label-warning" style="margin-left:10px;" >'+value+'</span> ';
      })

     $("#zona_etiquetas_roles").html(etiquetas);

    }).fail( function()
    {
    $("#zona_etiquetas_roles").html('<span style="color:red;">...Error: Aun no ha agregado roles  o revise su conexion...</span>');
    }) ;
}

function buscar_cobros_avisos_segun_valor(){
  //alert("hol");
  var buscar=$("#buscar").val();

  $.getJSON("buscar_cobros_avisos_segun_valor/"+buscar+"",{},function(objetosretorna){
    $("#tabla_cobros_avisos tbody").html("");
    var TamanoArray = objetosretorna.length;
    $.each(objetosretorna, function(i,datos){
      if (datos.pagado==1) {
        var nuevaFila =
        "<tr>"
        +"<td style='text-align:center;' scope='row'>"+datos.nro+"</td>"
        +"<td style='font-size: 14px; text-align:center;' scope='row'>"+datos.total.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+"</td>"
        +"<td style='' scope='row'>"+datos.periodo_pago+"</td>"
        +"<td style='' scope='row'>"+datos.responsable_pago+"</td>"
        +"<td style='' scope='row'>"+datos.unidades_funcionales+"</td>"
        +"<td style='text-align:center; color: green;' scope='row'> Pagado </td>"
        +"</tr>";
      }
      else {
        if (datos.activo==1) {
          var nuevaFila =
          "<tr>"
          +"<td style='text-align:center;' scope='row'>"+datos.nro+"</td>"
          +"<td style='font-size: 14px; text-align:center;' scope='row'>"+datos.total.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+"</td>"
          +"<td style='' scope='row'>"+datos.periodo_pago+"</td>"
          +"<td style='' scope='row'>"+datos.responsable_pago+"</td>"
          +"<td style='' scope='row'>"+datos.unidades_funcionales+"</td>"
          +"<td style='text-align:center;'>"
          +"<button type='button' class='btn  btn-success btn-xs' onclick='verinfo_cobros("+datos.id_cobro_aviso+",8);'> Cobrar </button>"
          +"</td>"
          +"</tr>";
        }else {
          var nuevaFila =
          "<tr>"
          +"<td style='text-align:center;' scope='row'>"+datos.nro+"</td>"
          +"<td style='font-size: 14px; text-align:center;' scope='row'>"+datos.total.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+"</td>"
          +"<td style='' scope='row'>"+datos.periodo_pago+"</td>"
          +"<td style='' scope='row'>"+datos.responsable_pago+"</td>"
          +"<td style='' scope='row'>"+datos.unidades_funcionales+"</td>"
          +"<td style='text-align:center; color: red;' scope='row'> Aviso Anulado </td>"
          +"</tr>";
        }
      }

      $(nuevaFila).appendTo("#tabla_cobros_avisos tbody");
    });

    if(TamanoArray==0){
      var nuevaFila =
      "<tr><td colspan=6>No se encontraron avisos de cobranza que coincidan con su búsqueda</td>"
      +"</tr>";
      $(nuevaFila).appendTo("#tabla_cobros_avisos tbody");
    }
  });

};

function cobro_aviso_agregar_item(){
      var id_cobro_aviso=$("#id_cobro_aviso").val();
      var concepto=$("#concepto").val();
      var detalle=$("#detalle").val();
      var monto=$("#monto").val();

      if(concepto == "" || detalle == "" || monto == ""){
        return alertify.error('Debe llenar todos los campos');
      }

      //Codificamos el concepto y detalle, reemplazando el caracter / por &47; btoa es para base64 encode
      var concepto_codificado = String(concepto).replace('/', '&47;');
      var detalle_codificado = String(detalle).replace('/', '&47;');
      $.getJSON("cobro_aviso_agregar_item/"+id_cobro_aviso+"/"+concepto_codificado+"/"+detalle_codificado+"/"+monto+"",{},function(objetosretorna){
        $("#tabla_cobros_avisos_conceptos tbody").html("");
        var TamanoArray = objetosretorna.length;
        var nro=0;
        $.each(objetosretorna, function(i,datos){
          nro = nro+1;
          var nuevaFila =
          "<tr>"
          +"<td style='text-align:center;' scope='row'>"+nro+"</td>"
          +"<td style='' scope='row'>"+datos.concepto+"</td>"
          +"<td style='' scope='row'>"+datos.detalle+"</td>"
          //+"<td style='font-size: 14px; text-align:center;' scope='row'>"+Intl.NumberFormat("de-DE").format(datos.monto)+"</td>"
          +"<td style='font-size: 14px; text-align:center;' scope='row'>"+datos.monto.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })+"</td>"
          +"<td style='text-align:center;'>"
          +"<button type='button' class='btn  btn-danger btn-xs' onclick='borrar_cobro_aviso_concepto("+datos.id_cobro_aviso_concepto+", \""+datos.concepto+"\");'><i class='fa fa-fw fa-remove'></i> Borrar</button>"
          +"</td>"
          +"</tr>";

          $(nuevaFila).appendTo("#tabla_cobros_avisos_conceptos tbody");
        });

        //agregar ultima fila (la de introducir nuevo concepto)
        var ultimaFila=
        "<tr>"
        +"<td style='font-size: 15px; text-align:center;'><b>+</b></td>"
        +"<td>"
        +"<div class='form-group' style='text-align:center'>"
        +"<input type='text' name='concepto' id='concepto' style='width: 95%;'>"
        +"</div>"
        +"</td>"

        +"<td>"
        +"<div class='form-group' style='text-align:center'>"
        +"<input type='text' name='detalle' id='detalle' style='width: 95%;'>"
        +"</div>"
        +"</td>"

        +"<td>"
        +"<div class='form-group' style='text-align:center'>"
        +"<input type='number' name='monto' id='monto' min='0' step='0.01' style='width: 95%;'>"
        +"</div>"
        +"</td>"

        +"<td>"
        +"<div class='form-group' style='text-align:center'>"
        +"<button type='button' class='btn btn-xs btn-primary' onclick='cobro_aviso_agregar_item();'>Agregar Ítem</button>"
        +"</div>"
        +"</td>"
        +"</tr>"

        $(ultimaFila).appendTo("#tabla_cobros_avisos_conceptos tbody");

        if(TamanoArray==0){
          var nuevaFila =
          "<tr><td colspan=4>No se encontraron parametros para el aviso de cobranza</td>"
          +"</tr>";
          $(nuevaFila).appendTo("#tabla_cobros_avisos_conceptos tbody");
        }

        //Limpiamos los campos
        document.getElementById("concepto").value = "";
        document.getElementById("detalle").value = "";
        document.getElementById("monto").value = "";
      });
 }

 function borrar_cobro_aviso_concepto(id_cobro_aviso_concepto, concepto){
   //var concepto=$("#concepto").val();
   alertify.confirm('', 'Está por borrar el ítem con concepto "'+concepto+'"',
   function(){
     //$("#f_borrar_cobro_aviso_concepto").submit()
     var urlraiz=$("#url_raiz_proyecto").val();
     $("#capa_modal").show();
     $("#capa_formularios").show();
     var screenTop = $(document).scrollTop();
     $("#capa_formularios").css('top', screenTop);
     $("#capa_formularios").html($("#cargador_empresa").html());
     var miurl=urlraiz+"/borrar_cobro_aviso_concepto/"+id_cobro_aviso_concepto+"";

      $.ajax({
      url: miurl
      }).done( function(resul)
      {
       $("#capa_formularios").html(resul);

      }).fail( function(resul)
     {
      $("#capa_formularios").html(resul);
    }) ;
    alertify.success('Ítem eliminado correctamente')
   },
   function(){ alertify.success('Ítem no eliminado')}).set('labels', {ok:'Confirmar', cancel:'Cancelar'});
 }

function borrado_usuario(idusu){

   var urlraiz=$("#url_raiz_proyecto").val();
   $("#capa_modal").show();
   $("#capa_formularios").show();
   var screenTop = $(document).scrollTop();
   $("#capa_formularios").css('top', screenTop);
   $("#capa_formularios").html($("#cargador_empresa").html());
   var miurl=urlraiz+"/form_borrado_usuario/"+idusu+"";

    $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#capa_formularios").html(resul);

    }).fail( function(resul)
   {
    $("#capa_formularios").html(resul);
   }) ;
}


function borrar_permiso(idrol,idper){

     var urlraiz=$("#url_raiz_proyecto").val();
     var miurl=urlraiz+"/quitar_permiso/"+idrol+"/"+idper+"";
     $("#filaP_"+idper+"").html($("#cargador_empresa").html() );
        $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#filaP_"+idper+"").hide();

    }).fail( function()
   {
     alert("No se borro correctamente, intentalo nuevamente o revisa tu conexion");
   }) ;



}


function borrar_rol(idrol){

     var urlraiz=$("#url_raiz_proyecto").val();
     var miurl=urlraiz+"/borrar_rol/"+idrol+"";
     $("#filaR_"+idrol+"").html($("#cargador_empresa").html() );
        $.ajax({
    url: miurl
    }).done( function(resul)
    {
     $("#filaR_"+idrol+"").hide();

    }).fail( function()
   {
     alert("No se borro correctamente, intentalo nuevamente o revisa tu conexion");
   }) ;



}

//Funcion para cargar un archivo
$(document).on("submit",".formarchivo",function(e){

  e.preventDefault();
  var formu=$(this);
  var nombreform=$(this).attr("id");

  if(nombreform=="f_editar_evidencia_persona" ){ var miurl="editar_evidencia_persona";  var divresul="div_notificacion_sol"; }
  // if(nombreform=="f_cargar_datos_usuarios" ){ var miurl="cargar_datos_usuarios";  var divresul="notificacion_resul_fcdu"; }
  // if(nombreform=="f_subir_imagen_tipodisp" ){ var miurl="subir_imagen_tipodisp";  var divresul="notificacion_resul_fsitd"; }

  //información del formulario
  var formData = new FormData($("#"+nombreform+"")[0]);

  //hacemos la petición ajax
  $.ajax({
      url: miurl,
      type: 'POST',

      // Form data
      //datos del formulario
      data: formData,
      //necesario para subir archivos via ajax
      cache: false,
      contentType: false,
      processData: false,
      //mientras enviamos el archivo
      beforeSend: function(){
          $("#"+divresul+"").html($("#cargador_empresa").html());
      },
      //una vez finalizado correctamente
      success: function(data){
          $("#"+divresul+"").html(data);
          // $("#fotografia_usuario").attr('src', $("#fotografia_usuario").attr('src') + '?' + Math.random() );
      },
      //si ha ocurrido un error
      error: function(data){
          alert("ha ocurrido un error") ;

      }
  });
});
