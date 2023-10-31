@extends('layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.pagenotfound') }}
@endsection

@section('contentheader_title')
    {{ trans('adminlte_lang::message.404error') }}
@endsection

@section('$contentheader_description')
@endsection

@section('main-content')

<div class="error-page">
    <h2 class="headline text-red"> 403</h2>
    <div class="error-content">
        <h3><i class="fa fa-exclamation text-red"></i> Oops! Contenido no permitido.</h3>
        <p>
            No tienes permiso para acceder al contenido solicitado.
            {{ trans('adminlte_lang::message.mainwhile') }} <a href='{{ url('/home') }}'>{{ trans('adminlte_lang::message.returndashboard') }}</a> o comunícate con el administrador si crees que se trata de un error.
        </p>
        <form class='search-form'>
            <div class='input-group'>
                <!--input type="text" name="search" class='form-control' placeholder="{{ trans('adminlte_lang::message.search') }}"/>
                <div class="input-group-btn">
                    <button type="submit" name="submit" class="btn btn-warning btn-flat"><i class="fa fa-search"></i></button>
                </div-->
            </div><!-- /.input-group -->
        </form>
    </div><!-- /.error-content -->
</div><!-- /.error-page -->
@endsection
