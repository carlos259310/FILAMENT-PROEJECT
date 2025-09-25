<?php

namespace Database\Seeders;

use App\Models\Inventario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('inventarios')->delete();
        Inventario::factory()->count(10)->create();
    }
}
