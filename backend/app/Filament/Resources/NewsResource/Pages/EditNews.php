<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    use HasTranslationFields;

    protected static string $resource = NewsResource::class;

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
