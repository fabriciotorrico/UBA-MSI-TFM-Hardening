@extends('layouts.app')

@section('htmlheader_title')
	Listado de Clientes
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
        <h3 class="box-title">Nuevo Escaneo</h3>
        <div class="pull-right box-tools">
        <!--a href="{{route('import_contacto')}}" class="btn btn-default">
            <i class="fa fa-fw fa-file-excel-o text-green"></i> Importar
        </a-->
        </div>
		</div>

		<div class="margin" id="botones_control">
				<!--a href="javascript:void(0);" class="btn btn-xs btn-success" onclick="cargar_formulario(104);">Nuevo escaneo</a-->
				<a href="{{ url("/listado_clientes_perfiles") }}"  class="btn btn-xs btn-default" >Actualizar Listado</a>
		</div>

		<div class="box-body table-responsive">
		  <table id="tabla_clientes_perfiles" class="table table-hover table-striped table-bordered">
			<thead>
				<th>Id cliente perfil</th>
				<th>Cliente</th>
				<th>Descripción</th>
				<th>Dirección IP</th>
				<th>Política</th>
				<th>Perfil</th>
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
 function activar_tabla_clientes_perfiles() {
    var t = $('#tabla_clientes_perfiles').DataTable({

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
    ajax: '{!! url('buscar_clientes_perfiles') !!}',
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
			{ data: 'id_cliente_perfil', name: 'id_cliente_perfil', visible: false },
			{ data: 'nombre', name: 'nombre' },
            { data: 'descripcion', name: 'descripcion' },
            { data: 'direccion_ip', name: 'direccion_ip' },
            { data: 'politica', name: 'politica' },
            { data: 'perfil', name: 'perfil' },
            { data: null,  render: function ( data, type, row ) {
								//if ($("#rol_usuario").val() === 'admin' || $("#rol_usuario").val() === 'super_admin') {
									//if ( row.perfil === "Activo") {
										//return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_cliente("+data.id_cliente+","+1+")'> <i class='fa fa-pencil-square-o'> Editar Cliente </i></button></td> <td><button type='button' class='btn btn-danger btn-xs' onclick='verinfo_cliente("+data.id_cliente+","+2+")' ><i class='fa fa-times'> Anular cliente </i></button></td>"
										return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_cliente("+data.id_cliente_perfil+","+2+")'> <i class='fa fa-play'> Escanear </i></button></td> "
									//} else {
										//return "<td><button type='button' class='btn btn-warning btn-xs' onclick='verinfo_cliente("+data.id_clinete+","+3+")' ><i class='fa fa-check'> Habilitar clinete</i></button></td>"
									//}
								//}
							}
						},
    ]
    });

}
activar_tabla_clientes_perfiles();
</script>



@endsection
