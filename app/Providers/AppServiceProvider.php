<?php

namespace App\Providers;

use App\Persona;
use Illuminate\Support\ServiceProvider;
use Auth;
use App\User;


use App\UnidadFuncional;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function($view) {
        if (Auth::guest()) {
            $personas_logueadas = [];
        }
        else {
            $personas_logueadas = User::find(Auth::user()->id)->persona()->first();
        }
        $view->with('personas_logueadas', $personas_logueadas); 
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
