<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MarcaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->company,
            'codigo'      => $this->faker->unique()->bothify('MRC-###'),
            'descripcion' => $this->faker->sentence,
            'activo'      => $this->faker->boolean(90), // 90% probabilidad de true
        ];
    }
    }