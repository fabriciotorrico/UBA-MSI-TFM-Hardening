 @extends('layouts.auth')

@section('content')

  <body class="mybody">
    <div class="mytop-content" >
        <div class="container" >

                <div class="col-sm-12 " style="background-color:rgba(0, 0, 0, 0.35); height: 60px; " >
                   {{-- <a class="mybtn-social pull-right" href="{{ url('/register') }}">
                       Register
                  </a> --}}

                  <a class="mybtn-social pull-right" href="{{ url('/login') }}">
                       Login
                  </a>

                </div>

            <div class="row">
              <div class="col-sm-6 col-sm-offset-3 myform-cont" >


                     <div class="myform-top">
                        <div class="myform-top-left">
                           <img  src="{{ url('img/logo_principal.png') }}" class="img-responsive logo" />
                          <h3>En este momento no está permitido el registro online</h3>
                            <p>Disculpe las molestias</p>
                        </div>
                        <div class="myform-top-right">
                          <i class="fa fa-user-times"></i>
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
                   </div  >
              </div>
            </div>
            {{-- <div class="row">
                <div class="col-sm-12 mysocial-login">
                    <h3>...Visitanos en nuestra Pagina</h3>
                    <h1><strong>minculturas.gob.bo</strong>.net</h1>
                </div>
            </div> --}}
        </div>
      </div>

 </body>
@endsection
