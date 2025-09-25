<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{

       use HasFactory;
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $fillable = [
        'nombre_1', 'nombre_2', 'apellido_1', 'apellido_2',
        'id_tipo_documento', 'id_tipo_persona', 'id_ciudad', 'id_departamento',
        'numero_documento', 'razon_social', 'email', 'telefono', 'direccion', 'activo'
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }

    public function tipoPersona()
    {
        return $this->belongsTo(TipoPersona::class, 'id_tipo_persona');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }
}