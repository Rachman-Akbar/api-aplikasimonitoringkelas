<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Filament\Imports\UserImporter;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Pages\Enums\SubNavigationPosition;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Pengguna';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi User')
                    ->description('Informasi dasar pengguna')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\User::where('name', $state)->first();
                                    if ($existing) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Nama pengguna sudah terdaftar')
                                            ->body('Silakan gunakan nama lain.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(
                                'users',
                                'email',
                                ignoreRecord: true
                            )
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                                if ($operation === 'create') {
                                    $existing = \App\Models\User::where('email', $state)->first();
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

                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->visibleOn('create'),

                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(function () {
                                // Get all unique roles from database
                                return \App\Models\User::query()
                                    ->select('role')
                                    ->distinct()
                                    ->whereNotNull('role')
                                    ->orderBy('role')
                                    ->pluck('role', 'role')
                                    ->mapWithKeys(function ($role) {
                                        $roleLabels = [
                                            'admin' => 'Admin',
                                            'kepsek' => 'Kepala Sekolah',
                                            'kurikulum' => 'Kurikulum',
                                            'guru' => 'Guru',
                                            'siswa' => 'Siswa',
                                        ];
                                        return [$role => $roleLabels[$role] ?? ucfirst($role)];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('guru_id')
                            ->label('Data Guru')
                            ->relationship('guru', 'nama')
                            ->searchable()
                            ->preload()
                            ->visible(fn($get) => $get('role') === 'guru')
                            ->helperText('Hubungkan akun user ini dengan data guru'),

                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama')
                            ->searchable()
                            ->preload()
                            ->visible(fn($get) => $get('role') === 'siswa')
                            ->helperText('Hubungkan akun user ini dengan kelas siswa'),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'primary',
                        'kepsek' => 'warning',
                        'kurikulum' => 'success',
                        'guru' => 'info',
                        'siswa' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('guru.nama')
                    ->label('Data Guru')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('kelas.nama')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options(function () {
                        // Get all unique roles from database
                        return \App\Models\User::query()
                            ->select('role')
                            ->distinct()
                            ->whereNotNull('role')
                            ->orderBy('role')
                            ->pluck('role', 'role')
                            ->mapWithKeys(function ($role) {
                                $roleLabels = [
                                    'admin' => 'Admin',
                                    'kepsek' => 'Kepala Sekolah',
                                    'kurikulum' => 'Kurikulum',
                                    'guru' => 'Guru',
                                    'siswa' => 'Siswa',
                                ];
                                return [$role => $roleLabels[$role] ?? ucfirst($role)];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('guru_id')
                    ->label('Data Guru')
                    ->relationship('guru', 'nama')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                ExportAction::make()
                    ->exports([
                        ExcelExport::make('users')
                            ->fromTable()
                            ->withColumns([
                                Column::make('id'),
                                Column::make('name'),
                                Column::make('email'),
                                Column::make('role'),
                                Column::make('created_at'),
                                Column::make('updated_at'),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
