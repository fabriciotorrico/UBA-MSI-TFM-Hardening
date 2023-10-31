<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Escaneo extends Model
{
  protected $table = 'escaneos';
  protected $primaryKey = 'id_escaneo';
  public $timestamps = true;
}
