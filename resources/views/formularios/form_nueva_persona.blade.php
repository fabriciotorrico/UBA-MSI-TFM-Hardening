<div class="container">
    <div class="row">
      <div class="col-sm-12 myform-cont" >
             <div class="myform-top">
                <div class="myform-top-left">
                   {{-- <img  src="" class="img-responsive logo" /> --}}
                  <h3>Agregar Persona</h3>
                    <p>Por favor llene los siguientes campos</p>
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
            <form action="{{ url('nueva_persona') }}"  method="post" id="f_enviar_agregar_persona" class="" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="col-md-12">
									<div class="form-group">
											<label >Nombres</label>
											<input type="input" name="nombres" placeholder="" class="form-control" value="{{ old('nombres') }}"  required/>
									</div>
							</div>
							<div class="col-md-12">
									<div class="form-group">
											<label >Paterno</label>
											<input type="input" name="paterno" placeholder="" class="form-control" value="{{ old('paterno') }}" />
									</div>
							</div>
							<div class="col-md-12">
									<div class="form-group">
											<label >Materno</label>
											<input type="input" name="materno" placeholder="" class="form-control" value="{{ old('materno') }}" />
									</div>
							</div>

              <div class="col-md-8">
                    <div class="form-group">
                        <label >Cédula de Identidad</label>
                        <input type="input" name="cedula" id="input_cedula" placeholder="" class="form-control" value="{{ old('cedula') }}" pattern="[0-9]{6,9}" required/>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label >Complemento SEGIP</label>
                        <input type="input" name="complemento" placeholder="" class="form-control" value="{{ old('complemento') }}" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label >Expedido</label>
                        <select class="form-control" name="expedido">
                            <option>LP</option>
                            <option>OR</option>
                            <option>PT</option>
                            <option>CB</option>
                            <option>SC</option>
                            <option>BN</option>
                            <option>PA</option>
                            <option>TJ</option>
                            <option>CH</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class=" ">Fecha de nacimiento</label>
                        <input style='line-height: initial;' type="date" name="nacimiento" placeholder="" min="1900-01-01" class="form-control" value="{{ old('nacimiento') }}" required />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label >Número de teléfono</label>
                        <input type="input" name="telefono" placeholder="" class="form-control" value="{{ old('telefono') }}" pattern="[0-9]{6,9}" onkeydown="return event.keyCode !== 69" title="Introduzca un número valido" required/>
                    </div>
                </div>

                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <label >Contacto de Referencia</label>
                        <input type="text" name="telefono_ref" placeholder="" class="form-control" value="{{ old('telefono_ref') }}" pattern="[0-9]{8}" data-inputmask="&quot;mask&quot;: &quot;99999999&quot;" data-mask="" title="Introduzca un número valido" required/>
                    </div>
                </div> --}}
                {{-- <div class="col-md-12">
                    <div class="form-group">
                        <label >Dirección</label>
                        <input type="input" name="direccion" placeholder="Domicilio" class="form-control" value="{{ old('direccion') }}" required/>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label >Email</label>
                        <input type="email" name="email" placeholder="Correo electrónico" class="form-control" value="{{ old('email') }}" />
                    </div>
                </div> --}}
                <div class="col-md-12">
                    <br>
                </div>
                <button type="submit" class="mybtn">Registrar</button>
              </form>

							<!--br>
							<button type="button" class="btn-info" id="cerrar_modal"><i class="fa fa-close"> Cerrar</i></button-->
            </div>
      </div>
    </div>
</div>
