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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 20)->unique();
            $table->string('codigo_barras')->unique()->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('id_categoria')->constrained('categorias');
            $table->foreignId('id_marca')->constrained('marcas');
            $table->foreignId('id_proveedor')->constrained('proveedores');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
