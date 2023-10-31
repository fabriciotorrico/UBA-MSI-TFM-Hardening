<div class="container">
    <div class="row">
      <div class="col-sm-2 myform-cont" ></div>
      <div class="col-sm-8 myform-cont" >
             <div class="myform-top">
                <div class="myform-top-left">
                   {{-- <img  src="" class="img-responsive logo" /> --}}
                  <h3>Introducir Nueva Política</h3>
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
            <form action="{{ url('nueva_politica') }}"  method="post" class="" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="col-md-6">
                <div class="form-group">
                  <label >Archivo</label>
                  <input type="file" name="file_politica" required/>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label >Tipo de archivo</label>
                      <select class="form-control" name="tipo" id="tipo" required>
                        <option value="Source Data Stream"> Source Data Stream (ds.xml) </option>
                      </select>
                </div>
              </div>

              <div class="col-md-12">
                <div class="form-group">
                  <label >Descripción</label>
                  <input type="input" name="descripcion" placeholder="Breve descripcion" class="form-control" required/>
                </div>
              </div>

              <div class="col-md-12">
                  <br>
              </div>
              <button type="submit" class="mybtn">Cargar</button>
            </form>

            </div>
      </div>
    </div>
</div>
