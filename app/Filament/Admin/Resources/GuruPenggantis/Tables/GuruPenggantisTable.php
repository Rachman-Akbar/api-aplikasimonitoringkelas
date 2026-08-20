<?php

namespace App\Filament\Admin\Resources\GuruPenggantis\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class GuruPenggantisTable
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
                TextColumn::make('jadwal_id')
                    ->label('Jadwal ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jadwal.kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jadwal.mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('jadwal.hari')
                    ->label('Hari')
                    ->badge()
                    ->sortable(),
                TextColumn::make('jadwal.jam_ke')
                    ->label('Jam Ke')
                    ->sortable(),
                TextColumn::make('jadwal.jam_mulai')
                    ->label('Jam Mulai')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('jadwal.jam_selesai')
                    ->label('Jam Selesai')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('guruAsli.nama')
                    ->label('Guru Asli')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guruAsli.nip')
                    ->label('NIP Guru Asli')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('guruPengganti.nama')
                    ->label('Guru Pengganti')
                    ->searchable()
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
                TextColumn::make('guruPengganti.nip')
                    ->label('NIP Guru Pengganti')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_penggantian')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dijadwalkan' => 'warning',
                        'selesai' => 'success',
                        'tidak_hadir' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('catatan_approval')
                    ->label('Catatan Approval')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('disetujuiOleh.name')
                    ->label('Disetujui Oleh')
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
                SelectFilter::make('status_penggantian')
                    ->label('Status')
                    ->options([
                        'dijadwalkan' => 'Dijadwalkan',
                        'selesai' => 'Selesai',
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
                SelectFilter::make('guru_asli_id')
                    ->label('Guru Asli')
                    ->relationship('guruAsli', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('guru_pengganti_id')
                    ->label('Guru Pengganti')
                    ->relationship('guruPengganti', 'nama')
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
                    ->withFilename('substitute-teacher-data-' . date('Y-m-d'))
                    ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
            ])->label('Export Selected'),
        ]);
    }
}
