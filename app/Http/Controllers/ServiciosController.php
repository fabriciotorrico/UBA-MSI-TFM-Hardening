<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class ServiciosController extends Controller
{
    public function indexAPI()

    {
        $usuarios = User::orderBy('created_at', 'desc')->get();
        return $usuarios;
    }


}
