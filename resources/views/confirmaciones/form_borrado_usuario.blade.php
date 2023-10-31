<div class="col-md-12">
  <!-- general form elements -->
  <div class="box box-primary">
    <div class="box-header with-border my-box-header">
      <h2 class="box-title">Borrar Usuario</h2>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class=" box-body" style="text-align: center;">
      <h3>¿Realmente desea Borrar de manera definitiva al suario "{{ $usuario->name }}"?</h3>
    </div>
    <div class="box-footer"  style="text-align: center;">
      <form method="post" action="{{ url('borrar_usuario') }}" id="f_borrar_usuario" class="formentrada" >
       <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
       <input type="hidden" name="id_usuario" value="{{ $usuario->id }}">
       <button type="button" class="btn btn-default" onclick="javascript:$('.div_modal').click();" >Cancelar</button>
       <button type="submit" class="btn btn-danger" style="margin-left:20px;" >Borrar Usuario</button>
      </form>
    </div>
  </div>
  <!-- /.box -->
</div>
