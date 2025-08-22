<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = \App\Models\Producto::class;

    public function definition(): array
    {
        $precioCompra = $this->faker->randomFloat(2, 1, 1000);
        // margen de venta entre 1.1 y 1.6 sobre el precio de compra
        $precioVenta = round($precioCompra * $this->faker->randomFloat(2, 1.1, 1.6), 2);

        return [
            'nombre'         => $this->faker->words(3, true),
            'codigo'         => $this->faker->unique()->bothify('PRD-####'),
            'codigo_barras'  => $this->faker->optional(0.7)->ean13(),
            'descripcion'    => $this->faker->optional()->sentence(),
            'id_categoria'   => Categoria::factory(),
            'id_marca'       => Marca::factory(),
            'id_proveedor'   => Proveedor::factory(),
            'activo'         => $this->faker->boolean(90),
        ];
    }
}