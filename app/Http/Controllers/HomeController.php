<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Personal;
use App\Areas;
use App\Unidad;

use Carbon\Carbon;
use DB;
use Auth;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {
      //Tomamos la fecha actual
      $date = new Carbon();
      $hoy = Carbon::now();
      //Tomamos el primer y ultimo dia del mes actual
      $primer_dia_del_mes = date("Y-m-d", strtotime("01-".strftime("%m-%Y", strtotime($hoy))));
      $ultimo_dia_del_mes = date("Y-m-t", strtotime("01-".strftime("%m-%Y", strtotime($hoy))));
      //Tomamos el mes actual y el anterior
      $meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
      //Tomamos el mes numeral, quitando el 0 si corresponde (devuelve enero->01)
      if (strftime("%m", strtotime($hoy))[0] == "0") {$mes_numeral = substr(strftime("%m", strtotime($hoy)), 1);}
      else {$mes_numeral = strftime("%m", strtotime($hoy));}
      $mes_actual = $meses[$mes_numeral];
      $mes_anterior_numeral = date('m', strtotime('-1 month')) ;
      if ($mes_anterior_numeral[0] == "0") {$mes_anterior_numeral = substr($mes_anterior_numeral, 1);}
      $mes_anterior = $meses[$mes_anterior_numeral];

      return view('home');
    }
}
