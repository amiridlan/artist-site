<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components as FormComponents;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

trait HasTranslationFields
{
    protected static function getLocales(): array
    {
        return [
            'ms' => 'Malay (MY)',
            'ja' => 'Japanese (JP)',
        ];
    }

    /**
     * Build a translation Section with fields for each locale.
     * Each field key is prefixed: trans_{locale}_{field}
     *
     * @param array<string, string> $fields  ['field_name' => 'textarea'|'text'|'richEditor']
     */
    protected static function translationSection(array $fields): Section
    {
        $tabs = [];

        foreach (static::getLocales() as $locale => $label) {
            $formFields = [];

            foreach ($fields as $field => $type) {
                $key = "trans_{$locale}_{$field}";
                $fieldLabel = ucfirst(str_replace('_', ' ', $field)) . " ({$label})";

                $formFields[] = match ($type) {
                    'textarea' => FormComponents\Textarea::make($key)
                        ->label($fieldLabel)
                        ->rows(3),
                    'richEditor' => FormComponents\RichEditor::make($key)
                        ->label($fieldLabel),
                    default => FormComponents\TextInput::make($key)
                        ->label($fieldLabel)
                        ->maxLength(255),
                };
            }

            $tabs[] = Tab::make($label)
                ->icon($locale === 'ms' ? 'heroicon-o-flag' : 'heroicon-o-language')
                ->schema($formFields);
        }

        return Section::make('Translations')
            ->description('Optional translations for Malay and Japanese. Leave blank to keep English only.')
            ->schema([
                Tabs::make('translations')->tabs($tabs),
            ])
            ->collapsible()
            ->collapsed();
    }

    /**
     * Fill translation fields from the model's translations relation.
     * Call from mutateFormDataBeforeFill().
     */
    protected function fillTranslationData(array $data): array
    {
        $record = $this->record;

        if (!$record || !method_exists($record, 'getTranslatableFields')) {
            return $data;
        }

        $record->load('translations');

        foreach (static::getLocales() as $locale => $label) {
            foreach ($record->getTranslatableFields() as $field) {
                $key = "trans_{$locale}_{$field}";
                $translation = $record->translations
                    ->where('locale', $locale)
                    ->where('field', $field)
                    ->first();

                $data[$key] = $translation?->value ?? '';
            }
        }

        return $data;
    }

    /**
     * Save translation fields from form data.
     * Call from afterSave() or afterCreate().
     */
    protected function saveTranslationData(array $data): void
    {
        $record = $this->record;

        if (!$record || !method_exists($record, 'getTranslatableFields')) {
            return;
        }

        foreach (static::getLocales() as $locale => $label) {
            foreach ($record->getTranslatableFields() as $field) {
                $key = "trans_{$locale}_{$field}";
                $value = $data[$key] ?? '';

                if (!empty(trim($value))) {
                    $record->setTranslation($field, $locale, $value, autoTranslated: false);
                } else {
                    // Remove translation if field was cleared
                    $record->translations()
                        ->where('locale', $locale)
                        ->where('field', $field)
                        ->delete();
                }
            }
        }
    }
}
