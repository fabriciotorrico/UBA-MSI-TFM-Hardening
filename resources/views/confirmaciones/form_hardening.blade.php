<div class="col-md-12">
  <!-- general form elements -->
  <div class="box box-primary">
    <div class="box-header with-border my-box-header">
      <!--h2 class="box-title">Ejecutar Hardening</h2-->
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class=" box-body" style="text-align: center;">
      <h3>¿Realmente desea ejecutar las tareas de hardening en este momento?</h3>
    </div>
    <div class="box-footer"  style="text-align: center;">
      <form method="post" action="{{ url('hardening') }}">
       <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
       <input type="hidden" name="id_escaneo" value="{{ $id_escaneo }}">
       
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

       <br>
       <button type="button" class="btn btn-default" onclick="javascript:$('.div_modal').click();" >Cancelar</button>
       <button type="submit" class="btn btn-danger" style="margin-left:20px;" >Ejecutar tareas de hardenización</button>
      </form>
    </div>
  </div>
  <!-- /.box -->
</div>
