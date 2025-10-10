<?php

namespace App\Filament\Resources\ReportesInventario;

use App\Filament\Resources\ReportesInventario\Pages\InventarioReport;
use App\Models\Inventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ReportesInventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;
    // Agrupa este recurso bajo el menú "Reportes"
    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCubeTransparent;

    protected static ?string $navigationLabel = 'Reportes de Inventario';
    
    protected static ?string $modelLabel = 'Reporte de Inventario';
    
    protected static ?string $pluralModelLabel = 'Reportes de Inventario';
    
    protected static ?int $navigationSort = 102;

    public static function canAccess(): bool
    {
        return Gate::allows('access-reportes');
    }

    public static function getPages(): array
    {
        return [
            'index' => InventarioReport::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
