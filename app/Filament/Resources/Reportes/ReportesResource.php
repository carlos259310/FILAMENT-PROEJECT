<?php

namespace App\Filament\Resources\Reportes;

use App\Filament\Resources\Reportes\Pages\VentasReport;
use App\Models\Factura;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class ReportesVentasResource extends Resource
{
    protected static ?string $model = Factura::class;

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