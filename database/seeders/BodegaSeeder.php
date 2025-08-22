<?php

namespace Database\Seeders;

use App\Models\Bodega;
use Illuminate\Database\Seeder;

class BodegaSeeder extends Seeder
{
    public function run(): void
    {
        Bodega::factory()->count(5)->create();
    }
}