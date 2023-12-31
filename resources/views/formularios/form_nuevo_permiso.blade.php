<div class="col-md-12">

    <div class="box box-primary col-md-12 box-gris">
 
        <div class="box-header with-border my-box-header">
        <h3 class="box-title"><strong>Nuevo permiso</strong></h3>
        </div><!-- /.box-header -->
        <hr style="border-color:white;" />
        <div class="box-body">

                <div class="col-md-6">
		             <form   action="{{ url('asignar_permiso') }}"  method="post" id="f_asignar_permiso" class="formentrada"  >
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
						<div class="form-group">
							<label class="col-sm-2" for="rol">Rol*</label>
		    			<div class="col-sm-10" >
								<select id="rol_sel" name="rol_sel" class="form-control" required>
									@foreach($roles as $rol)
									<option value="{{ $rol->id }}">{{ $rol->name }}</option>
									@endforeach
		    				</select>
              </div>
						</div><!-- /.form-group -->

						<div class="form-group">
							<label class="col-sm-2" for="rol">Permisos*</label>
		    			<div class="col-sm-10" >
		                     
		                     <select id="permiso_rol" name="permiso_rol" class="form-control" required>
		                     @foreach($permisos as $permiso)
		                     <option value="{{ $permiso->id }}">{{ $permiso->name }}</option>
		                     @endforeach
		    				</select>
		                     
		                </div>
						</div><!-- /.form-group -->

						<div class="box-footer col-xs-12 box-gris ">
		                        <button type="submit" class="btn btn-primary">Agregar Permiso</button>
		                </div>
					 </form>
		        </div>

			


			    <div class="col-md-6">
              
		            <form   action="{{ url('crear_permiso') }}"  method="post" id="f_crear_permiso" class="formentrada"  >
						<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
		                


		                
		                <div class="col-md-12">	  
			                <div class="form-group">
									<label class="col-sm-2" for="apellido">Permiso*</label>
			                    <div class="col-sm-10" >
									<input type="text" class="form-control" id="permiso_nombre" name="permiso_nombre" " required >
			                    </div>
							</div><!-- /.form-group -->

					    </div><!-- /.col -->

					      <div class="col-md-12">	  
			                <div class="form-group">
									<label class="col-sm-2" for="apellido">Slug*</label>
			                    <div class="col-sm-10" >
									<input type="text" class="form-control" id="permiso_slug" name="permiso_slug" " required >
			                    </div>
							</div><!-- /.form-group -->

					    </div><!-- /.col -->

					      <div class="col-md-12">	  
			                <div class="form-group">
									<label class="col-sm-2" for="apellido">Descripcion*</label>
			                    <div class="col-sm-10" >
									<input type="text" class="form-control" id="permiso_descripcion" name="permiso_descripcion" " required >
			                    </div>
							</div><!-- /.form-group -->

					    </div><!-- /.col -->


		                <div class="box-footer col-xs-12 box-gris ">
		                        <button type="submit" class="btn btn-primary">Crear Nuevo Permiso</button>
		                </div>
		            </form>
                </div>          
        </div>
                    
    </div>
                       
</div>


<div class="col-md-12 box-white">

@foreach($roles as $rol)

    <div class="table-responsive" >

	    <table  class="table table-hover table-striped" cellspacing="0" width="100%">
				
                <thead>
                <th colspan="5" style="text-align: center; background-color: #b8ccde;" >Permisos del Usuario {{ $rol->name }}</th>
                </thead>
				<thead>
						    <th>codigo</th>
								<th>nombre</th>
								<th>slug</th>
								<th>descripcion</th>
							    <th>Acción</th>
						
				</thead>
	    <tbody>
	 

	    @foreach($rol->permissions as $permiso)
		   
        
		 <tr role="row" class="odd" id="filaP_{{ $permiso->id }}">
			<td>{{ $permiso->id }}</td>
			<td><span class="label label-default">{{ $permiso->name or "Ninguno" }}</span></td>
			<td class="mailbox-messages mailbox-name"><a href="javascript:void(0);" style="display:block"></i>&nbsp;&nbsp;{{ $permiso->slug  }}</a></td>
			<td>{{ $permiso->description }}</td>
			<td>
			<button type="button"  class="btn  btn-danger btn-xs"  onclick="borrar_permiso({{ $rol->id }},{{ $permiso->id }});"  ><i class="fa fa-fw fa-remove"></i></button>
			</td>
		   </tr>
	
	    @endforeach
		</tbody>
		</table>

	</div>
@endforeach

</div>



