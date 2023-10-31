<div class="container">
    <div class="row">
      <div class="col-sm-1 myform-cont" ></div>
      <div class="col-sm-10 myform-cont" >
             <div class="myform-top">
                <div class="myform-top-left">
                   {{-- <img  src="" class="img-responsive logo" /> --}}
                  <h3>Nuevo Perfil Personalizado</h3>
                    <p>Por favor llene los siguientes campos</p>
                </div>
                <div class="myform-top-right">
                  <i class="fa fa-legal"></i>
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
            <form action="{{ url('nuevo_perfil') }}"  method="post" class="" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              
              <div class="col-md-12">
                <div class="form-group">
                  <label >Política Base</label>
                      <select class="form-control select2" name="id_politica" id="id_politica" required>
                        @foreach($politicas as $politica)
                          <option value="{{ $politica->id_politica }}">
                            {{ $politica->descripcion." (".$politica->nombre.")" }}
                          </option>
                        @endforeach
                      </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label >Profile Id</label>
                  <input type="input" name="profile_id" placeholder="Ej.: xccdf_ar.uba.content_profile_custom_cambio" class="form-control" required/>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label >Título</label>
                  <input type="input" name="title" placeholder="" class="form-control" required/>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label >Descripción</label>
                  <input type="input" name="description" placeholder="Breve descripción del perfil" class="form-control" required/>
                </div>
              </div>

              <div class="col-md-12">
                  <br>
              </div>
              <button type="submit" class="mybtn">Crear</button>
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