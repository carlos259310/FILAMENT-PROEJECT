<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos_documentos=[
            ['documento' => 'Cédula de Ciudadanía', 'codigo' => 'CC'],
            ['documento' => 'Cédula de Extranjería', 'codigo' => 'CE'],
            ['documento' => 'Tarjeta de Identidad', 'codigo' => 'TI'],
            ['documento' => 'Registro Civil', 'codigo' => 'RC'],
            ['documento' => 'Pasaporte', 'codigo' => 'PA'],
            ['documento' => 'NIT', 'codigo' => 'NIT'],
            // Agrega más tipos de documentos según sea necesario
        ];
        //
        //declaro el array data
        $date=[];

        foreach ($tipos_documentos as $tipo_documento) {
            $date[] = [
                'documento' => $tipo_documento['documento'],
                'codigo' => $tipo_documento['codigo']
            ];
        }
        DB::table('tipos_documentos')->insert($date);
   
    }
}
