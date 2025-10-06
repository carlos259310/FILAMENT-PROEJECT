<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use App\Models\Ciudad;
use App\Models\TipoDocumento;

class ClientesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('id_tipo_persona')
                ->label('Tipo de Persona')
                ->relationship('tipoPersona', 'tipo_persona')
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    // Limpiar campos al cambiar tipo de persona
                    if ($state == 1) { // Natural
                        $set('razon_social', null);
                    } else { // Jurídica
                        $set('nombre_1', null);
                        $set('nombre_2', null);
                        $set('apellido_1', null);
                        $set('apellido_2', null);
                    }
                }),
            
            Select::make('id_tipo_documento')
                ->label('Tipo de Documento')
                ->options(function ($get) {
                    $tipoPersonaId = $get('id_tipo_persona');
                    
                    if ($tipoPersonaId == 2) { // Jurídica - Solo NIT
                        return TipoDocumento::where('codigo', 'NIT')
                            ->pluck('documento', 'id');
                    }
                    
                    // Natural - Todos excepto NIT
                    return TipoDocumento::where('codigo', '!=', 'NIT')
                        ->pluck('documento', 'id');
                })
                ->required()
                ->searchable()
                ->disabled(fn($get) => !$get('id_tipo_persona'))
                ->live(),
            
            TextInput::make('numero_documento')
                ->label('Número de Documento')
                ->required()
                ->numeric()
                ->maxLength(30),

            // Campos para Persona Natural
            TextInput::make('nombre_1')
                ->label('Primer Nombre')
                ->required(fn($get) => $get('id_tipo_persona') == 1)
                ->hidden(fn($get) => $get('id_tipo_persona') == 2)
                ->maxLength(100),
            
            TextInput::make('nombre_2')
                ->label('Segundo Nombre')
                ->hidden(fn($get) => $get('id_tipo_persona') == 2)
                ->maxLength(100),
            
            TextInput::make('apellido_1')
                ->label('Primer Apellido')
                ->required(fn($get) => $get('id_tipo_persona') == 1)
                ->hidden(fn($get) => $get('id_tipo_persona') == 2)
                ->maxLength(100),
            
            TextInput::make('apellido_2')
                ->label('Segundo Apellido')
                ->hidden(fn($get) => $get('id_tipo_persona') == 2)
                ->maxLength(100),

            // Campo para Persona Jurídica
            TextInput::make('razon_social')
                ->label('Razón Social')
                ->required(fn($get) => $get('id_tipo_persona') == 2)
                ->hidden(fn($get) => $get('id_tipo_persona') == 1)
                ->maxLength(255),

            TextInput::make('email')
                ->label('Correo Electrónico')
                ->email()
                ->maxLength(255),
            
            TextInput::make('telefono')
                ->label('Teléfono')
                ->required()
                ->length(10)
                ->regex('/^3\d{9}$/')
                ->helperText('Debe ser un número de 10 dígitos que inicie con 3')
                ->placeholder('3001234567'),
            
            TextInput::make('direccion')
                ->label('Dirección')
                ->required()
                ->maxLength(255),
            
            Select::make('id_departamento')
                ->label('Departamento')
                ->relationship('departamento', 'nombre')
                ->required()
                ->live()
                ->afterStateUpdated(fn(callable $set) => $set('id_ciudad', null)),
            
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
                ->label('Activo')
                ->default(true),
        ]);
    }
}
