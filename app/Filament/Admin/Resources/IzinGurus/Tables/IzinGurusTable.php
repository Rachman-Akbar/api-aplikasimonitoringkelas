<?php

namespace App\Filament\Admin\Resources\IzinGurus\Tables;

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

class IzinGurusTable
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
                TextColumn::make('guru_id')
                    ->label('Guru ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('guru.nama')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) =>
                        match ($record->status_approval) {
                            'pending' => 'Pending',
                            'disetujui' => 'Disetujui',
                            'ditolak' => 'Ditolak',
                            default => ucfirst($record->status_approval),
                        }),
                TextColumn::make('guru.nip')
                    ->label('NIP Guru')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sakit' => 'danger',
                        'izin' => 'warning',
                        'cuti' => 'info',
                        'dinas_luar' => 'success',
                        'lainnya' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('durasi_hari')
                    ->label('Durasi')
                    ->numeric()
                    ->sortable()
                    ->suffix(' hari'),
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
                TextColumn::make('status_approval')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('disetujuiOleh.name')
                    ->label('Disetujui Oleh')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('tanggal_approval')
                    ->label('Tanggal Approval')
                    ->dateTime('d M Y H:i')
                    ->sortable()
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
                SelectFilter::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        'dinas_luar' => 'Dinas Luar',
                        'lainnya' => 'Lainnya',
                    ]),
                SelectFilter::make('status_approval')
                    ->label('Status Approval')
                    ->options([
                        'pending' => 'Pending',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->defaultSort('tanggal_mulai', 'desc')
            ->selectable()
            ->recordCheckboxPosition(\Filament\Tables\Enums\RecordCheckboxPosition::BeforeCells)
            ->recordActions([
                ViewAction::make(),
                // Admin hanya approve/reject, tidak edit/delete
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
                    ->withFilename('teacher-permission-data-' . date('Y-m-d'))
                    ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
            ])->label('Export Selected'),
        ]);
    }
}
