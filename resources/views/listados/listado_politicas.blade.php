@extends('layouts.app')

@section('htmlheader_title')
	Listado de Politicas y Perfiles
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
        <h3 class="box-title">Listado de Políticas y Perfiles</h3>
        <div class="pull-right box-tools">
        <!--a href="{{route('import_contacto')}}" class="btn btn-default">
            <i class="fa fa-fw fa-file-excel-o text-green"></i> Importar
        </a-->
        </div>
		</div>

		<div class="margin" id="botones_control">
				<a href="javascript:void(0);" class="btn btn-xs btn-warning" onclick="cargar_formulario(101);">Cargar Nueva Política</a>
				<a href="javascript:void(0);" class="btn btn-xs btn-success" onclick="cargar_formulario(102);">Nuevo perfil personalizado</a>
				<a href="{{ url("/listado_politicas") }}"  class="btn btn-xs btn-default" >Actualizar Listado</a>
		</div>

		<div class="box-body table-responsive">
		  <table id="tabla_politicas" class="table table-hover table-striped table-bordered">
			<thead>
				<th>Id perfil</th>
				<th>Politica</th>
				<th>Perfil</th>
				<th>Descripción</th>
				<th>Tipo</th>
				<th>Estado</th>
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
 function activar_tabla_politicas() {
    var t = $('#tabla_politicas').DataTable({

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
    ajax: '{!! url('buscar_politicas') !!}',
		order: [ 0, 'desc'],
    columns: [
            /*{ data: null,  render: function ( data, type, row ) {
									if ( row.categoria === "domicilio") {
										return "<td><i class='fa fa-building-o'> Domicilio (Departamentos) </i></td> "
									} else if (row.categoria === "comercial") {
										return "<td><i class='fa fa-cart-plus'> Comercial (Locales Comerciales) </i></td> "
									}
							}
						},*/
						{ data: 'id_perfil', name: 'id_perfil', visible: false },
						{ data: 'politica_nombre', name: 'politica_nombre' },
            { data: 'perfil_title', name: 'perfil_title' },
            { data: 'perfil_description', name: 'perfil_description' },
            { data: 'perfil_tipo', name: 'perfil_tipo' },
            { data: 'estado', name: 'estado' },
            { data: null,  render: function ( data, type, row ) {
								//if ($("#rol_usuario").val() === 'admin' || $("#rol_usuario").val() === 'super_admin') {
									if ( row.perfil_tipo === "Custom") {
										return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_reglas("+data.id_perfil+","+1+")'> <i class='fa fa-pencil-square-o'> Editar Reglas </i></button></td>"
									} else {
										return "<td></td>"
									}
								//}
							}
						},
    ]
    });

}
activar_tabla_politicas();
</script>



@endsection
