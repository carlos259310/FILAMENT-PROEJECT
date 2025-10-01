<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Ciudad;

class ClientesForm
{
    public static function configure(Schema $schema): Schema
    {

        return $schema->schema([
            Select::make('id_tipo_persona')
                ->label('Tipo de Persona')
                ->relationship('tipoPersona', 'tipo_persona')
                ->required(),
            Select::make('id_tipo_documento')
                ->label('Tipo de Documento')
                ->relationship('tipoDocumento', 'documento')
                ->required(),
            TextInput::make('numero_documento')
                ->label('Número de Documento')
                ->required()
                ->maxLength(30),
            TextInput::make('nombre_1')
                ->label('Primer Nombre')
                ->required()
                ->maxLength(100),
            TextInput::make('nombre_2')
                ->label('Segundo Nombre')
                ->maxLength(100),
            TextInput::make('apellido_1')
                ->label('Primer Apellido')
                ->required()
                ->maxLength(100),
            TextInput::make('apellido_2')
                ->label('Segundo Apellido')
                ->maxLength(100),
            TextInput::make('razon_social')
                ->label('Razón Social')
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->maxLength(255),
            TextInput::make('telefono')
                ->maxLength(30),
            TextInput::make('direccion')
                ->required()
                ->maxLength(255),
            Select::make('id_departamento')
                ->label('Departamento')
                ->relationship('departamento', 'nombre')
                ->required()
                ->live(),
            Select::make('id_ciudad')
                ->label('Ciudad')
                ->options(function ($get) {
                    $departamentoId = $get('id_departamento');
                    if (!$departamentoId) {
                        return [];
                    }
                    return Ciudad::where('id_departamento', $departamentoId)
                        ->orderBy('nombre')
                        ->pluck('nombre', 'id');
                })
                ->required()
                ->searchable()
                ->disabled(fn($get) => !$get('id_departamento'))
                ->live(),
            Toggle::make('activo')
                ->default(true),
        ]);
    }
}
