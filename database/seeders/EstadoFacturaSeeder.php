<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoFacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estados_factura')->insert([
            ['nombre' => 'Pendiente', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pagada', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cancelada', 'activo' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}