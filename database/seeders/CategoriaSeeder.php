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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categorias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Categoria::factory()->count(10)->create();
    }
}
