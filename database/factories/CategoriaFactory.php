<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoriaFactory extends Factory
{
    use HasFactory;
    public function definition(): array
    {
        return [
            'nombre'      => $this->faker->word,
            'codigo'      => $this->faker->unique()->bothify('CAT-###'),
            'descripcion' => $this->faker->sentence,
            'activo'      => $this->faker->boolean(90), // 90% probabilidad de true
        ];
    }
}
