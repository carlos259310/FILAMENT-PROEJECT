<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class ClientesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tipoPersona.tipo_persona')
                    ->label('Tipo de Persona')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Natural' => 'success',
                        'Jurídica' => 'info',
                        default => 'gray',
                    }),
                
                IconEntry::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                // Campos para Persona Natural
                TextEntry::make('nombre_1')
                    ->label('Primer Nombre')
                    ->visible(fn($record) => $record->tipoPersona->tipo_persona === 'Natural'),
                
                TextEntry::make('nombre_2')
                    ->label('Segundo Nombre')
                    ->visible(fn($record) => $record->tipoPersona->tipo_persona === 'Natural')
                    ->placeholder('N/A'),
                
                TextEntry::make('apellido_1')
                    ->label('Primer Apellido')
                    ->visible(fn($record) => $record->tipoPersona->tipo_persona === 'Natural'),
                
                TextEntry::make('apellido_2')
                    ->label('Segundo Apellido')
                    ->visible(fn($record) => $record->tipoPersona->tipo_persona === 'Natural')
                    ->placeholder('N/A'),
                
                // Campo para Persona Jurídica
                TextEntry::make('razon_social')
                    ->label('Razón Social')
                    ->visible(fn($record) => $record->tipoPersona->tipo_persona === 'Jurídica'),
                
                TextEntry::make('tipoDocumento.documento')
                    ->label('Tipo de Documento'),
                
                TextEntry::make('numero_documento')
                    ->label('N° Documento')
                    ->copyable()
                    ->copyMessage('Documento copiado'),
                
                TextEntry::make('email')
                    ->label('Correo Electrónico')
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->placeholder('No registrado'),
                
                TextEntry::make('telefono')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->copyable(),
                
                TextEntry::make('direccion')
                    ->label('Dirección')
                    ->icon('heroicon-o-map-pin'),
                
                TextEntry::make('departamento.nombre')
                    ->label('Departamento')
                    ->icon('heroicon-o-map'),
                
                TextEntry::make('ciudad.nombre')
                    ->label('Ciudad')
                    ->icon('heroicon-o-map-pin'),
                
                TextEntry::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y H:i:s'),
                
                TextEntry::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i:s'),
            ]);
    }
}