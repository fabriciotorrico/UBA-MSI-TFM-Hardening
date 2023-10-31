@extends('layouts.app')

@section('htmlheader_title')
	Listado de Usuarios
@endsection

@section('main-content')
<section  id="contenido_principal">
	@if(isset($mensaje_exito))
		  {{-- <div class="alert alert-success">
			 {{ $mensaje_exito }}
		 </div> --}}
			@section("scripts_toasts")
			<script>
					alertify.success('{{ $mensaje_exito }}');
			</script>
			@endsection
	@endif
	@if(isset($mensaje_error))
			{{-- <div class="alert alert-warning">
			{{ $mensaje_error }}
		</div> --}}
			@section("scripts_toasts")
			<script>
					alertify.error('{{ $mensaje_error }}');
			</script>
			@endsection
	@endif

<div class="box box-primary">
		<div class="box-header">
            <h3 class="box-title">Listado de Usuarios</h3>
            {{-- <input type="hidden" id="rol_usuario" value="{{ $rol->slug }}"> --}}
            <div class="pull-right box-tools">
            <!--a href="{{route('import_contacto')}}" class="btn btn-default">
                <i class="fa fa-fw fa-file-excel-o text-green"></i> Importar
            </a-->
              </div>
		</div>

		<div class="margin" id="botones_control">
			<a href="javascript:void(0);" class="btn btn-xs btn-success" onclick="cargar_formulario(16);">Agregar Usuario</a>
		  <a href="{{ url("/listado_usuarios") }}"  class="btn btn-xs btn-default" >Actualizar Listado</a>
		</div>

		<div class="box-body table-responsive">
		  <table id="tabla_usuarios" class="table table-hover table-striped table-bordered">
			<thead>
				<th>Id usuario</th>
				<th>Persona</th>
				<th>Roles</th>
				<th>Nombre de Usuario</th>
				<th>Acción</th>
			</thead>
				Exportar a:
			</table>
		</div>
		<!-- /.box-body -->
	  </div>
</section>
@endsection

@section('scripts')

@parent

<script>
 function activar_tabla_usuarios() {
    var t = $('#tabla_usuarios').DataTable({

		scrollY:"420px",
		scrollX: true,
		dom: 'Bfrtip',
        processing: true,
        serverSide: true,
		// pageLength: 100,
		pageLength: 10,
		buttons: [
			'excel', 'pdf', 'print'
		],
    language: {
             "url": '{!! asset('/plugins/datatables/latino.json') !!}'
              } ,
    ajax: '{!! url('buscar_usuario') !!}',
		order: [[ 1, 'desc']],
    columns: [
						{ data: 'id_usuario', name: 'id_usuario', visible: false },
            { data: 'persona', name: 'persona' },
            { data: 'roles', name: 'roles' },
            { data: 'name', name: 'name' },
            { data: null,  render: function ( data, type, row ) {
							return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_usuario("+data.id_usuario+","+1+")' ><i class='fa fa-pencil-square-o'></i></button></td> <td><button type='button' class='btn btn-danger btn-xs' onclick='borrado_usuario("+data.id_usuario+")' ><i class='fa fa-fw fa-times'></i></button></td>"
							}
						},
    ]
    });

}
activar_tabla_usuarios();
</script>



@endsection
