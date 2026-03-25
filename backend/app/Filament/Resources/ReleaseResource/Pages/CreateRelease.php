<?php

namespace App\Filament\Resources\ReleaseResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\ReleaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRelease extends CreateRecord
{
    use HasTranslationFields;

    protected static string $resource = ReleaseResource::class;

    protected function afterCreate(): void
    {
        $this->saveTranslationData($this->data);
    }
}
