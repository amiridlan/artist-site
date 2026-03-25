<?php

namespace App\Models\Concerns;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslations
{
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get all translatable field names for this model.
     */
    abstract public function getTranslatableFields(): array;

    /**
     * Get translated value for a field in a given locale.
     * Falls back to the original English value if no translation exists.
     */
    public function getTranslation(string $field, string $locale): ?string
    {
        if ($locale === 'en') {
            return $this->getAttribute($field);
        }

        $translation = $this->translations
            ->where('locale', $locale)
            ->where('field', $field)
            ->first();

        return $translation?->value ?? $this->getAttribute($field);
    }

    /**
     * Get all translations for a given locale as key-value pairs.
     */
    public function getTranslationsForLocale(string $locale): array
    {
        if ($locale === 'en') {
            return collect($this->getTranslatableFields())
                ->mapWithKeys(fn (string $field) => [$field => $this->getAttribute($field)])
                ->toArray();
        }

        $translations = $this->translations
            ->where('locale', $locale)
            ->pluck('value', 'field')
            ->toArray();

        // Fill missing translations with English fallback
        foreach ($this->getTranslatableFields() as $field) {
            if (!isset($translations[$field])) {
                $translations[$field] = $this->getAttribute($field);
            }
        }

        return $translations;
    }

    /**
     * Set a translation for a field.
     */
    public function setTranslation(string $field, string $locale, string $value, bool $autoTranslated = false): void
    {
        $this->translations()->updateOrCreate(
            [
                'locale' => $locale,
                'field' => $field,
            ],
            [
                'value' => $value,
                'auto_translated' => $autoTranslated,
            ]
        );
    }
}
