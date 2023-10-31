

<section  id="contenido_principal">
<section  id="content">

    <div class="" >
        <div class="container">
            <div class="row">
              <div class="col-sm-12 myform-cont" >

                     <div class="myform-top">
                        <div class="myform-top-left">
                           {{-- <img  src="" class="img-responsive logo" /> --}}
                          <h3>Agregar Usuario</h3>
                            <p>Por favor busque el ci o nombre de la persona a la cual se creará el usuario</p>
                        </div>
                        <div class="myform-top-right">
                          <i class="fa fa-user-plus"></i>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_modal"><span aria-hidden="true">&times;</span></button>
                        </div>
                    </div>

                  <div class="col-md-12" >
                    @if (count($errors) > 0)

                        <div class="alert alert-danger">
                            <strong>UPPS!</strong> Error al Registrar<br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                    @endif
                   </div>

                    <div id="div_notificacion_sol" class="myform-bottom">
                    	<br>
                      <div class="col-md-4">
                            <div class="form-group">
                                <label >Buscar por Cédula de Identidad</label>
                                <input type="input" name="cedula" id="input_cedula" placeholder="" class="form-control" value="{{ old('cedula') }}" pattern="[0-9]{6,9}" required/>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label >Buscar por Nombre</label>
                                <input type="input" name="nombre" id="input_nombre" placeholder="" class="form-control" value="{{ old('nombres') }}"  required/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <br>
                        </div>
                        <button style="border: none; background-color: #32a7eb;"></button>

											<div class="box box-primary">
													<div  style="background-color:#fff; text-align:center; color:black" class="box-header">
															<h3 class="box-title"><b>Personas Encontradas</b></h3>
													</div>
													<div class="box-body table-responsive no-padding scrollable">
															<table class="table table-bordered" id="tabla_cedula">
																	<thead>
																	<tr>
																			<th style="background-color:#32a7eb; text-align:center; color:white">Nombre</th>
																			<th style="background-color:#32a7eb; text-align:center; color:white">Cédula de Identidad</th>
																			<th style="background-color:#32a7eb; text-align:center; color:white">Acción</th>
																	</tr>
																	</thead>
																	<tbody>
																	</tbody>
															</table>
													</div>
											</div>
                    </div>
              </div>
            </div>

        </div>
      </div>

</section>

</section>




<script>

    $( "#input_cedula" ).keyup(function() {
        $("#tabla_cedula tbody").html("");
        var cedula = $("#input_cedula").val();
        var cedula_sin_espacios = cedula.trim();
        if (cedula_sin_espacios == "") {

        } else {
            // $.getJSON("consultaRecintosPorRecinto/"+recinto+"",{},function(objetosretorna){
            $.getJSON("consultaPersonaRegistradaCi/"+cedula_sin_espacios+"",{},function(objetosretorna){
                $("#div_usuarios_encontrados").show();
                $("#error").html("");
                var TamanoArray = objetosretorna.length;
                $.each(objetosretorna, function(i,datos){

                    var nuevaFila =
                    "<tr>"
                    +"<td style=text-align:center;>"+datos.nombre_completo+"</td>"
                    +"<td style=text-align:center;>"+datos.cedula_identidad+"</td>"
                    +"<td style=text-align:center;><button type='button' class='btn btn-success btn-xs' onclick='verinfo_usuario("+datos.id_persona+","+0+")' ><i class='fa fa-user'> Agregar Usuario</i></button></td></td>"
                    +"</tr>";

                    $(nuevaFila).appendTo("#tabla_cedula tbody");
                });
                if(TamanoArray==0){
                    var nuevaFila =
                    "<tr><td colspan=6>No se encontraron parametros para su busqueda</td>"
                    +"</tr>";
                    $(nuevaFila).appendTo("#tabla_cedula tbody");
                }
            });
        }
    });


		$( "#input_nombre" ).keyup(function() {
				$("#tabla_cedula tbody").html("");
				var nombre = $("#input_nombre").val();
				//var cedula_sin_espacios = cedula.trim();
				//if (cedula_sin_espacios == "") {

				//} else {
						$.getJSON("consultaPersonaRegistradaNombre/"+nombre+"",{},function(objetosretorna){
								$("#div_usuarios_encontrados").show();
								$("#error").html("");
								var TamanoArray = objetosretorna.length;
								$.each(objetosretorna, function(i,datos){
										var nuevaFila =
										"<tr>"
										+"<td style=text-align:center;>"+datos.nombre_completo+"</td>"
										+"<td style=text-align:center;>"+datos.cedula_identidad+"</td>"
										+"<td style=text-align:center;><button type='button' class='btn btn-success btn-xs' onclick='verinfo_usuario("+datos.id_persona+","+0+")' ><i class='fa fa-user'> Agregar Usuario</i></button></td></td>"
										+"</tr>";
										$(nuevaFila).appendTo("#tabla_cedula tbody");
								});
								if(TamanoArray==0){
										var nuevaFila =
										"<tr><td colspan=6>No se encontraron parametros para su busqueda</td>"
										+"</tr>";
										$(nuevaFila).appendTo("#tabla_cedula tbody");
								}
						});
				//}
		});

	</script>
