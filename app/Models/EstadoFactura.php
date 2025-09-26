<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoFactura extends Model
{
    use HasFactory;

    protected $table = 'estados_factura';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    // Relación con facturas
    public function facturas(): HasMany
    {
        return $this->hasMany(Factura::class, 'id_estado');
    }
}