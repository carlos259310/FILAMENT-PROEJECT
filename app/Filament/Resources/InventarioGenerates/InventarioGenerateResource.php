<?php

namespace App\Filament\Resources\InventarioGenerates;

use App\Filament\Resources\InventarioGenerates\Pages\CreateInventarioGenerate;
use App\Filament\Resources\InventarioGenerates\Pages\EditInventarioGenerate;
use App\Filament\Resources\InventarioGenerates\Pages\ListInventarioGenerates;
use App\Filament\Resources\InventarioGenerates\Pages\ViewInventarioGenerate;
use App\Filament\Resources\InventarioGenerates\Schemas\InventarioGenerateForm;
use App\Filament\Resources\InventarioGenerates\Schemas\InventarioGenerateInfolist;
use App\Filament\Resources\InventarioGenerates\Tables\InventarioGeneratesTable;
use App\Models\Inventario;
use App\Models\InventarioGenerate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventarioGenerateResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'producto.nombre';
    
    protected static ?string $navigationLabel = 'Inventario';
    
    protected static ?string $modelLabel = 'Inventario';
    
    protected static ?string $pluralModelLabel = 'Inventarios';

    public static function form(Schema $schema): Schema
    {
        return InventarioGenerateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventarioGenerateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventarioGeneratesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventarioGenerates::route('/'),
            'view' => ViewInventarioGenerate::route('/{record}'),
        ];
    }
}
