<?php

namespace App\Filament\Resources\SalidaInventarios\Pages;

use App\Filament\Resources\SalidaInventarios\SalidaInventarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalidaInventarios extends ListRecords
{
    protected static string $resource = SalidaInventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
