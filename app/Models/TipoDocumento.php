<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipos_documento';
    protected $fillable = ['nombre', 'codigo', 'descripcion', 'activo'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_tipo_documento');
    }
}