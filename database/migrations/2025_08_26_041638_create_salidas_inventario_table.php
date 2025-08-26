<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salidas_inventario', function (Blueprint $table) {
            $table->id();

            // Relaciones con otras tablas
            $table->foreignId('id_producto')->constrained('productos');
            $table->foreignId('id_bodega')->constrained('bodegas');
            $table->foreignId('id_motivo')->constrained('motivos_salida'); // Necesitarás crear esta tabla

            // Datos de la salida
            $table->integer('cantidad');
            $table->decimal('precio_costo', 10, 2)->nullable(); // Precio al momento de la salida
            $table->decimal('precio_venta', 10, 2)->nullable(); // Precio de venta si aplica

            // Información adicional
            $table->string('numero_factura')->nullable(); // Factura, remisión, etc.
            $table->text('observacion')->nullable();

            // Auditoría
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salidas_inventario');
    }
};
