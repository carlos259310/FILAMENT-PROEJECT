<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'id_cliente',
        'id_estado',
        'prefijo',
        'numero_factura',
        'fecha_factura',
        'subtotal',
        'total_impuesto',
        'total_factura',
    ];

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoFactura::class, 'id_estado');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(FacturaDetalle::class, 'id_factura');
    }
}