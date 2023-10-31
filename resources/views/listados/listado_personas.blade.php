@extends('layouts.app')

@section('htmlheader_title')
	Listado de Personas
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
            <h3 class="box-title">Listado de Personas</h3>
            <input type="hidden" id="rol_usuario" value="{{ $rol->slug }}">
            <div class="pull-right box-tools">
            <!--a href="{{route('import_contacto')}}" class="btn btn-default">
                <i class="fa fa-fw fa-file-excel-o text-green"></i> Importar
            </a-->
              </div>
		</div>

		<div class="margin" id="botones_control">
							<a href="javascript:void(0);" class="btn btn-xs btn-success" onclick="cargar_formulario(1);">Agregar Persona</a>
							<a href="{{ url("/listado_personas") }}"  class="btn btn-xs btn-default" >Actualizar Listado</a>
							<!--a href="{{ url('form_agregar_persona') }}" class="btn btn-xs btn-default">Agregar Persona</a>
							<a href="{{ url("/listado_usuarios") }}"  class="btn btn-xs btn-default" >Listado Usuarios</a>
							<a href="javascript:void(0);" class="btn btn-xs btn-default" onclick="cargar_formulario(2);">Roles</a>
							<a href="javascript:void(0);" class="btn btn-xs btn-default" onclick="cargar_formulario(3);" >Permisos</a-->
		</div>

		<div class="box-body table-responsive">
		  <table id="tabla_personas" class="table table-hover table-striped table-bordered">
			<thead>
				<th>Nombre</th>
				<th>Cedula</th>
				<th>Nacimiento</th>
				<th>Teléfono</th>
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
 function activar_tabla_personas() {
    var t = $('#tabla_personas').DataTable({

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
		// buttons: [
        //           {
        //               extend: 'pdfHtml5',
        //               orientation: 'landscape',
        //               pageSize: 'LEGAL'
        //           }
        //         ],
        language: {
                 "url": '{!! asset('/plugins/datatables/latino.json') !!}'
                  } ,
        ajax: '{!! url('buscar_persona') !!}',
        columns: [
						//{ data: 'circunscripcion', name: 'circunscripcion' },
            //{ data: 'distrito', name: 'distrito' },
            // { data: 'distrito_referencial', name: 'distrito_referencial' },
            //{ data: 'nombre_recinto', name: 'nombre_recinto' },
            // { data: 'nombre', name: 'nombre' },
            // { data: 'paterno', name: 'paterno' },
            // { data: 'materno', name: 'materno' },
            { data: 'nombre_completo', name: 'nombre_completo' },
            { data: 'cedula_identidad', name: 'cedula_identidad' },
            // { data: 'complemento_cedula', name: 'complemento_cedula' },
            { data: 'fecha_nacimiento', name: 'fecha_nacimiento' },
            { data: 'telefono_celular', name: 'telefono_celular' },
            // { data: 'telefono_celular', name: 'telefono_celular' },
            // { data: 'telefono_referencia', name: 'telefono_referencia' },
            // { data: 'direccion', name: 'direccion' },
            // { data: 'grado_compromiso', name: 'grado_compromiso' },
            // { data: 'fecha_registro', name: 'fecha_registro' },
            // { data: 'activo', name: 'activo' },

            // { data: 'origen', name: 'origen'+'sub_origen' },
            // { data: 'sub_origen', name: 'sub_origen' },
            /*{ data: null,  render: function ( data, type, row ) {

				return row.origen + ' - ' + row.sub_origen;

			}
            },*/
            //{ data: 'description', name: 'description' },
            //{ data: 'titularidad', name: 'titularidad' },
            // { data: 'nombre_evidencia', name: 'nombre_evidencia' },

			{ data: null,  render: function ( data, type, row ) {
					//if ($("#rol_usuario").val() === 'admin' || $("#rol_usuario").val() === 'super_admin') {
						if ( row.activo === 1) {
						// return "<a href='{{ url('form_editar_contacto/') }}/"+ data.id +"' class='btn btn-xs btn-primary' >Editar</button>"
							return "<td><button type='button' class='btn btn-success btn-xs' onclick='verinfo_persona("+data.id_persona+","+1+")' ><i class='fa fa-pencil-square-o'></i></button></td> <td><button type='button' class='btn btn-danger btn-xs' onclick='verinfo_persona("+data.id_persona+","+2+")' ><i class='fa fa-fw fa-user-times'></i></button></td>"
						} else {
							return "<td><button type='button' class='btn btn-warning btn-xs' onclick='verinfo_persona("+data.id_persona+","+3+")' ><i class='fa fa-user-plus'></i></button></td>"
						}
					//}
				}
			},
        ]
    });

}
activar_tabla_personas();
</script>



@endsection
