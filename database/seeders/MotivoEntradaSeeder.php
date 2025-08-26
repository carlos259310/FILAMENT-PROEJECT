<?php


namespace Database\Seeders;

use App\Models\MotivoEntrada;
use Illuminate\Database\Seeder;

class MotivoEntradaSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            ['nombre' => 'Compra'],
            ['nombre' => 'Devolución'],
            ['nombre' => 'Ajuste de inventario'],
            ['nombre' => 'Donación'],
            ['nombre' => 'Transferencia'],
            ['nombre' => 'Producción'],
            ['nombre' => 'Otro'],
        ];

        foreach ($motivos as $motivo) {
            MotivoEntrada::create($motivo);
        }
    }
}