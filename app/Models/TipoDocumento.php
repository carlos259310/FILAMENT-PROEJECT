<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{

    use HasFactory;
    protected $table = 'tipos_documentos';
    protected $fillable = ['documento', 'codigo', 'descripcion', 'activo'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_tipo_documento');
    }
}
