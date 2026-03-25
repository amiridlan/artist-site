<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\VideoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVideo extends CreateRecord
{
    use HasTranslationFields;

    protected static string $resource = VideoResource::class;

    protected function afterCreate(): void
    {
        $this->saveTranslationData($this->data);
    }
}
