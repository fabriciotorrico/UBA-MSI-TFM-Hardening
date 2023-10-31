<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regla extends Model
{
  protected $table = 'reglas';
  protected $primaryKey = 'id_regla';
  public $timestamps = true;
}
