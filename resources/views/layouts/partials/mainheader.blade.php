<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ url('/home') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b><img src="{{asset('/img/logo_mainheader.png')}}" style="width:30px;height:30px;" class="centered" alt="User Image"/></b></span>
        <div class="row centered logo-lg">
            {{-- <span><b>A</b></span> --}}
            <img src="{{asset('/img/logo_mainheader.png')}}" style="width:50px;height:50px;" class="centered" alt="User Image"/>
        </div>
        <!-- logo for regular state and mobile devices -->
        {{-- <span class="logo-lg"><b>S-C</b></span> --}}
    </a>

    <!-- Header Navbar -->
    <!--nav class="navbar navbar-static-top" role="navigation"-->
    <nav class="navbar" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ trans('adminlte_lang::message.togglenav') }}</span>
            <b>Menú</b>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <!-- Menu toggle button -->
                    <!--a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-envelope-o"></i>
                        <span class="label label-success">1</span>
                    </a-->
                    <!--ul class="dropdown-menu">
                        {{-- <li class="header">{{ trans('adminlte_lang::message.tabmessages') }}</li> --}}
                        <li class="header">Mensajes</li>
                        <li>
                            <ul class="menu">
                                    {{-- <a href="#"> --}}
                                        <div class="pull-left">
                                            {{-- <img src="/img/user2-160x160.jpg" class="img-circle" alt="User Image"/> --}}
                                        </div>
                                        <h3 style="background-color:#00c0ef; text-align:center; color:white" id="clock"></h3>

                                </li>
                            </ul>
                        </li>
                    </ul-->
                </li>
                <!-- /.messages-menu -->

            @can('ver_notificaciones')
            @endcan

                @if (Auth::guest())
                    <!--li><a href="{{ url('/register') }}">{{ trans('adminlte_lang::message.register') }}</a></li>
                    <li><a href="{{ url('/login') }}">{{ trans('adminlte_lang::message.login') }}</a></li-->
                @else
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <!--img src="{{asset('/img/on_off.png')}}" class="user-image" alt="User Image"/-->
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            {{-- <span class="hidden-xs">{{ Auth::user()->name }}</span> --}}
                            <span class="text-black"><i class="fa fa-gear text-black"></i> Mis Datos</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <p>
                                    {{ $personas_logueadas['nombre'] ?? '' }} {{ $personas_logueadas['paterno'] ?? '' }} {{ $personas_logueadas['materno'] ?? '' }}
                                </p>
                                <p>
                                    Usuario: {{ Auth::user()->name }}
                                    <!--small><script>
                                        var meses = new Array ("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                                        var f=new Date();
                                        document.write(f.getDate() + " de " + meses[f.getMonth()] + " de " + f.getFullYear());
                                    </script></small-->
                                </p>
                                <a onclick='editar_mis_datos(1)' class="btn btn-flat btn-block"  >
                                 <label class="text-black "><i class="fa fa-edit "></i> Editar mis Datos Personales</label>
                                </a>
                                <a onclick='editar_mis_datos(2)' class="btn btn-flat btn-block"  >
                                  <label class="text-black "><i class="fa fa-expeditedssl"></i> Editar mi contraseña </label>
                                </a>
                                {{--
                                @foreach(Auth::user()->getRolesDescription() as $roles)
                                   <span class="label label-default">
                                     {{ $roles }}
                                   </span>
                                   <br>
                                @endforeach
                                --}}
                            </li>
                            <!-- Menu Body -->
                            <!-- Menu Footer-->
                            <li class="user-footer">
                              <div class="">
                                 <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-flat btn-block">
                                    <label class="text-black "><i class="fa fa-power-off text-black"></i> Salir</label>
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                              </div>
                            </li>
                        </ul>
                    </li>
                @endif

            </ul>
        </div>
    </nav>
</header>
