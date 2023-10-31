<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $primaryKey = 'id_persona';
    public $timestamps = true;

    /**
     * Get the User record associated with the Person.
     */
    public function usuario()
    {
        return $this->hasOne('App\User', 'id_persona', 'id_persona');
    }
    /**
     * Get the User record associated with the Person.
     */

    public function roles_persona(){
        return $this->hasOne('\Caffeinated\Shinobi\Models\Role', 'id', 'id_rol');
    }
}
