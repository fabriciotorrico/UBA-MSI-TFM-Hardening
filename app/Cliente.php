<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
  protected $table = 'clientes';
  protected $primaryKey = 'id_cliente';
  public $timestamps = true;
}
