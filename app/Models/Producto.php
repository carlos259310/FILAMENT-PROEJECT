<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'codigo',
        'codigo_barras',
        'descripcion',
        'id_categoria',
        'id_marca',
        'id_proveedor',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Boot del modelo para generar códigos automáticos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            // Generar código automático si no se proporciona
            if (empty($producto->codigo)) {
                $producto->codigo = self::generarCodigo();
            }

            // Generar código de barras automático si no se proporciona
            if (empty($producto->codigo_barras)) {
                $producto->codigo_barras = self::generarCodigoBarras();
            }
        });
    }

    /**
     * Genera un código único para el producto
     * Formato: P-YYMMDDHHMM-RND (17 caracteres)
     * 
     * @return string
     */
    private static function generarCodigo(): string
    {
        // Formato compacto: YY+MM+DD+HH+MM (10 dígitos)
        $timestamp = now()->format('ymdHi');
        
        // Random de 3 caracteres
        $random = strtoupper(substr(uniqid(), -3));
        
        // P-YYMMDDHHMM-RND = 17 caracteres total
        return "P-{$timestamp}-{$random}";
    }

    /**
     * Genera un código de barras único en formato EAN-13 simulado
     * Formato: 7YYMMDDHHMMSS (13 dígitos)
     * 
     * @return string
     */
    private static function generarCodigoBarras(): string
    {
        // Prefijo 7 para productos internos
        $prefijo = '7';
        
        // Fecha y hora: YYMMDDHHMMSS (12 dígitos)
        $timestamp = now()->format('ymdHis');
        
        // Código de 13 dígitos
        return $prefijo . $timestamp;
    }


    //relaciones
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    // ...existing code...
    public function entradasInventario()
    {
        return $this->hasMany(EntradaInventario::class, 'id_producto');
    }
    // ...existing code...

    public function inventario()
    {
        return $this->hasMany(Inventario::class, 'id_producto');
    }


    // ...existing code...
    public function salidasInventario()
    {
        return $this->hasMany(SalidaInventario::class, 'id_producto');
    }
    // ...existing code...



}
