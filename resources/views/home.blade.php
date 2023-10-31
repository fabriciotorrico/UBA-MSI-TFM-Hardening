@extends('layouts.app')

@section('htmlheader_title')
	Home
@endsection


@section('main-content')
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
	<div class="container spark-screen">
		<div class="row">
			{{-- <div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Bienvenid@</div>

					<div class="panel-body">
						{{ trans('adminlte_lang::message.logged') }}
						{{$personas}}
					</div>
				</div>
			</div> --}}
		<div class="col-md-2">
		</div>

		<div class="col-md-8">
			<!-- MAP & BOX PANE -->
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><b>Sistema de Hardening Centralizado</b></h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body no-padding">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="pad">
								<div style="height: 340px; text-align:center;">
									<img src="{{asset('img/logo_home.png')}}" style="height: 300px; width: 90%;">
								</div>
							</div>
						</div>
						<!-- /.col -->
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.box-body -->
			</div>
		</div>







		</div>
	</div>
@endsection
