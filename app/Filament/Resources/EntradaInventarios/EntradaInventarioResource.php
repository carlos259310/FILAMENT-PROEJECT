<?php

namespace App\Filament\Resources\EntradaInventarios;

use App\Filament\Resources\EntradaInventarios\Pages\CreateEntradaInventario;
use App\Filament\Resources\EntradaInventarios\Pages\EditEntradaInventario;
use App\Filament\Resources\EntradaInventarios\Pages\ListEntradaInventarios;
use App\Filament\Resources\EntradaInventarios\Pages\ViewEntradaInventario;
use App\Filament\Resources\EntradaInventarios\Schemas\EntradaInventarioForm;
use App\Filament\Resources\EntradaInventarios\Schemas\EntradaInventarioInfolist;
use App\Filament\Resources\EntradaInventarios\Tables\EntradaInventariosTable;
use App\Models\EntradaInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EntradaInventarioResource extends Resource
{
    protected static ?string $model = EntradaInventario::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'EntradaInventario';

    public static function form(Schema $schema): Schema
    {
        return EntradaInventarioForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EntradaInventarioInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EntradaInventariosTable::configure($table);
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
            'index' => ListEntradaInventarios::route('/'),
            'create' => CreateEntradaInventario::route('/create'),
            'view' => ViewEntradaInventario::route('/{record}'),
            'edit' => EditEntradaInventario::route('/{record}/edit'),
        ];
    }
}
