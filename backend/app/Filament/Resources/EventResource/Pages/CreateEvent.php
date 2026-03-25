<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    use HasTranslationFields;

    protected static string $resource = EventResource::class;

    protected function afterCreate(): void
    {
        $this->saveTranslationData($this->data);
    }
}
