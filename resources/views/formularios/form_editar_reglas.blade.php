
<section >
  <div class="container">
      <div class="row">
        <div class="col-sm-12 myform-cont" >
               <div class="myform-top">
                  <div class="myform-top-left">
                     {{-- <img  src="" class="img-responsive logo" /> --}}
                    <h3>Perfil Personalizado</h3>
                      <!--p>Cambie los campos que desea modificar y pulse el boton "Guardar Cambios"</p-->
                  </div>
                  <div class="myform-top-right">
                    <i class="fa fa-file-text"></i>
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
            <form action="#"  method="post" class="" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              
              <div class="col-md-12">
                <div class="form-group">
                  <label >Política Base</label>
                  <input type="text" class="form-control" value="{{$politica_base_description}} ({{$politica_base_title}})" readonly>
                </div>
              </div>

              <!--div class="col-md-6">
                <div class="form-group">
                  <label >Profile Id</label>
                  <input type="input" name="profile_id" placeholder="Ej.: xccdf_ar.uba.content_profile_custom_cambio" class="form-control" required/>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label >Tailoring (Archvio modificado)</label>
                  <p>Profile_ID_tailoring_fecha-hora.xml</p>
                </div>
              </div-->

              <div class="col-md-12">
                <div class="form-group">
                  <label >Título</label>
                  <input type="text" class="form-control" value="{{$title}}" readonly>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label >Descripción</label>
                  <input type="text" class="form-control" value="{{$description}}" readonly>                  
                </div>
              </div>

              <div class="col-md-12">
                  <br>
              </div>
              <button type="submit"></button>
            </form>

            </div>

        </div>
      </div>
  </div>

  <br>


  <div class="col-md-12">
    <!--div class="box box-primary box-gris" style="margin-bottom: 200px;"-->
    <div class="box box-primary box-gris">

      <div class="myform-top">
         <div class="myform-top-left">
            {{-- <img  src="" class="img-responsive logo" /> --}}
           <h3>Modificar la asignación de reglas</h3>
             <p>Habilite las reglas que deben ser revisadas</p>
         </div>
         <!--div class="myform-top-right">
           <i class="fa fa-tachometer"></i>
         </div-->
     </div>
      <div class="box-body">
        <div class="col-md-12">
          <div class="box-body table-responsive no-padding">

            <form action="{{ route('guardado_preventivo_reglas') }}" id="f_guardado_preventivo_reglas"  method="post" enctype="multipart/form-data" class="formentrada">

            <table id="tabla_personas" class="table table-hover table_striped_lecturas_agua table-bordered">
                <thead>
                  <tr style="background-color:#024a7cc9; text-align:center; color:white">
                    <th style="text-align:center; vertical-align: middle;" colspan="4">
                        REGLAS
                    </th>
                  </tr>
                  <tr style="background-color:#024a7cc9; text-align:center; color:white">
                    <!--th style="text-align:center; vertical-align: middle;">#</th-->
                    <th style="text-align:center; vertical-align: middle;">Nro</th>
                    <th style="text-align:center; vertical-align: middle;">Revisar</th>
                    <th style="text-align:center; vertical-align: middle;">Título</th>
                    <th style="text-align:center; vertical-align: middle;">Descripción</th>
                  </tr>
                </thead>
                <tbody>

                    @foreach ($reglas as $key => $regla)
                      @php
                        $num = $key+1;
                      @endphp
                      <tr>
                        <!--td style='font-size: 15px; text-align:center;'><b>{{$key+1}}</b></td-->
                        <input type="hidden" name="id_perfil_regla_{{$regla->id_perfil_regla}}" value="{{$regla->id_perfil_regla}}">
                        <td style='text-align:center;' scope="row"><b>{{ $num }}</b></td>
                        <td style='text-align:center;' scope="row"><b>                          
                          <input type="checkbox" name="id_perfil_regla_{{$regla->id_perfil_regla}}_habilitada" value="habilitada"  <?php if($regla->habilitada == 1) echo "checked" ?> >
                        </td>
                        <td style='text-align:center;' scope="row"><b>{{ $regla->title }} </b></td>
                        <td style='text-align:center;' scope="row"><b>{{ $regla->description }}<br>(ID: {{ $regla->id_elemento }})</b></td>



                        @if($num == 1)
                          <td rowspan="{{ count($reglas) }}" style="background-color:#ecf0f5; text-align:center; color:white">
                            <button type="submit" class="btn btn-primary btn-block" id="button_guardar">                              
                              @for($i=1; $i<=count($reglas)/2; $i++)
                                G<br>U<br>A<br>R<br>D<br>A<br>R<br><br>
                              @endfor                              
                            </button>
                          </td>
                        @endif
                      </tr>
                    @endforeach
                </tbody>
              </form>
            </table>

            @if (count($reglas) == 0)
            <div class="box box-primary col-xs-12">
              <div class='aprobado' style="margin-top:70px; text-align: center">
              <label style='color:#177F6B'>
                ... No se encontraron reglas registradas...
              </label>
              </div>
            </div>
            @endif
          </div>


          <div class="col-md-11">
            <form action="{{ url("/listado_politicas") }}" id="form_salir" method="get">
              <button type="button" class="btn btn-primary btn-block" onclick="confirmar_salir_agua_lecturas()">Salir</button>
            </form>
          </div>
          <div class="col-md-12">
              <br>
          </div>
        </div>
       </div>
    </div>
  </div>
</section>

<script>
  $('#button_guardar').css('height', $('#button_guardar').parent('td').height());

  function confirmar_salir_agua_lecturas(){
    alertify.confirm('', 'No olvide que debe pulsar el boton "Guardar" ubicado a la derecha de la tabla donde llenó las lecturas, caso contrario los datos introducidos se borrarán.',
      function(){ $("#form_salir").submit()},
      function(){ alertify.error('Pulse el botón "Guardar"')}).set('labels', {ok:'Ya guardé', cancel:'Volver para guardar'});
  };
</script>
