<div class="container">
    <div class="row">
      <div class="col-sm-1 myform-cont" ></div>
      <div class="col-sm-10 myform-cont" >
             <div class="myform-top">
                <div class="myform-top-left">
                   {{-- <img  src="" class="img-responsive logo" /> --}}
                  <h3>Ejecutar Escaneo</h3>
                    <p>Por favor edita los campos que corresonda</p>
                </div>
                <div class="myform-top-right">
                  <i class="fa fa-heartbeat"></i>
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
            <form action="{{ url('nuevo_escaneo') }}"  method="post" class="" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <input type="hidden" name="id_cliente" value="{{ $id_cliente }}">
              <input type="hidden" name="id_cliente_perfil" value="{{ $id_cliente_perfil }}">
            
              <div class="col-md-12">
                <div class="form-group">
                  <label >Nombre descriptivo del cliente</label>
                  <input type="input" name="nombre" placeholder="" class="form-control" value="{{ $nombre }}" required readonly/>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label >Descripción</label>
                  <input type="input" name="descripcion" placeholder="Breve descripción del cliente" class="form-control" value="{{ $descripcion }}" required readonly/>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label >Perfiles a evaluar</label>
                    <select class="form-control select2" name="perfiles_asignados[]" id="id_politica" required disabled>
                      @foreach($perfiles as $perfil)
                        <?php $seleccionada = 0?>
                        @foreach($perfiles_activos as $perfil_activo)
                          <?php if ($perfil->id_perfil == $perfil_activo->id_perfil) {
                            $seleccionada = 1;
                            ?>
                              <option value="{{ $perfil->id_perfil }}" selected>                  
                                {{ "Polítcia: ".$perfil->politica_descripcion." (".$perfil->politica_nombre."). Perfil: ".$perfil->perfil_title." (".$perfil->perfil_id.")" }}
                              </option>
                            <?php
                          }?>
                        @endforeach
                        <?php if ($seleccionada == 0) { ?>
                            <option value="{{ $perfil->id_perfil }}">                  
                              {{ "Polítcia: ".$perfil->politica_descripcion." (".$perfil->politica_nombre."). Perfil: ".$perfil->perfil_title." (".$perfil->perfil_id.")" }}
                            </option>
                          <?php
                        }?>
                      @endforeach
                    </select>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label >Dirección IP (v4)</label>
                  <input type="input" name="direccion_ip" placeholder="Ej.: 10.0.0.10" class="form-control" value="{{ $direccion_ip }}" required readonly/>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label >Usuario para acceso vía SSH</label>
                  <input type="input" name="usuario" class="form-control" value="{{ $usuario }}" required/>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label >Contraseña</label>
                  <input type="password" name="contrasena" class="form-control" value="{{ $contrasena }}" required/>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label >Notas del escaneo</label>
                  <input type="input" name="notas" class="form-control" placeholder="" required/>
                </div>
              </div>

              <div class="col-md-12">
                  <br>
              </div>
              <button type="submit" class="mybtn">Escanear</button>
            </form>

            </div>
      </div>
    </div>
</div>
<script>
  $( document ).ready(function() {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
</script>