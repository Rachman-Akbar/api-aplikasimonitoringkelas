<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

final class RelationResolver
{
    public static function findOneByText(string $modelClass, string $column, mixed $value, string $label): ?Model
    {
        $normalized = TextNormalizer::lower($value);

        if ($normalized === null || $normalized === '') {
            return null;
        }

        $records = $modelClass::query()
            ->whereRaw("LOWER(TRIM({$column})) = ?", [$normalized])
            ->limit(2)
            ->get();

        if ($records->count() > 1) {
            throw ValidationException::withMessages([
                $column => "{$label} '{$value}' ditemukan lebih dari satu. Rapikan data master terlebih dahulu agar nama tidak ganda.",
            ]);
        }

        return $records->first();
    }

    public static function idByText(string $modelClass, string $column, mixed $value, string $label, bool $required = true): ?int
    {
        $record = self::findOneByText($modelClass, $column, $value, $label);

        if (!$record && $required) {
            throw ValidationException::withMessages([
                $column => "{$label} '{$value}' tidak ditemukan. Gunakan nama yang sudah terdaftar di data master.",
            ]);
        }

        return $record?->getKey();
    }
}
