<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            
            TextInput::make('name')
                ->label('Nombre')
                ->required()
                ->maxLength(255)
                ->placeholder('Nombre completo del usuario')
                ->columnSpanFull(),
            
            TextInput::make('email')
                ->label('Correo Electrónico')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->placeholder('ejemplo@correo.com')
                ->columnSpan(1),
            
            Select::make('role')
                ->label('Rol')
                ->required()
                ->options([
                    'admin' => 'Administrador',
                    'administrativo' => 'Administrativo',
                ])
                ->default('administrativo')
                ->helperText('Admin: Acceso total | Administrativo: Solo facturación')
                ->native(false)
                ->columnSpan(1),
            
            TextInput::make('password')
                ->label('Contraseña')
                ->password()
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->revealable()
                ->minLength(6)
                ->maxLength(255)
                ->placeholder('Mínimo 6 caracteres')
                ->helperText('Deja en blanco para mantener la contraseña actual')
                ->columnSpan(1),
            
            TextInput::make('password_confirmation')
                ->label('Confirmar Contraseña')
                ->password()
                ->required(fn (string $context): bool => $context === 'create')
                ->dehydrated(false)
                ->revealable()
                ->same('password')
                ->placeholder('Repite la contraseña')
                ->columnSpan(1),
        ]);
    }
}
