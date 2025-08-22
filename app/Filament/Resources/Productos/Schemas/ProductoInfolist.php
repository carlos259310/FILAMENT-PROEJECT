<?php

namespace App\Filament\Resources\Productos\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('codigo'),
                TextEntry::make('codigo_barras')
                    ->label('Código de Barras'),
                TextEntry::make('categoria.nombre')
                    ->label('Categoria'),
                TextEntry::make('proveedor.nombre')
                    ->label('Proveedor'),
                TextEntry::make('marca.nombre')
                    ->label('Marca'),
                IconEntry::make('activo')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
