<?php

namespace App\Filament\Imports;

use App\Models\MataPelajaran;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class MataPelajaranImporter extends Importer
{
    protected static ?string $model = \App\Models\MataPelajaran::class;

    public static function getSelectedColumns(): array
    {
        return ['kode', 'nama', 'deskripsi', 'sks', 'kategori', 'status'];
    }

    public static function getAcceptedFileTypes(): array
    {
        return [
            'text/csv',
            'text/plain',
            'text/x-csv',
            'application/csv',
            'application/x-csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/comma-separated-values',
            'text/x-comma-separated-values',
        ];
    }

    public static function getColumns(): array
    {
        return [
            \Filament\Actions\Imports\ImportColumn::make('kode')
                ->label('Kode')
                ->requiredMapping(true)
                ->guess(['kode', 'code', 'Kode', 'Code'])
                ->example('MTK001')
                ->rules(['required', 'string', 'max:20']),
            \Filament\Actions\Imports\ImportColumn::make('nama')
                ->label('Nama')
                ->requiredMapping(true)
                ->guess(['nama', 'name', 'Nama', 'Name', 'nama_mata_pelajaran'])
                ->example('Matematika')
                ->rules(['required', 'string', 'max:100']),
            \Filament\Actions\Imports\ImportColumn::make('deskripsi')
                ->label('Deskripsi')
                ->guess(['deskripsi', 'description', 'Deskripsi', 'Description', 'keterangan'])
                ->example('Mata pelajaran dasar matematika')
                ->rules(['nullable', 'string', 'max:255']),
            \Filament\Actions\Imports\ImportColumn::make('sks')
                ->label('SKS')
                ->guess(['sks', 'SKS', 'kredit', 'Sks'])
                ->example('3')
                ->rules(['nullable', 'integer', 'min:0', 'max:10']),
            \Filament\Actions\Imports\ImportColumn::make('kategori')
                ->label('Kategori')
                ->guess(['kategori', 'category', 'Kategori', 'Category', 'jenis'])
                ->example('Keahlian')
                ->rules(['nullable', 'string', 'max:50']),
            \Filament\Actions\Imports\ImportColumn::make('status')
                ->label('Status')
                ->guess(['status', 'Status', 'is_active', 'active'])
                ->example('aktif')
                ->rules(['nullable', 'string', 'in:aktif,nonaktif']),
        ];
    }

    public function resolveRecord(): ?\App\Models\MataPelajaran
    {
        try {
            // Cari by ID jika ada (untuk UPDATE)
            if (!empty($this->data['id'])) {
                $record = MataPelajaran::find($this->data['id']);
                if ($record) {
                    \Illuminate\Support\Facades\Log::info('Updating existing record by ID', ['id' => $this->data['id']]);
                    return $record;
                }
            }

            // Validate that required fields are present and not malformed
            if (empty($this->data['kode'])) {
                \Illuminate\Support\Facades\Log::error('Kode is empty in import data', ['data' => $this->data]);
                throw new \Exception('Kode mata pelajaran tidak boleh kosong.');
            }

            // Validate kode format
            if (strlen($this->data['kode']) > 50) {
                \Illuminate\Support\Facades\Log::error('Kode appears to contain merged data', [
                    'raw_data' => $this->data,
                    'kode' => $this->data['kode'],
                ]);
                throw new \Exception('Format kode tidak valid. Pastikan tidak ada penggabungan kolom dalam file CSV Anda.');
            }

            // Fallback: cari by kode (unique field) untuk INSERT/UPDATE
            if (!empty($this->data['kode'])) {
                // Use firstOrNew to get existing record or create new one
                $record = MataPelajaran::firstOrNew([
                    'kode' => $this->data['kode']
                ]);

                \Illuminate\Support\Facades\Log::info('Processing record by kode', [
                    'kode' => $this->data['kode'],
                    'exists' => $record->exists
                ]);

                // Return the record - it will be updated in beforeSave method
                return $record;
            }

            return new MataPelajaran();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating/updating record', [
                'error' => $e->getMessage(),
                'data' => $this->data,
            ]);
            throw $e;
        }
    }

    public function beforeValidate(): void
    {
        // Log the incoming data for debugging
        \Illuminate\Support\Facades\Log::info('MataPelajaran Import - Before Validation', [
            'data' => $this->data,
        ]);

        // Validate required fields exist and are not malformed
        if (!isset($this->data['kode']) || !isset($this->data['nama'])) {
            \Illuminate\Support\Facades\Log::error('Required fields missing from CSV row', [
                'data' => $this->data,
            ]);

            // Add user-friendly error to help with troubleshooting
            if (!isset($this->data['kode'])) {
                throw new \Exception('Kolom "kode" tidak ditemukan. Pastikan header kolom sesuai dengan template.');
            }
            if (!isset($this->data['nama'])) {
                throw new \Exception('Kolom "nama" tidak ditemukan. Pastikan header kolom sesuai dengan template.');
            }
        }

        // Check if the data looks malformed (merged fields)
        if (is_string($this->data['kode']) && strlen($this->data['kode']) > 50) { // Arbitrary length that suggests merged fields
            \Illuminate\Support\Facades\Log::warning('Possible malformed CSV data detected - code field too long', [
                'raw_data' => $this->data,
                'code_length' => strlen($this->data['kode']),
            ]);

            throw new \Exception('Format data tidak valid. Kolom "kode" terlalu panjang, mungkin tergabung dengan kolom lain. Pastikan file disimpan dalam format CSV yang benar.');
        }

        if (is_string($this->data['nama']) && strlen($this->data['nama']) > 200) { // Name field too long might indicate merged data
            \Illuminate\Support\Facades\Log::warning('Possible malformed CSV data detected - name field too long', [
                'raw_data' => $this->data,
                'name_length' => strlen($this->data['nama']),
            ]);

            throw new \Exception('Format data tidak valid. Kolom "nama" terlalu panjang, mungkin tergabung dengan kolom lain. Pastikan file disimpan dalam format CSV yang benar.');
        }
    }

    public function afterValidate(): void
    {
        // No-op after validation.
    }

    public function beforeSave(): void
    {
        // Use $this->record to adjust the model before save if necessary.
        if ($this->record) {
            try {
                // Clean and validate the data before applying to the model
                $cleanedData = [];

                // Only set kode for new records (preserve for existing records)
                if (!$this->record->exists && isset($this->data['kode'])) {
                    $kode = trim($this->data['kode']);
                    if (empty($kode)) {
                        throw new \Exception('Kode tidak boleh kosong');
                    }
                    $cleanedData['kode'] = $kode;
                }

                // Clean and collect all field changes
                if (isset($this->data['nama'])) {
                    $nama = trim($this->data['nama']);
                    if (empty($nama)) {
                        throw new \Exception('Nama tidak boleh kosong');
                    }
                    $cleanedData['nama'] = $nama;
                }

                if (isset($this->data['deskripsi'])) {
                    $cleanedData['deskripsi'] = trim($this->data['deskripsi']);
                }

                if (isset($this->data['kategori'])) {
                    // Normalize category to handle different formats (e.g., "wajib" vs "Wajib")
                    $cleanedData['kategori'] = $this->normalizeKategori(trim($this->data['kategori']));
                }

                // Ensure status has proper value
                $newStatus = 'aktif'; // Default status
                if (!empty($this->data['status'])) {
                    $status = strtolower(trim($this->data['status']));
                    if (in_array($status, ['1', 'true', 'yes', 'aktif', 'active'], true)) {
                        $newStatus = 'aktif';
                    } elseif (in_array($status, ['0', 'false', 'no', 'nonaktif', 'inactive'], true)) {
                        $newStatus = 'nonaktif';
                    } else {
                        $newStatus = in_array($status, ['aktif', 'nonaktif']) ? $status : 'aktif';
                    }
                }
                $cleanedData['status'] = $newStatus;

                // Handle SKS value
                if (!empty($this->data['sks']) && is_numeric($this->data['sks'])) {
                    $sksValue = (int) $this->data['sks'];
                    if ($sksValue < 0 || $sksValue > 10) {
                        throw new \Exception('SKS harus antara 0-10');
                    }
                    $cleanedData['sks'] = $sksValue;
                } elseif (!$this->record->exists) {
                    // Only set default SKS of 1 for new records
                    $cleanedData['sks'] = 1;
                }

                // Apply all cleaned changes to the model
                $this->record->fill($cleanedData);

                // Log the final values being saved
                \Illuminate\Support\Facades\Log::info('Mata Pelajaran preparing to save', [
                    'record_id' => $this->record->id,
                    'record_exists' => $this->record->exists,
                    'final_data' => $cleanedData
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error in beforeSave method', [
                    'error' => $e->getMessage(),
                    'data' => $this->data,
                    'record' => $this->record->toArray(),
                ]);
                throw $e;
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('Record is null in beforeSave method');
            throw new \Exception('Tidak ada data untuk disimpan. Pastikan file CSV memiliki format yang benar.');
        }
    }

    public function afterSave(): void
    {
        // Post-save hook; use $this->record if needed.
        if ($this->record) {
            Log::info('Mata Pelajaran berhasil diimport', [
                'id' => $this->record->id ?? 'null',
                'kode' => $this->record->kode ?? 'null',
                'nama' => $this->record->nama ?? 'null',
                'exists' => $this->record->exists,
                'wasRecentlyCreated' => $this->record->wasRecentlyCreated ?? false
            ]);
        } else {
            Log::warning('Mata Pelajaran import - Record is null after save');
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import mata pelajaran selesai! ';
        $body .= number_format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diimport.';
            $body .= ' Silakan periksa file Anda dan pastikan formatnya sesuai template.';
        }

        return $body;
    }

    private function normalizeKategori(string $kategori): string
    {
        // Normalize different forms of the same category to a consistent format
        $kategori = trim($kategori);
        $kategoriLower = strtolower($kategori);

        switch ($kategoriLower) {
            case 'wajib':
                return 'wajib';
            case 'pilihan':
                return 'pilihan';
            case 'muatan-lokal':
            case 'muatan lokal':
                return 'muatan-lokal';
            case 'keahlian':
                return 'Keahlian';
            case 'kejuruan':
                return 'Kejuruan';
            case 'normatif':
                return 'Normatif';
            case 'adaptif':
                return 'Adaptif';
            default:
                // If it doesn't match known values, return as-is but properly capitalized
                return $kategori;
        }
    }
}
