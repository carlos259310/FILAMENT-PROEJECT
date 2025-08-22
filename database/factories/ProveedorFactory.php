<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'    => $this->faker->company,
            'codigo'    => $this->faker->unique()->bothify('PRV-####'),
            'nit'       => $this->faker->unique()->numerify('#########'),
            'email'     => $this->faker->unique()->safeEmail,
            'telefono'  => $this->faker->phoneNumber,
            'direccion' => $this->faker->address,
            'activo'    => $this->faker->boolean(90), // 90% probabilidad de true
        ];
    }
}
