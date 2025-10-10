<?php

namespace App\Filament\Resources\SalidaInventarios;

use App\Filament\Resources\SalidaInventarios\Pages\CreateSalidaInventario;
use App\Filament\Resources\SalidaInventarios\Pages\EditSalidaInventario;
use App\Filament\Resources\SalidaInventarios\Pages\ListSalidaInventarios;
use App\Filament\Resources\SalidaInventarios\Pages\ViewSalidaInventario;
use App\Filament\Resources\SalidaInventarios\Schemas\SalidaInventarioForm;
use App\Filament\Resources\SalidaInventarios\Schemas\SalidaInventarioInfolist;
use App\Filament\Resources\SalidaInventarios\Tables\SalidaInventariosTable;
use App\Models\SalidaInventario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class SalidaInventarioResource extends Resource
{
    protected static ?string $model = SalidaInventario::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventarios';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'SalidaInventario';

    public static function canAccess(): bool
    {
        return Gate::allows('access-inventario');
    }

    public static function form(Schema $schema): Schema
    {
        return SalidaInventarioForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SalidaInventarioInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalidaInventariosTable::configure($table);
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
            'index' => ListSalidaInventarios::route('/'),
            'create' => CreateSalidaInventario::route('/create'),
            'view' => ViewSalidaInventario::route('/{record}'),
            'edit' => EditSalidaInventario::route('/{record}/edit'),
        ];
    }
}
