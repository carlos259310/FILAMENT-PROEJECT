<?php

namespace App\Filament\Resources\ReportesSalidas;

use App\Filament\Resources\ReportesSalidas\Pages\SalidasReport;
use App\Models\SalidaInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ReportesSalidasResource extends Resource
{
    protected static ?string $model = SalidaInventario::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $navigationLabel = 'Reportes de Salidas';

    protected static ?string $modelLabel = 'Reporte de Salidas';

    protected static ?string $pluralModelLabel = 'Reportes de Salidas';

    protected static ?int $navigationSort = 104;

    public static function canAccess(): bool
    {
        return Gate::allows('access-reportes');
    }

    public static function getPages(): array
    {
        return [
            'index' => SalidasReport::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
