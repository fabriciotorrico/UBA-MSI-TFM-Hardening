@extends('layouts.app')

@section('htmlheader_title')
	Escaneos Realizados
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
        <h3 class="box-title">Escaneos Realizados</h3>
        <div class="pull-right box-tools">
        <!--a href="{{route('import_contacto')}}" class="btn btn-default">
            <i class="fa fa-fw fa-file-excel-o text-green"></i> Importar
        </a-->
        </div>
		</div>

		<div class="margin" id="botones_control">
				<!--a href="javascript:void(0);" class="btn btn-xs btn-success" onclick="cargar_formulario(104);">Nuevo escaneo</a-->
				<a href="{{ url("/listado_escaneos") }}"  class="btn btn-xs btn-default" >Actualizar Listado</a>
		</div>

		<div class="box-body table-responsive">
		  <input type="hidden" id="rol_usuario" value="{{ $rol_usuario }}">
		  <table id="tabla_escaneos" class="table table-hover table-striped table-bordered">
			<thead>
				<th>Id cliente perfil</th>
				<th>Cliente</th>
				<th>Política</th>
				<th>Perfil</th>
				<th>Escaneo</th>
				<th>Hardening</th>
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
 function activar_tabla_escaneos() {
    var t = $('#tabla_escaneos').DataTable({

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
    ajax: '{!! url('buscar_escaneos') !!}',
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
			{ data: 'id_escaneo', name: 'id_escaneo', visible: false },
			{ data: 'cliente', name: 'cliente' },
            { data: 'politica', name: 'politica' },
            { data: 'perfil', name: 'perfil' },
            { data: 'escaneo', name: 'escaneo' },
            { data: 'hardening', name: 'hardening' },
            { data: null,  render: function ( data, type, row ) {
								if ($("#rol_usuario").val() === 'admin') {
									if ( row.hardening === "No ejecutado") {
										return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+1+")'> <i class='fa fa-print'> Resultado Escaneo </i></button> <br> <button type='button' class='btn btn-danger btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+2+")'> <i class='fa fa-shield'> Ejecutar Hardening </i></button>"
									} else {
										return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+1+")'> <i class='fa fa-print'> Resultado Escaneo </i></button> <br> <button type='button' class='btn btn-danger btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+2+")'> <i class='fa fa-shield'> Ejecutar Hardening </i></button> <br> <button type='button' class='btn btn-info btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+3+")'> <i class='fa fa-terminal'> Resultado Hardening </i></button> </td>"
									}
								}
								else if ($("#rol_usuario").val() === 'auditor') {
									if ( row.hardening === "No ejecutado") {
										return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+1+")'> <i class='fa fa-print'> Resultado Escaneo </i></button>"
									} else {
										return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+1+")'> <i class='fa fa-print'> Resultado Escaneo </i></button> <br> <button type='button' class='btn btn-info btn-xs' onclick='verinfo_escaneo("+data.id_escaneo+","+3+")'> <i class='fa fa-terminal'> Resultado Hardening </i></button> </td>"
									}
								}
							}
						},
    ]
    });

}
activar_tabla_escaneos();
</script>



@endsection
