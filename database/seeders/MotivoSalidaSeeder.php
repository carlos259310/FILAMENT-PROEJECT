<?php


namespace Database\Seeders;

use App\Models\MotivoSalida;
use Illuminate\Database\Seeder;

class MotivoSalidaSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            ['nombre' => 'Venta'],
            ['nombre' => 'Devolución'],
            ['nombre' => 'Ajuste de inventario'],
            ['nombre' => 'Donación'],
            ['nombre' => 'Transferencia'],
            ['nombre' => 'Producción']

        ];

        foreach ($motivos as $motivo) {
            MotivoSalida::create($motivo);
        }
    }
}
