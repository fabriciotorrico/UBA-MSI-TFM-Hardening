<?php
/**
 * Created by PhpStorm.
 * User: ghost
 * Date: 29/1/2017
 * Time: 02:42
 */

if(! function_exists('limpiar')){
    function limpiar($string){
        $clean_output = preg_replace("/[A-Za-z]*:[ ]/", "", $string);
        $clean_output = preg_replace("/[(][0-9]*[)][ ]/", "", $clean_output);
        $clean_output = str_replace("Gauge32", "", $clean_output);
        $clean_output = str_replace("Counter32", "", $clean_output);
        $clean_output = preg_replace("/[(][0-9][)]/", "", $clean_output);
        return $clean_output;
    }
}

if(! function_exists('pings')) {
    function pings($ip)
    {
            $output = array();
            exec("ping -n 1 $ip", $output, $status);
            $t= substr($output[8], -8);
            $t=intval(preg_replace('/[^0-9]+/', '', $t),10);
            return $t;
    }
}

if(!function_exists('fecha2text')){
    function fecha2text($fecha){
        $texto = $fecha->format('Y-m-d');
        return $texto;
    }
}

if(!function_exists('f_formato')){
    function f_formato($fecha){
        return date("d/m/Y", strtotime($fecha));
    }
}

if(!function_exists('f_formato_array')){
    function f_formato_array($fecha){
        $f = '';
        $fechas = explode(", ", $fecha);
        $fecha = sort($fechas);
        // implode($array,",");
        if (count($fechas) > 1) {
            foreach ($fechas as &$value ) {
                $value = f_formato($value);
            }
            $f = implode(", ", $fechas);
        }
        else{
            $f = f_formato($fechas[0]);
        }
        return $f;
    }
}

if(!function_exists('salto_n')){
    function salto_n($fecha1, $fecha2, $n){

        $f1 = explode(", ", $fecha1);
        $f2 = explode(", ", $fecha2);
        // implode($array,",");
        $num1 = count($f1);
        $num2 = count($f2);

        $num1 = ceil($num1/$n);
        $num2 = ceil($num2/$n);

        $num = $num1 - $num2;
        $text = "";
        for ($i=0; $i <= $num; $i++) {
            $text = $text."\r\n";
        }
        return $text;
    }
}

if(!function_exists('dif_fechas')){
    function dif_fechas($fecha1, $fecha2){
        $anio = $fecha1->diff($fecha2);
        $days = $anio->days;

        return $days;
    }
}
