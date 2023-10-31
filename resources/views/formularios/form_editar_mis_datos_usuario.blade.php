<section  id="content" style="background-color: #002640;">
  <div class="" >
      <div class="container">

          <div class="row">
            <div class="col-sm-6 col-sm-offset-3 myform-cont" >

                   <div class="myform-top">
                      <div class="myform-top-left">
                         {{-- <img  src="{{ url('img/minculturas_logo.png') }}" class="img-responsive logo" /> --}}
                        <h3 class="text-black">Acceso al Sistema</h3>
                          <p class="text-black">Credenciales de acceso</p>
                      </div>
                      <div class="myform-top-right">
                        <i class="fa fa-user"></i>
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

               <div id="notificacion_E3" ></div>
                <div class="myform-bottom">
                  <form   action="{{ url('editar_mis_datos_acceso') }}"  method="post" id="f_editar_acceso"  class="formentrada"  >
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="col-sm-12" for="name">Nombre de Usuario</label>
                        <div class="col-sm-12" >
                        <input type="name" class="form-control" id="name" name="name"  value="{{ $usuario->name  }}"  required >
                        </div>
                      </div><!-- /.form-group -->
                    </div><!-- /.col -->

                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="col-sm-12" for="email">Nueva Contraseña</label>
                        <div class="col-sm-12" >
                          <input type="password" class="form-control" id="password" name="password"  required >
                        </div>
                      </div><!-- /.form-group -->
                    </div><!-- /.col -->


                    <div class="col-md-12">
                       <br>
                    </div>
                    <button type="submit" class="mybtn">Actualizar Credenciales</button>
                  </form>
                </div>
            </div>
          </div>
      </div>
    </div>
</section>
