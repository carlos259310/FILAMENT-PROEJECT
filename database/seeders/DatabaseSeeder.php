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
            CategoriaSeeder::class,
            MarcaSeeder::class,
            ProveedorSeeder::class,
            BodegaSeeder::class,
            ProductoSeeder::class,
            InventarioSeeder::class,
            MotivoEntradaSeeder::class,
            DepartamentosSeeder::class,
            CiudadesSeeder::class,
            TipoPersonaSeeder::class,
            TipoDocumentoSeeder::class,
            EstadoFacturaSeeder::class
        ]);
        // Elimina todos los usuarios antes de crear el nuevo
        User::where('email', 'admin@example.com')->delete();
        User::where('email', 'administrativo@example.com')->delete();

        // Usuario Admin - Acceso total
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
        ]);

        // Usuario Administrativo - Solo facturación
        User::factory()->create([
            'name' => 'Administrativo',
            'email' => 'administrativo@example.com',
            'password' => Hash::make('123456'),
            'role' => 'administrativo',
        ]);
    }
}
