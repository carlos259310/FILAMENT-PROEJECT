<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
          /*  CategoriaSeeder::class,
            MarcaSeeder::class,
            ProveedorSeeder::class,
            BodegaSeeder::class,
            ProductoSeeder::class,
            InventarioSeeder::class*/
        ]);
        // Elimina todos los usuarios antes de crear el nuevo
        \App\Models\User::where('email', 'admin@example.com')->delete();


        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456')
        ]);
    }
}
