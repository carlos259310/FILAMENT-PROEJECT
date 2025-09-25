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
                TextEntry::make('nombre_1')->label('Primer Nombre'),
                TextEntry::make('apellido_1')->label('Primer Apellido'),
                TextEntry::make('razon_social')->label('Razón Social'),
                TextEntry::make('tipoDocumento.nombre')->label('Tipo de Documento'),
                TextEntry::make('numero_documento')->label('N° Documento'),
                TextEntry::make('tipoPersona.nombre')->label('Tipo de Persona'),
                TextEntry::make('departamento.nombre')->label('Departamento'),
                TextEntry::make('ciudad.nombre')->label('Ciudad'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('telefono')->label('Teléfono'),
                IconEntry::make('activo')->boolean()->label('Activo'),
            ]);
    }
}