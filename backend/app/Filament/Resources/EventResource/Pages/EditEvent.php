<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Concerns\HasTranslationFields;
use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    use HasTranslationFields;

    protected static string $resource = EventResource::class;

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
