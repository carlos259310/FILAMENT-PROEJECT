<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CiudadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $ciudades = [
            ['codigo' => '05001', 'nombre' => 'MEDELLÍN', 'id_departamento' => 1],
            ['codigo' => '08001', 'nombre' => 'BARRANQUILLA', 'id_departamento' => 2],
            ['codigo' => '11001', 'nombre' => 'BOGOTÁ, D.C.', 'id_departamento' => 3],
            ['codigo' => '13001', 'nombre' => 'CARTAGENA', 'id_departamento' => 4],
            ['codigo' => '15001', 'nombre' => 'TUNJA', 'id_departamento' => 5],
            // Agrega más ciudades según sea necesario
            // Valle del Cauca
            ['codigo' => '76001', 'nombre' => 'CALI', 'id_departamento' => 24],
            ['codigo' => '68001', 'nombre' => 'BUCARAMANGA', 'id_departamento' => 21],

        ];
        //declaro el array data
        $data=[];

        foreach ($ciudades as $ciudad) {
            $data[] = [
                'codigo' => $ciudad['codigo'],
                'nombre' => $ciudad['nombre'],
                'id_departamento' => $ciudad['id_departamento']

            ];
        }
    
        DB::table('ciudades')->insert($data);
        // Insertar los departamentos en la tabla     
    
    }   
}
