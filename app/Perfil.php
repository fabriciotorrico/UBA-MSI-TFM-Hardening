<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
  protected $table = 'perfiles';
  protected $primaryKey = 'id_perfil';
  public $timestamps = true;
}
