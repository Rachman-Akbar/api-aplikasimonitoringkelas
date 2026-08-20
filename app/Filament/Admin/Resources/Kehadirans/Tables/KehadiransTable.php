<?php

namespace App\Filament\Admin\Resources\Kehadirans\Tables;

use Filament\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class KehadiransTable
{
    public static function configure(Table $table): Table
    {
        $table = $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('siswa_id')
                    ->label('Siswa ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('jadwal_id')
                    ->label('Jadwal ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jadwal.hari')
                    ->label('Hari')
                    ->badge()
                    ->sortable(),
                TextColumn::make('jadwal.jam_ke')
                    ->label('Jam Ke')
                    ->sortable(),
                TextColumn::make('siswa.nama')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('siswa.kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jadwal.mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('jadwal.guru.nama')
                    ->label('Guru')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'sakit' => 'warning',
                        'izin' => 'info',
                        'tidak_hadir' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diupdate Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'tidak_hadir' => 'Tidak Hadir',
                    ]),
                SelectFilter::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
                    ->query(function ($query, $state) {
                        if ($state['value'] ?? null) {
                            return $query->whereHas('jadwal', fn($q) => $q->where('hari', $state['value']));
                        }
                    }),
                SelectFilter::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->query(function ($query, $state) {
                        if ($state['value'] ?? null) {
                            return $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $state['value']));
                        }
                    })
                    ->relationship('siswa.kelas', 'nama')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->selectable()
            ->recordCheckboxPosition(\Filament\Tables\Enums\RecordCheckboxPosition::BeforeCells)
            ->recordActions([
                ViewAction::make(),
                // Admin hanya monitoring, tidak edit/delete
            ])
            ->toolbarActions([
                // Admin tidak bisa delete bulk
            ]);

        return $table->bulkActions([
            DeleteBulkAction::make()
                ->label('Delete Selected')
                ->requiresConfirmation(),
            ExportBulkAction::make()->exports([
                ExcelExport::make('table')
                    ->fromTable()
                    ->withFilename('kehadiran-siswa-rekapan-' . date('Y-m-d'))
                    ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
            ])->label('Export Selected'),
        ]);
    }
}
