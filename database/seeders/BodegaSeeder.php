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
        Bodega::create([
            'nombre'     => 'Principal',
            'codigo'     => 'BOD-001',
            'ubicacion'  => 'Calle 1 #123, Centro',
            'descripcion' => 'Bodega principal de la empresa',
            'activo'     => true,
            'principal'  => true,
        ]);

        Bodega::create([
            'nombre'     => 'Secundaria',
            'codigo'     => 'BOD-002',
            'ubicacion'  => 'Calle 2 #456, Zona Industrial',
            'descripcion' => 'Bodega secundaria para soporte logístico',
            'activo'     => true,
            'principal'  => false,
        ]);

        // Bodega::factory()->count(5)->create();
    }
}
