<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $fillable = ['nombre', 'codigo', 'id_departamento', 'activo'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_ciudad');
    }
}