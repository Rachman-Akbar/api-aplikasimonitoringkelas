<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SiswaResource\Pages;
use App\Filament\Imports\SiswaImporter;
use App\Models\Kelas;
use App\Models\Siswa;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
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

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Akademik';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Siswa')
                    ->description('Data dasar siswa')
                    ->schema([
                        Forms\Components\TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->maxLength(20)
                            ->unique(
                                'siswas',
                                'nis',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Siswa::where('nis', $state)->first();
                                    if ($existing) {
                                        $set('nis', '');
                                        \Filament\Notifications\Notification::make()
                                            ->title('NIS sudah terdaftar')
                                            ->body('Silakan gunakan NIS lain.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->maxLength(20)
                            ->unique(
                                'siswas',
                                'nisn',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Siswa::where('nisn', $state)->first();
                                    if ($existing) {
                                        $set('nisn', '');
                                        \Filament\Notifications\Notification::make()
                                            ->title('NISN sudah terdaftar')
                                            ->body('Silakan gunakan NISN lain.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                'siswas',
                                'nama',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Siswa::where('nama', $state)->first();
                                    if ($existing) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Nama siswa sudah terdaftar')
                                            ->body('Silakan gunakan nama lain.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(
                                'siswas',
                                'email',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Siswa::where('email', $state)->first();
                                    if ($existing) {
                                        $set('email', '');
                                        \Filament\Notifications\Notification::make()
                                            ->title('Email sudah terdaftar')
                                            ->body('Silakan gunakan email lain.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->maxLength(500)
                            ->rows(3),

                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir')
                            ->maxDate(now()),

                        Forms\Components\TextInput::make('nama_orang_tua')
                            ->label('Nama Orang Tua')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('no_telp_orang_tua')
                            ->label('No. Telepon Orang Tua')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(function () {
                                // Get all unique status from database
                                return \App\Models\Siswa::query()
                                    ->select('status')
                                    ->distinct()
                                    ->whereNotNull('status')
                                    ->orderBy('status')
                                    ->pluck('status', 'status')
                                    ->mapWithKeys(function ($status) {
                                        $statusLabels = [
                                            'aktif' => 'Aktif',
                                            'nonaktif' => 'Nonaktif',
                                            'lulus' => 'Lulus',
                                            'pindah' => 'Pindah',
                                        ];
                                        return [$status => $statusLabels[$status] ?? ucfirst($status)];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->default('aktif')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Akun Login (Opsional)')
                    ->description('Biarkan kosong jika tidak ingin membuat akun login untuk siswa ini')
                    ->schema([
                        Forms\Components\Toggle::make('create_user_account')
                            ->label('Buat Akun Login')
                            ->live()
                            ->default(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn($get) => $get('create_user_account'))
                            ->visible(fn($get) => $get('create_user_account'))
                            ->minLength(8)
                            ->maxLength(255)
                            ->label('Password'),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->dehydrated(false)
                            ->required(fn($get) => $get('create_user_account'))
                            ->visible(fn($get) => $get('create_user_account'))
                            ->same('password')
                            ->label('Konfirmasi Password'),
                    ])
                    ->columns(2)
                    ->visible(fn(string $operation) => $operation === 'create'),
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
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'L' ? 'info' : 'warning'),

                Tables\Columns\IconColumn::make('user_id')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->label('Akun Login')
                    ->tooltip(fn($record) => $record->user_id ? 'Sudah punya akun login' : 'Belum punya akun login'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        'lulus' => 'info',
                        'pindah' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(function () {
                        // Get all unique status from database
                        return \App\Models\Siswa::query()
                            ->select('status')
                            ->distinct()
                            ->whereNotNull('status')
                            ->orderBy('status')
                            ->pluck('status', 'status')
                            ->mapWithKeys(function ($status) {
                                $statusLabels = [
                                    'aktif' => 'Aktif',
                                    'nonaktif' => 'Nonaktif',
                                    'lulus' => 'Lulus',
                                    'pindah' => 'Pindah',
                                ];
                                return [$status => $statusLabels[$status] ?? ucfirst($status)];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
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
                        ExcelExport::make('siswas')
                            ->fromTable()
                            ->withColumns([
                                Column::make('id'),
                                Column::make('nis'),
                                Column::make('nisn'),
                                Column::make('nama'),
                                Column::make('email'),
                                Column::make('no_telp'),
                                Column::make('kelas.nama')->heading('Kelas'),
                                Column::make('alamat'),
                                Column::make('jenis_kelamin'),
                                Column::make('tanggal_lahir'),
                                Column::make('nama_orang_tua'),
                                Column::make('no_telp_orang_tua'),
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
