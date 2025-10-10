<?php

namespace App\Filament\Resources\Clientes;

use App\Filament\Resources\Clientes\Pages\CreateClientes;
use App\Filament\Resources\Clientes\Pages\EditClientes;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Filament\Resources\Clientes\Pages\ViewClientes;
use App\Filament\Resources\Clientes\Schemas\ClientesForm;
use App\Filament\Resources\Clientes\Schemas\ClientesInfolist;
use App\Filament\Resources\Clientes\Tables\ClientesTable;
use App\Models\Cliente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;


class ClientesResource extends Resource
{
    protected static ?string $model = Cliente::class;


    protected static string|\UnitEnum|null $navigationGroup = 'Ventas';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Cliente';

    public static function canAccess(): bool
    {
        return Gate::allows('access-clientes');
    }

    public static function form(Schema $schema): Schema
    {
        return ClientesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientesTable::configure($table);
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
            'index' => ListClientes::route('/'),
            'create' => CreateClientes::route('/create'),
            'view' => ViewClientes::route('/{record}'),
            'edit' => EditClientes::route('/{record}/edit'),
        ];
    }
}
