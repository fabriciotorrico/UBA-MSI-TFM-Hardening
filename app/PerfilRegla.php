<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerfilRegla extends Model
{
  protected $table = 'perfiles_reglas';
  protected $primaryKey = 'id_perfil_regla';
  public $timestamps = true;
}
