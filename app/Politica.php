<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Politica extends Model
{
  protected $table = 'politicas';
  protected $primaryKey = 'id_politica';
  public $timestamps = true;
}
