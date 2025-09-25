<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model
{
    protected $table = 'tipos_persona';
    protected $fillable = ['nombre'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_tipo_persona');
    }
}