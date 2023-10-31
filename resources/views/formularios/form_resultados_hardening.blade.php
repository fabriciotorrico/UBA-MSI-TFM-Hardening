<section  id="content" style="background-color: #002640;"> 
<div class="" >
    <div class="container">

        <div class="row">
          <div class="col-md-12" >
              <div class="myform-top">
                <div class="myform-top-right">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="cerrar_modal"><span aria-hidden="true">&times;</span></button>
                </div>
              </div>

              <div style="background-color: black; color: white; font-family: monospace; padding: 20px;">
                <?php 
                    echo "> sh ".$archivo_hardening."<br><br>";
                    echo nl2br($resultado_hardening);
                ?>
              </div>

              <div id="div_notificacion_sol" class="myform-bottom">
                  <form href="oscap/scripts-remediacion/2023-10-27_20-08-08_id_escaneo_4.sh"  method="post" id="f_editar_acceso" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <div class="col-md-12" style="text-align: center">
                        <br>
                          <a href="oscap/scripts-remediacion/{{ $archivo_hardening}}" download="{{ $archivo_hardening}}" class="boton-descarga">Descargar Script de Hardenización</a>
                    </div>
                    <button></button>
                </form>



              </div>
              
        </div>
    </div>
  </div>
</section>


<style>
.boton-descarga {
  font-size: 17px;
  padding: 10px;
  background-color: #a9c4c3;
  color: #000;
  text-decoration: none;
  border-radius: 5px;
  width: 100%;
}

.boton-descarga:hover {
  background-color: #333;
  color: #fff;
}

</style>