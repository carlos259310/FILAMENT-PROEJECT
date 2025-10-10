<?php

namespace App\Filament\Resources\ReportesEntradas;

use App\Filament\Resources\ReportesEntradas\Pages\EntradasReport;
use App\Models\EntradaInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ReportesEntradasResource extends Resource
{
    protected static ?string $model = EntradaInventario::class;

    // Agrupa este recurso bajo el menú "Reportes"
    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static ?string $navigationLabel = 'Reportes de Entradas';

    protected static ?string $modelLabel = 'Reporte de Entradas';

    protected static ?string $pluralModelLabel = 'Reportes de Entradas';

    protected static ?int $navigationSort = 103;

    public static function canAccess(): bool
    {
        return Gate::allows('access-reportes');
    }

    public static function getPages(): array
    {
        return [
            'index' => EntradasReport::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
