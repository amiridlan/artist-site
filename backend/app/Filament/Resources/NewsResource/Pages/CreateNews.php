<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    use HasTranslationFields;

    protected static string $resource = NewsResource::class;

    protected function afterCreate(): void
    {
        $this->saveTranslationData($this->data);
    }
}
