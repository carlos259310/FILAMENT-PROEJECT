<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BodegaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company,
            'codigo' => $this->faker->unique()->bothify('BOD-###'),
            'ubicacion' => $this->faker->address,
            'descripcion' => $this->faker->sentence,
            'activo' => $this->faker->boolean(90),
            'principal'   => $this->faker->boolean(10), // 10% probabilidad de true
        
        ];
    }
}