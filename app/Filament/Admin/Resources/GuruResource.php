<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GuruResource\Pages;
use App\Filament\Imports\GuruImporter;
use App\Models\Guru;
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

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Akademik';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Guru')
                    ->description('Data dasar guru')
                    ->schema([
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->maxLength(20)
                            ->unique(
                                'gurus',
                                'nip',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Guru::where('nip', $state)->first();
                                    if ($existing) {
                                        $set('nip', '');
                                        \Filament\Notifications\Notification::make()
                                            ->title('NIP sudah terdaftar')
                                            ->body('Silakan gunakan NIP lain.')
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
                                'gurus',
                                'nama',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Guru::where('nama', $state)->first();
                                    if ($existing) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Nama guru sudah terdaftar')
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
                                'gurus',
                                'email',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\Guru::where('email', $state)->first();
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

                Section::make('Akun Login (Opsional)')
                    ->description('Buat akun login untuk guru ini')
                    ->schema([
                        Forms\Components\Toggle::make('create_user_account')
                            ->label('Buat Akun Login')
                            ->live()
                            ->default(false)
                            ->helperText('Aktifkan untuk membuat akun login bagi guru ini'),

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn($get) => $get('create_user_account'))
                            ->visible(fn($get) => $get('create_user_account'))
                            ->minLength(8)
                            ->same('password_confirmation'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->required(fn($get) => $get('create_user_account'))
                            ->visible(fn($get) => $get('create_user_account'))
                            ->minLength(8),
                    ])
                    ->columns(2)
                    ->visibleOn('create'),
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
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('user_id')
                    ->label('Akun Login')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->getStateUsing(fn($record) => $record->user_id !== null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No. Telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn(string $state): string => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'L' ? 'info' : 'warning'),

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
                Tables\Filters\SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ]),
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
                        ExcelExport::make('gurus')
                            ->fromTable()
                            ->withColumns([
                                Column::make('id'),
                                Column::make('nip'),
                                Column::make('nama'),
                                Column::make('email'),
                                Column::make('no_telp'),
                                Column::make('alamat'),
                                Column::make('jenis_kelamin'),
                                Column::make('tanggal_lahir'),
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
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}
