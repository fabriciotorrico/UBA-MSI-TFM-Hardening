
<section >



<div class="row" >

<div class="col-md-12">

  <div class="box box-primary   box-gris" style="margin-bottom: 200px;">
    <div class="myform-top">
       <div class="myform-top-left">
          {{-- <img  src="" class="img-responsive logo" /> --}}
         <h3>Agregar Usuario</h3>
           <p>Por favor llene los siguientes campos</p>
       </div>
       <div class="myform-top-right">
         <i class="fa fa-user-plus"></i>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_modal"><span aria-hidden="true">&times;</span></button>
       </div>
   </div>
   <div id="div_notificacion_sol" class="myform-bottom">
        <form action="{{ url('nuevo_usuario') }}"  method="post" id="f_editar_acceso" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
          <input type="hidden" name="id_persona" value="{{ $id_persona }}">
          <br>
          <div class="col-md-4">
              <div class="form-group">
                  <label >Nombre de Usuario</label>
                  <input type="name" name="name" placeholder="" class="form-control" required/>
              </div>
          </div>

          <div class="col-md-4">
              <div class="form-group">
                  <label >Contraseña</label>
                  <input type="password" class="form-control" id="password" name="password"  required >
              </div>
          </div>

          <div class="col-md-4">
              <div class="form-group">
                  <label >Rol</label>
                  <select class="form-control select2" name="rol[]" id="rol" multiple="multiple" required>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->description }}</option>
                    @endforeach
                  </select>
              </div>
          </div>

          <div class="col-md-12">
              <br>
          </div>
          <button type="submit" class="mybtn">Registrar Usuario</button>
       </form>
     </div>
  </div>
  </div>
</div>
</section>

<script>
  $( document ).ready(function() {
    //Initialize Select2 Elements
    $(".select2").select2();
  });
</script>
