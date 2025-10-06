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
                TextColumn::make('tipoPersona.tipo_persona')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Natural' => 'success',
                        'Jurídica' => 'info',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('nombre_completo')
                    ->label('Nombre / Razón Social')
                    ->searchable(['nombre_1', 'apellido_1', 'razon_social'])
                    ->getStateUsing(function ($record) {
                        if ($record->tipoPersona->tipo_persona === 'Natural') {
                            $nombre = trim($record->nombre_1 . ' ' . ($record->nombre_2 ?? ''));
                            $apellido = trim($record->apellido_1 . ' ' . ($record->apellido_2 ?? ''));
                            return trim($nombre . ' ' . $apellido);
                        }
                        return $record->razon_social;
                    })
                    ->sortable(),
                
                TextColumn::make('tipoDocumento.documento')
                    ->label('Tipo Documento')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('numero_documento')
                    ->label('N° Documento')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Documento copiado')
                    ->copyMessageDuration(1500),
                
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),
                
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                
                TextColumn::make('departamento.nombre')
                    ->label('Departamento')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('ciudad.nombre')
                    ->label('Ciudad')
                    ->sortable()
                    ->searchable(),
                
                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
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
            ])
            ->defaultSort('created_at', 'desc');
    }
}
