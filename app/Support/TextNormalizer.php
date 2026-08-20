<?php

namespace App\Support;

final class TextNormalizer
{
    public static function lower(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);

        return mb_strtolower($value, 'UTF-8');
    }

    public static function trim(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }
}
