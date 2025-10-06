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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Reportes';
    
    protected static ?string $modelLabel = 'Reporte';
    
    protected static ?string $pluralModelLabel = 'Reportes';
    
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