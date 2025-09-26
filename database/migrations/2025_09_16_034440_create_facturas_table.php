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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente');
            $table->foreignId('id_estado')->constrained('estados_factura');
            $table->string('prefijo', 20);
            $table->string('numero_factura', 20);
            $table->date('fecha_factura');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_impuesto', 15, 2)->default(0);
            $table->decimal('total_factura', 15, 2)->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
