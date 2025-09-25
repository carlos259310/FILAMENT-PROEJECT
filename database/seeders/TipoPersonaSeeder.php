<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos_personas = [
            ['tipo_persona' => 'Natural'],
            ['tipo_persona' => 'Jurídica'],
            // Agrega más tipos de personas según sea necesario
        ];
        //
        //declaro el array data
        $data = [];             
        foreach ($tipos_personas as $tipo_persona) {
            $data[] = [
                'tipo_persona' => $tipo_persona['tipo_persona'],
            ];
        }
        DB::table('tipos_personas')->insert($data);
    }
}
