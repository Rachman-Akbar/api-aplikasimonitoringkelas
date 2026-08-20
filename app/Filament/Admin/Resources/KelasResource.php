<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KelasResource\Pages;
use App\Filament\Imports\KelasImporter;
use App\Models\Kelas;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Pages\Enums\SubNavigationPosition;
use UnitEnum;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Akademik';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Kelas')
                    ->description('Data dasar kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kelas')
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                'kelas',
                                'nama',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Kelas::where('nama', $state)->first();
                                    if ($existing) {
                                        $set('nama', '');
                                        \Filament\Notifications\Notification::make()
                                            ->title('Nama kelas sudah terdaftar')
                                            ->body('Silakan gunakan nama lain.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('tingkat')
                            ->label('Tingkat')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12),

                        Forms\Components\Select::make('jurusan')
                            ->label('Jurusan')
                            ->required()
                            ->options(function () {
                                return \App\Models\Kelas::query()
                                    ->select('jurusan')
                                    ->distinct()
                                    ->whereNotNull('jurusan')
                                    ->orderBy('jurusan')
                                    ->pluck('jurusan', 'jurusan')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('jurusan')
                                    ->label('Jurusan Baru')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->helperText('Pilih dari jurusan yang sudah ada atau tambahkan yang baru'),

                        Forms\Components\Select::make('wali_kelas_id')
                            ->label('Wali Kelas')
                            ->relationship('waliKelas', 'nama')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('kapasitas')
                            ->label('Kapasitas')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('jumlah_siswa')
                            ->label('Jumlah Siswa')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('ruangan')
                            ->label('Ruangan')
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->default('aktif')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('waliKelas.nama')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kapasitas')
                    ->label('Kapasitas')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_siswa')
                    ->label('Jumlah Siswa')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'aktif' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
                Tables\Filters\SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                        6 => '6',
                        7 => '7',
                        8 => '8',
                        9 => '9',
                        10 => '10',
                        11 => '11',
                        12 => '12',
                    ]),
                Tables\Filters\SelectFilter::make('jurusan')
                    ->label('Jurusan')
                    ->options(function () {
                        return \App\Models\Kelas::query()
                            ->select('jurusan')
                            ->distinct()
                            ->whereNotNull('jurusan')
                            ->orderBy('jurusan')
                            ->pluck('jurusan', 'jurusan')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('wali_kelas_id')
                    ->label('Wali Kelas')
                    ->relationship('waliKelas', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('nama', 'asc')
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->action(fn($record) => $record->forceDelete()),
                Actions\ForceDeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->action(fn(\Illuminate\Database\Eloquent\Collection $records) => $records->each->forceDelete()),
                Actions\ForceDeleteBulkAction::make(),
                Actions\RestoreBulkAction::make(),
                ExportAction::make()
                    ->exports([
                        ExcelExport::make('kelas')
                            ->fromTable()
                            ->withColumns([
                                Column::make('id'),
                                Column::make('nama'),
                                Column::make('tingkat'),
                                Column::make('jurusan'),
                                Column::make('wali_kelas_id')->heading('Wali Kelas ID'),
                                Column::make('waliKelas.nama')->heading('Wali Kelas'),
                                Column::make('kapasitas'),
                                Column::make('jumlah_siswa'),
                                Column::make('ruangan'),
                                Column::make('status'),
                                Column::make('created_at'),
                                Column::make('updated_at'),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
