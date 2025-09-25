<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ...en tu seeder...
        DB::table('categorias')->delete();
        Categoria::factory()->count(10)->create();
    }
}
