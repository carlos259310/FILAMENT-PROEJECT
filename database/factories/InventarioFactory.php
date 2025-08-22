<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Bodega;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventarioFactory extends Factory
{
    public function definition(): array
    {
        $precioCompra = $this->faker->randomFloat(2, 1, 1000);
        $precioVenta = round($precioCompra * $this->faker->randomFloat(2, 1.1, 1.6), 2);

        return [
            'id_producto' => Producto::factory(),
            'id_bodega' => Bodega::factory(),
            'cantidad' => $this->faker->numberBetween(1, 100),
            'precio_compra' => $precioCompra,
            'precio_venta' => $precioVenta,
            'precio_compra_promedio' => $precioCompra,
            'precio_venta_promedio' => $precioVenta,
        ];
    }
}