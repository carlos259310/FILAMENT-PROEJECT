<?php

namespace Database\Seeders;

use App\Models\Bodega;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BodegaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bodegas')->delete();

        Bodega::factory()->count(5)->create();
    }
}
