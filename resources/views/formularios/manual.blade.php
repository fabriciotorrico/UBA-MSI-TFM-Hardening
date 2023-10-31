@extends('layouts.app')

@section('htmlheader_title')
	Home
@endsection

@section('main-content')

<section class="content">
  <div class="col-md-0">
  </div>
  <div class="col-md-12 col-lg-12 col-xs-12">
    <div class="box box-primary">
			<embed src="{{asset('/documentos/manual.pdf')}}" type="application/pdf" width="100%" height="600px" />
    </div>
  </div>
</section>
@endsection
