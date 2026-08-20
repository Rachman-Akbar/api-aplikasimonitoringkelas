<?php

namespace App\Models\Concerns;

use App\Support\TextNormalizer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\ValidationException;

trait NormalizesTextAttributes
{
    public static function bootNormalizesTextAttributes(): void
    {
        static::saving(function ($model): void {
            foreach ($model->lowercaseAttributes() as $attribute) {
                if ($model->getAttribute($attribute) !== null) {
                    $model->setAttribute($attribute, TextNormalizer::lower($model->getAttribute($attribute)));
                }
            }

            foreach ($model->trimmedAttributes() as $attribute) {
                if ($model->getAttribute($attribute) !== null) {
                    $model->setAttribute($attribute, TextNormalizer::trim($model->getAttribute($attribute)));
                }
            }

            foreach ($model->caseInsensitiveUniqueAttributes() as $attribute) {
                $value = TextNormalizer::lower($model->getAttribute($attribute));

                if ($value === null || $value === '') {
                    continue;
                }

                $query = static::query();

                if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
                    $query->withTrashed();
                }

                $query->whereRaw("LOWER(TRIM({$attribute})) = ?", [$value]);

                if ($model->exists) {
                    $query->where($model->getKeyName(), '!=', $model->getKey());
                }

                if ($query->exists()) {
                    throw ValidationException::withMessages([
                        $attribute => ucfirst(str_replace('_', ' ', $attribute)) . ' sudah terdaftar meskipun penulisan huruf besar/kecil berbeda.',
                    ]);
                }
            }
        });
    }

    protected function lowercaseAttributes(): array
    {
        return [];
    }

    protected function trimmedAttributes(): array
    {
        return [];
    }

    protected function caseInsensitiveUniqueAttributes(): array
    {
        return [];
    }
}
