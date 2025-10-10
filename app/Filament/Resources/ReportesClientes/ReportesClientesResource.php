<?php

namespace App\Filament\Resources\ReportesClientes;

use App\Filament\Resources\ReportesClientes\Pages\ClientesReport;
use App\Models\Cliente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ReportesClientesResource extends Resource
{
    protected static ?string $model = Cliente::class;



    // Agrupa este recurso bajo el menú "Reportes"
    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Reportes de Clientes';

    protected static ?string $modelLabel = 'Reporte de Cliente';

    protected static ?string $pluralModelLabel = 'Reportes de Clientes';

    protected static ?int $navigationSort = 101;

    public static function canAccess(): bool
    {
        return Gate::allows('access-reportes');
    }

    public static function getPages(): array
    {
        return [
            'index' => ClientesReport::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
