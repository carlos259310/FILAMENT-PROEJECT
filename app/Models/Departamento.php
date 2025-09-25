<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{

       use HasFactory;
    protected $table = 'departamentos';
    protected $fillable = ['nombre', 'codigo', 'activo'];

    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'id_departamento');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_departamento');
    }
}