<?php


namespace App\Filament\Resources\Clientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_1')
                    ->label('Primer Nombre')
                    ->searchable(),
                TextColumn::make('apellido_1')
                    ->label('Primer Apellido')
                    ->searchable(),
                TextColumn::make('razon_social')
                    ->label('Razón Social')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tipoDocumento.nombre')
                    ->label('Tipo Documento')
                    ->sortable(),
                TextColumn::make('numero_documento')
                    ->label('N° Documento')
                    ->searchable(),
                TextColumn::make('tipoPersona.nombre')
                    ->label('Tipo Persona')
                    ->sortable(),
                TextColumn::make('departamento.nombre')
                    ->label('Departamento')
                    ->sortable(),
                TextColumn::make('ciudad.nombre')
                    ->label('Ciudad')
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('telefono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
