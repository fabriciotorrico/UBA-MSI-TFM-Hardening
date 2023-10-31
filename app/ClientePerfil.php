<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientePerfil extends Model
{
  protected $table = 'clientes_perfiles';
  protected $primaryKey = 'id_cliente_perfil';
  public $timestamps = true;
}
