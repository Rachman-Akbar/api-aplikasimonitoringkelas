<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JadwalResource\Pages;
use App\Filament\Imports\JadwalImporter;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
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

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Akademik';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Jadwal')
                    ->description('Detail jadwal pelajaran')
                    ->schema([
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama', function ($query) {
                                return $query->whereNull('deleted_at')
                                    ->where('status', 'aktif');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $mata_pelajaran_id = $get('mata_pelajaran_id');
                                    $guru_id = $get('guru_id');
                                    $hari = $get('hari');
                                    $jam_ke = $get('jam_ke');

                                    if ($mata_pelajaran_id && $guru_id && $hari && $jam_ke) {
                                        $existing = \App\Models\Jadwal::where('kelas_id', $state)
                                            ->where('mata_pelajaran_id', $mata_pelajaran_id)
                                            ->where('guru_id', $guru_id)
                                            ->where('hari', $hari)
                                            ->where('jam_ke', $jam_ke)
                                            ->first();

                                        if ($existing) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Jadwal sudah ada')
                                                ->body('Jadwal untuk kelas ini dengan mata pelajaran, guru, hari, dan jam yang sama sudah terdaftar.')
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                }
                            }),

                        Forms\Components\Select::make('mata_pelajaran_id')
                            ->label('Mata Pelajaran')
                            ->relationship('mataPelajaran', 'nama', function ($query) {
                                return $query->whereNull('deleted_at')
                                    ->where('status', 'aktif');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $kelas_id = $get('kelas_id');
                                    $guru_id = $get('guru_id');
                                    $hari = $get('hari');
                                    $jam_ke = $get('jam_ke');

                                    if ($kelas_id && $guru_id && $hari && $jam_ke) {
                                        $existing = \App\Models\Jadwal::where('kelas_id', $kelas_id)
                                            ->where('mata_pelajaran_id', $state)
                                            ->where('guru_id', $guru_id)
                                            ->where('hari', $hari)
                                            ->where('jam_ke', $jam_ke)
                                            ->first();

                                        if ($existing) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Jadwal sudah ada')
                                                ->body('Jadwal untuk kelas ini dengan mata pelajaran, guru, hari, dan jam yang sama sudah terdaftar.')
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                }
                            }),

                        Forms\Components\Select::make('guru_id')
                            ->label('Guru')
                            ->relationship('guru', 'nama', function ($query) {
                                return $query->whereNull('deleted_at')
                                    ->where('status', 'aktif');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $kelas_id = $get('kelas_id');
                                    $mata_pelajaran_id = $get('mata_pelajaran_id');
                                    $hari = $get('hari');
                                    $jam_ke = $get('jam_ke');

                                    if ($kelas_id && $mata_pelajaran_id && $hari && $jam_ke) {
                                        $existing = \App\Models\Jadwal::where('kelas_id', $kelas_id)
                                            ->where('mata_pelajaran_id', $mata_pelajaran_id)
                                            ->where('guru_id', $state)
                                            ->where('hari', $hari)
                                            ->where('jam_ke', $jam_ke)
                                            ->first();

                                        if ($existing) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Jadwal sudah ada')
                                                ->body('Jadwal untuk kelas ini dengan mata pelajaran, guru, hari, dan jam yang sama sudah terdaftar.')
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                }
                            }),

                        Forms\Components\Select::make('hari')
                            ->label('Hari')
                            ->options(function () {
                                // Get all unique hari from database
                                return \App\Models\Jadwal::query()
                                    ->select('hari')
                                    ->distinct()
                                    ->whereNotNull('hari')
                                    ->orderBy('hari')
                                    ->pluck('hari', 'hari')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $kelas_id = $get('kelas_id');
                                    $mata_pelajaran_id = $get('mata_pelajaran_id');
                                    $guru_id = $get('guru_id');
                                    $jam_ke = $get('jam_ke');

                                    if ($kelas_id && $mata_pelajaran_id && $guru_id && $jam_ke) {
                                        $existing = \App\Models\Jadwal::where('kelas_id', $kelas_id)
                                            ->where('mata_pelajaran_id', $mata_pelajaran_id)
                                            ->where('guru_id', $guru_id)
                                            ->where('hari', $state)
                                            ->where('jam_ke', $jam_ke)
                                            ->first();

                                        if ($existing) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Jadwal sudah ada')
                                                ->body('Jadwal untuk kelas ini dengan mata pelajaran, guru, hari, dan jam yang sama sudah terdaftar.')
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('jam_ke')
                            ->label('Jam Ke')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $kelas_id = $get('kelas_id');
                                    $mata_pelajaran_id = $get('mata_pelajaran_id');
                                    $guru_id = $get('guru_id');
                                    $hari = $get('hari');

                                    if ($kelas_id && $mata_pelajaran_id && $guru_id && $hari) {
                                        $existing = \App\Models\Jadwal::where('kelas_id', $kelas_id)
                                            ->where('mata_pelajaran_id', $mata_pelajaran_id)
                                            ->where('guru_id', $guru_id)
                                            ->where('hari', $hari)
                                            ->where('jam_ke', $state)
                                            ->first();

                                        if ($existing) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Jadwal sudah ada')
                                                ->body('Jadwal untuk kelas ini dengan mata pelajaran, guru, hari, dan jam yang sama sudah terdaftar.')
                                                ->warning()
                                                ->send();
                                        }
                                    }
                                }
                            }),

                        Forms\Components\TimePicker::make('jam_mulai')
                            ->label('Jam Mulai')
                            ->required()
                            ->native(false)
                            ->seconds(true)
                            ->format('h:i:s A')
                            ->displayFormat('h:i:s A')
                            ->validationMessages([
                                'required' => 'Jam mulai wajib diisi.',
                            ]),

                        Forms\Components\TimePicker::make('jam_selesai')
                            ->label('Jam Selesai')
                            ->required()
                            ->native(false)
                            ->seconds(true)
                            ->format('h:i:s A')
                            ->displayFormat('h:i:s A')
                            ->validationMessages([
                                'required' => 'Jam selesai wajib diisi.',
                            ]),

                        Forms\Components\TextInput::make('ruangan')
                            ->label('Ruangan')
                            ->maxLength(20),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(function () {
                                // Get all unique status from database
                                return \App\Models\Jadwal::query()
                                    ->select('status')
                                    ->distinct()
                                    ->whereNotNull('status')
                                    ->orderBy('status')
                                    ->pluck('status', 'status')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->default('aktif')
                            ->required(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->maxLength(255),
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
                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Guru')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_ke')
                    ->label('Jam Ke')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ruangan')
                    ->label('Ruangan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'warning',
                        'batal' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hari')
                    ->label('Hari')
                    ->options(function () {
                        // Get all unique hari from database
                        return \App\Models\Jadwal::query()
                            ->select('hari')
                            ->distinct()
                            ->whereNotNull('hari')
                            ->orderBy('hari')
                            ->pluck('hari', 'hari')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(function () {
                        // Get all unique status from database
                        return \App\Models\Jadwal::query()
                            ->select('status')
                            ->distinct()
                            ->whereNotNull('status')
                            ->orderBy('status')
                            ->pluck('status', 'status')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('guru_id')
                    ->label('Guru')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('hari', 'asc')
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
                        ExcelExport::make('jadwals')
                            ->fromTable()
                            ->withColumns([
                                Column::make('id'),
                                Column::make('kelas.nama')->heading('Kelas'),
                                Column::make('mataPelajaran.nama')->heading('Mata Pelajaran'),
                                Column::make('guru.nama')->heading('Guru'),
                                Column::make('hari'),
                                Column::make('jam_ke'),
                                Column::make('jam_mulai'),
                                Column::make('jam_selesai'),
                                Column::make('ruangan'),
                                Column::make('status'),
                                Column::make('keterangan'),
                                Column::make('created_at'),
                                Column::make('updated_at'),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwals::route('/'),
            'create' => Pages\CreateJadwal::route('/create'),
            'edit' => Pages\EditJadwal::route('/{record}/edit'),
        ];
    }
}
