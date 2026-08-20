<?php

namespace App\Filament\Admin\Resources\KehadiranGurus\Tables;

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

class KehadiranGurusTable
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
                TextColumn::make('tanggal')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('guru.nama')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('guru.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('jadwal.hari')
                    ->label('Day')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jadwal.jam_ke')
                    ->label('Period')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jadwal.kelas.nama')
                    ->label('Class')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('jadwal.mataPelajaran.nama')
                    ->label('Subject')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'telat' => 'warning',
                        'tidak_hadir' => 'danger',
                        'izin' => 'info',
                        'sakit' => 'gray',
                        default => 'gray',
                    })
                    ->icons([
                        'heroicon-m-check-circle' => 'hadir',
                        'heroicon-m-clock' => 'telat',
                        'heroicon-m-x-circle' => 'tidak_hadir',
                        'heroicon-m-document-text' => 'izin',
                        'heroicon-m-heart' => 'sakit',
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('waktu_datang')
                    ->label('Arrival Time')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('durasi_keterlambatan')
                    ->label('Late Duration')
                    ->numeric()
                    ->sortable()
                    ->suffix(' min')
                    ->color('warning')
                    ->toggleable(),
                TextColumn::make('keterangan')
                    ->label('Notes')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_kehadiran')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'telat' => 'Telat',
                        'tidak_hadir' => 'Tidak Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                    ])
                    ->placeholder('All Status'),
                SelectFilter::make('guru_id')
                    ->label('Teacher')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jadwal.kelas_id')
                    ->label('Class')
                    ->relationship('jadwal.kelas', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jadwal.mata_pelajaran_id')
                    ->label('Subject')
                    ->relationship('jadwal.mataPelajaran', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jadwal.hari')
                    ->label('Day')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ]),
                TrashedFilter::make(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->striped()
            ->deferLoading()
            ->selectable()
            ->recordCheckboxPosition(\Filament\Tables\Enums\RecordCheckboxPosition::BeforeCells)
            ->actions([
                ViewAction::make()
                    ->label('View'),
                EditAction::make()
                    ->label('Edit'),
            ]);

        return $table->bulkActions([
            DeleteBulkAction::make()
                ->label('Delete Selected'),
            RestoreBulkAction::make()
                ->label('Restore Selected'),
            ForceDeleteBulkAction::make()
                ->label('Force Delete Selected'),
            ExportBulkAction::make()->exports([
                ExcelExport::make('table')
                    ->fromTable()
                    ->withFilename('teacher-attendance-data-' . date('Y-m-d'))
                    ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
            ])->label('Export Selected'),
        ]);
    }
}
