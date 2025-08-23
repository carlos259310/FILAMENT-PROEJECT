<?php

namespace App\Filament\Resources\EntradaInventarios\Pages;

use App\Filament\Resources\EntradaInventarios\EntradaInventarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntradaInventarios extends ListRecords
{
    protected static string $resource = EntradaInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
