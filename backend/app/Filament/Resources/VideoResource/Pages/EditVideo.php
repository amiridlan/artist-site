<?php

namespace App\Filament\Resources\VideoResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\VideoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideo extends EditRecord
{
    use HasTranslationFields;

    protected static string $resource = VideoResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->fillTranslationData($data);
    }

    protected function afterSave(): void
    {
        $this->saveTranslationData($this->data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
