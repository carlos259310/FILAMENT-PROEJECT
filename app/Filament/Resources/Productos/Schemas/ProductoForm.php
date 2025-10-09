<?php

namespace App\Filament\Resources\Productos\Schemas;


use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;

class ProductoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema ->schema([
            TextInput::make('nombre')
                ->label('Nombre del Producto')
                ->required()
                ->maxLength(255)
                ->columnSpanFull()
                ->placeholder('Ej: Laptop HP Pavilion 15'),
            
            Select::make('id_categoria')
                ->label('Categoría')
                ->relationship('categoria', 'nombre')
                ->required()
                ->searchable()
                ->preload(),
            
            Select::make('id_marca')
                ->label('Marca')
                ->relationship('marca', 'nombre')
                ->required()
                ->searchable()
                ->preload(),
            
            Select::make('id_proveedor')
                ->label('Proveedor')
                ->relationship('proveedor', 'nombre')
                ->required()
                ->searchable()
                ->preload(),
            
            Placeholder::make('codigo_info')
                ->label('📋 Información de Códigos Automáticos')
                ->content(fn () => 
                    '✨ Los códigos se generarán automáticamente al guardar si los dejas vacíos.' . PHP_EOL .
                    '🔖 Código: ' . self::generarCodigoPreview() . PHP_EOL .
                    '� Código de Barras: ' . self::generarCodigoBarrasPreview()
                )
                ->columnSpanFull(),
            
            TextInput::make('codigo')
                ->label('Código del Producto')
                ->maxLength(50)
                ->placeholder('Dejar vacío para generar: ' . self::generarCodigoPreview())
                ->helperText('� Se generará automáticamente con formato: P-YYMMDDHHMM-XXX')
                ->suffixIcon('heroicon-o-qr-code')
                ->columnSpan(1),
            
            TextInput::make('codigo_barras')
                ->label('Código de Barras')
                ->maxLength(255)
                ->placeholder('Dejar vacío para generar: ' . self::generarCodigoBarrasPreview())
                ->helperText('📊 Se generará automáticamente con formato EAN-13 (13 dígitos)')
                ->suffixIcon('heroicon-o-bars-3-bottom-left')
                ->columnSpan(1),
            
            Textarea::make('descripcion')
                ->label('Descripción')
                ->rows(3)
                ->columnSpanFull()
                ->placeholder('Descripción detallada del producto (opcional)'),
            
            Toggle::make('activo')
                ->label('Producto Activo')
                ->default(true)
                ->inline(false)
                ->helperText('Desactiva el producto si ya no está disponible para venta'),
        ]);

    }

    /**
     * Genera un ejemplo de código para preview
     */
    private static function generarCodigoPreview(): string
    {
        $timestamp = now()->format('ymdHi');
        return "P-{$timestamp}-XXX";
    }

    /**
     * Genera un ejemplo de código de barras para preview
     */
    private static function generarCodigoBarrasPreview(): string
    {
        $timestamp = now()->format('ymdHis');
        return "7{$timestamp}";
    }
}

