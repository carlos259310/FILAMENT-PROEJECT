<?php

namespace App\Filament\Resources\Reportes;

use App\Filament\Resources\Reportes\Pages\VentasReport;
use App\Models\Factura;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class ReportesResource extends Resource
{
    protected static ?string $model = Factura::class;

    // Agrupa este recurso bajo el menú "Reportes"
    protected static string|\UnitEnum|null $navigationGroup = 'Reportes';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Reportes de Ventas';
    
    protected static ?string $modelLabel = 'Reporte de Ventas';
    
    protected static ?string $pluralModelLabel = 'Reportes de Ventas';
    
    protected static ?int $navigationSort = 100;

    public static function getPages(): array
    {
        return [
            'index' => VentasReport::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}