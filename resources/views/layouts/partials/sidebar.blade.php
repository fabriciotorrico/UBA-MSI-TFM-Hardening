<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        @if (! Auth::guest())
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{asset('/img/avatar.png')}}" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    {{-- <p>{{ Auth::user()->name }}</p>
                    <p><b>{{ $personas_logueadas['nombre'] }}</b></p>--}}

                    @if ($personas_logueadas['paterno'] == '')
                    <p>{{ $personas_logueadas['nombre'] }} {{ $personas_logueadas['materno'] }} </p>
                    @else
                    <p>{{ explode(" ", $personas_logueadas['nombre'], 2)[0] }} {{ $personas_logueadas['paterno'] }}</p>
                    @endif

                    @role('super_admin')
                      <p><i class="fa fa-caret-right text-red"></i> Administrador</p>
                    @endrole

                    {{-- <p><i class="fa fa-caret-right text-red"></i> </p> --}}
                </div>
            </div>
        @endif

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
          @can('menu_ver_configuraciones_iniciales')
            <li class="header">CONFIGURACIONES INICIALES </li>

            <li class="treeview">
                <a href="#"><i class='fa fa-user'></i> <span>Gestión de Usuarios</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="{{ url('listado_personas') }}">Listado de Personas</a></li>
                  <!--li><a href="javascript:void(0);" onclick="cargar_formulario(1);">Agregar Persona</a></li-->
                  <li><a href="{{ url('listado_usuarios') }}">Listado de Usuarios</a></li>
                  <!--li><a href="{{ url('form_nuevo_usuario_buscar') }}">Agregar Usuario</a></li-->
                </ul>
            </li>

            <li><a href="{{ url('listado_politicas') }}"><i class='fa fa-legal'></i>Políticas, perfiles y reglas</a></li>

            <li><a href="{{ url('listado_clientes') }}"><i class='fa fa-desktop'></i>Inventario de clientes </a></li>

         @endcan

         @can('menu_ver_tareas_recurrentes')
            <li class="header"> TAREAS RECURRENTES</li>            

            <li class="treeview">
                <a href="#"><i class='fa fa-heartbeat'></i> <span>Escaneo y Hardening</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="{{ url('listado_clientes_perfiles') }}">Escanear</a></li>
                  <li><a href="{{ url('listado_escaneos') }}">Resultados y Hardening</a></li>
                </ul>
            </li>

         @endcan

         <!--li class="treeview">
             <a href="{{ url('/manual') }}"><i class='fa fa-book'></i> <span>Manual</span> </a>
         </li-->

         <li class="treeview">
             <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class='fa fa-power-off'></i> <span>Salir</span> </a>
         </li>

          {{--
              @role('super_admin')
              @endrole
          --}}

        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
