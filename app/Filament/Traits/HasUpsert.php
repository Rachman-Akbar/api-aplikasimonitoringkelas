<?php

namespace App\Filament\Traits;

/**
 * Trait untuk menambahkan fitur UPSERT pada Filament Importer
 * 
 * Cara pakai:
 * 1. Tambahkan trait ini di Importer class
 * 2. Tentukan $uniqueBy property dengan field unique (contoh: 'nip', 'nis', 'email')
 * 3. resolveRecord() akan otomatis menggunakan logika upsert
 */
trait HasUpsert
{
    /**
     * Field yang digunakan untuk menentukan uniqueness
     * Override di class yang menggunakan trait ini
     * 
     * @var string|array
     */
    protected static string|array $uniqueBy = 'id';

    /**
     * Resolve record dengan logika UPSERT
     * Akan mencari record berdasarkan $uniqueBy
     * Jika ditemukan = UPDATE, jika tidak = INSERT
     */
    public function resolveRecord(): mixed
    {
        $model = static::getModel();
        $uniqueFields = is_array(static::$uniqueBy) ? static::$uniqueBy : [static::$uniqueBy];

        // Cari record berdasarkan unique fields
        $query = $model::query();
        $foundByField = null;

        foreach ($uniqueFields as $field) {
            if (!empty($this->data[$field])) {
                $record = (clone $query)->where($field, $this->data[$field])->first();

                if ($record) {
                    // Record ditemukan, akan di-update
                    \Log::info(class_basename($model) . ' ditemukan, akan diupdate', [
                        $field => $this->data[$field],
                        'id' => $record->id
                    ]);
                    return $record;
                }
            }
        }

        // Record tidak ditemukan, buat baru
        \Log::info(class_basename($model) . ' baru akan dibuat', [
            'unique_fields' => $uniqueFields
        ]);

        return new $model();
    }

    /**
     * Get unique fields untuk display di notification
     */
    protected function getUniqueFieldsForNotification(): string
    {
        $fields = is_array(static::$uniqueBy) ? static::$uniqueBy : [static::$uniqueBy];
        return implode(', ', $fields);
    }
}
