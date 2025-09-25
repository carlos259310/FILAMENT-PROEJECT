<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model
{
       use HasFactory;
    protected $table = 'tipos_personas';
    protected $fillable = ['tipo_persona'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_tipo_persona');
    }
}