<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_factura')->constrained('facturas');
            $table->foreignId('id_producto')->constrained('productos');
            $table->foreignId('id_bodega')->constrained('bodegas');
            $table->integer('cantidad');
            $table->decimal('precio_venta', 8, 2);
            $table->decimal('impuesto', 8, 2);
            $table->bigInteger('porcentaje_impuesto');
            $table->decimal('subtotal_linea', 8, 2);
        $table->decimal('total_linea', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas_detalle');
    }
};
