<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ciudades = [
            // Antioquia
            ['codigo' => '05001', 'nombre' => 'MEDELLÍN', 'dep_codigo' => '05'],
            ['codigo' => '05002', 'nombre' => 'ABEJORRAL', 'dep_codigo' => '05'],
            ['codigo' => '05004', 'nombre' => 'ABRIAQUÍ', 'dep_codigo' => '05'],
            ['codigo' => '05266', 'nombre' => 'ITAGÜÍ', 'dep_codigo' => '05'],
            ['codigo' => '05284', 'nombre' => 'LA ESTRELLA', 'dep_codigo' => '05'],
            ['codigo' => '05380', 'nombre' => 'RIONEGRO', 'dep_codigo' => '05'],
            // Atlántico
            ['codigo' => '08001', 'nombre' => 'BARRANQUILLA', 'dep_codigo' => '08'],
            ['codigo' => '08296', 'nombre' => 'MALAMBO', 'dep_codigo' => '08'],
            ['codigo' => '08372', 'nombre' => 'PUERTO COLOMBIA', 'dep_codigo' => '08'],
            // Bogotá D.C.
            ['codigo' => '11001', 'nombre' => 'BOGOTÁ, D.C.', 'dep_codigo' => '11'],
            // Bolívar
            ['codigo' => '13001', 'nombre' => 'CARTAGENA', 'dep_codigo' => '13'],
            ['codigo' => '13006', 'nombre' => 'ACHI', 'dep_codigo' => '13'],
            // Boyacá
            ['codigo' => '15001', 'nombre' => 'TUNJA', 'dep_codigo' => '15'],
            ['codigo' => '15022', 'nombre' => 'AQUITANIA', 'dep_codigo' => '15'],
            // Valle del Cauca
            ['codigo' => '76001', 'nombre' => 'CALI', 'dep_codigo' => '76'],
            ['codigo' => '76109', 'nombre' => 'BUENAVENTURA', 'dep_codigo' => '76'],
            ['codigo' => '76111', 'nombre' => 'BUGA', 'dep_codigo' => '76'],
            ['codigo' => '76834', 'nombre' => 'YUMBO', 'dep_codigo' => '76'],
            // Santander
            ['codigo' => '68001', 'nombre' => 'BUCARAMANGA', 'dep_codigo' => '68'],
            ['codigo' => '68276', 'nombre' => 'FLORIDABLANCA', 'dep_codigo' => '68'],
            ['codigo' => '68296', 'nombre' => 'GIRÓN', 'dep_codigo' => '68'],
            ['codigo' => '68307', 'nombre' => 'PIEDECUESTA', 'dep_codigo' => '68'],
            // Cundinamarca
            ['codigo' => '25001', 'nombre' => 'AGUA DE DIOS', 'dep_codigo' => '25'],
            ['codigo' => '25019', 'nombre' => 'ALBÁN', 'dep_codigo' => '25'],
            ['codigo' => '25286', 'nombre' => 'FACATATIVÁ', 'dep_codigo' => '25'],
            ['codigo' => '25754', 'nombre' => 'SOACHA', 'dep_codigo' => '25'],
            // Meta
            ['codigo' => '50001', 'nombre' => 'VILLAVICENCIO', 'dep_codigo' => '50'],
            // Norte de Santander
            ['codigo' => '54001', 'nombre' => 'CÚCUTA', 'dep_codigo' => '54'],
            // Magdalena
            ['codigo' => '47001', 'nombre' => 'SANTA MARTA', 'dep_codigo' => '47'],
            // Bolívar
            ['codigo' => '13052', 'nombre' => 'ARJONA', 'dep_codigo' => '13'],
            // Sucre
            ['codigo' => '70001', 'nombre' => 'SINCELEJO', 'dep_codigo' => '70'],
            // Tolima
            ['codigo' => '73001', 'nombre' => 'IBAGUÉ', 'dep_codigo' => '73'],
            // Huila
            ['codigo' => '41001', 'nombre' => 'NEIVA', 'dep_codigo' => '41'],
            // Risaralda
            ['codigo' => '66001', 'nombre' => 'PEREIRA', 'dep_codigo' => '66'],
            // Quindío
            ['codigo' => '63001', 'nombre' => 'ARMENIA', 'dep_codigo' => '63'],
            // Cesar
            ['codigo' => '20001', 'nombre' => 'VALLEDUPAR', 'dep_codigo' => '20'],
            // Córdoba
            ['codigo' => '23001', 'nombre' => 'MONTERÍA', 'dep_codigo' => '23'],
            // La Guajira
            ['codigo' => '44001', 'nombre' => 'RIOHACHA', 'dep_codigo' => '44'],
            // Amazonas
            ['codigo' => '91001', 'nombre' => 'LETICIA', 'dep_codigo' => '91'],
            // Casanare
            ['codigo' => '85001', 'nombre' => 'YOPAL', 'dep_codigo' => '85'],
            // Putumayo
            ['codigo' => '86001', 'nombre' => 'MOCOA', 'dep_codigo' => '86'],
            // Nariño
            ['codigo' => '52001', 'nombre' => 'PASTO', 'dep_codigo' => '52'],
            // Cauca
            ['codigo' => '19001', 'nombre' => 'POPAYÁN', 'dep_codigo' => '19'],
            // Arauca
            ['codigo' => '81001', 'nombre' => 'ARAUCA', 'dep_codigo' => '81'],
            // Guaviare
            ['codigo' => '95001', 'nombre' => 'SAN JOSÉ DEL GUAVIARE', 'dep_codigo' => '95'],
            // Vichada
            ['codigo' => '99001', 'nombre' => 'PUERTO CARREÑO', 'dep_codigo' => '99'],
            // Vaupés
            ['codigo' => '97001', 'nombre' => 'MITÚ', 'dep_codigo' => '97'],
            // Guainía
            ['codigo' => '94001', 'nombre' => 'INÍRIDA', 'dep_codigo' => '94'],
        ];

        $data = [];
        foreach ($ciudades as $ciudad) {
            $departamento = DB::table('departamentos')->where('codigo', $ciudad['dep_codigo'])->first();
            if ($departamento) {
                $data[] = [
                    'codigo' => $ciudad['codigo'],
                    'nombre' => $ciudad['nombre'],
                    'id_departamento' => $departamento->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('ciudades')->insert($data);
    }
}
