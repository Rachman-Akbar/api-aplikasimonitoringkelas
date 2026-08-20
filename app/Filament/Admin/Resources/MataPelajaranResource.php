<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Imports\MataPelajaranImporter;
use App\Filament\Admin\Resources\MataPelajaranResource\Pages;
use App\Filament\Admin\Resources\MataPelajaranResource\RelationManagers;
use App\Models\MataPelajaran;
use BackedEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Pages\Enums\SubNavigationPosition;
use UnitEnum;

class MataPelajaranResource extends Resource
{
    protected static ?string $model = MataPelajaran::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Mata Pelajaran';
    protected static ?string $modelLabel = 'Mata Pelajaran';
    protected static ?string $pluralModelLabel = 'Mata Pelajaran';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Akademik';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(20)
                    ->unique(
                        'mata_pelajarans',
                        'kode',
                        ignoreRecord: true
                    )
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                        if ($operation === 'create') {
                            $existing = \App\Models\MataPelajaran::where('kode', $state)->first();
                            if ($existing) {
                                $set('kode', '');
                                \Filament\Notifications\Notification::make()
                                    ->title('Kode mata pelajaran sudah ada')
                                    ->body('Silakan gunakan kode lain.')
                                    ->warning()
                                    ->send();
                            }
                        }
                    }),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(100)
                    ->unique(
                        'mata_pelajarans',
                        'nama',
                        ignoreRecord: true
                    )
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, $set, $get) {
                        if ($operation === 'create') {
                            $existing = \App\Models\MataPelajaran::where('nama', $state)->first();
                            if ($existing) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Nama mata pelajaran sudah ada')
                                    ->body('Silakan gunakan nama lain.')
                                    ->warning()
                                    ->send();
                            }
                        }
                    }),
                Forms\Components\Textarea::make('deskripsi')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sks')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10),
                Forms\Components\TextInput::make('kategori')
                    ->required()
                    ->maxLength(50)
                    ->datalist(function () {
                        // Get all unique categories from database
                        $categories = \App\Models\MataPelajaran::query()
                            ->select('kategori')
                            ->distinct()
                            ->whereNotNull('kategori')
                            ->where('kategori', '!=', '')
                            ->orderBy('kategori', 'asc')
                            ->pluck('kategori')
                            ->toArray();

                        // Add common categories
                        $commonCategories = [
                            'Normatif',
                            'Adaptif',
                            'Produktif',
                            'Keahlian',
                            'Kejuruan',
                            'Muatan Lokal',
                        ];

                        // Merge and remove duplicates
                        return array_values(array_unique(array_merge($commonCategories, $categories)));
                    })
                    ->helperText('Pilih dari saran atau ketik kategori baru')
                    ->placeholder('Ketik atau pilih kategori'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->native(false)
                    ->options(function () {
                        // Get all unique status from database
                        return \App\Models\MataPelajaran::query()
                            ->select('status')
                            ->distinct()
                            ->whereNotNull('status')
                            ->orderBy('status')
                            ->pluck('status', 'status')
                            ->mapWithKeys(function ($status) {
                                $statusLabels = [
                                    'aktif' => 'Aktif',
                                    'nonaktif' => 'Nonaktif',
                                ];
                                return [$status => $statusLabels[$status] ?? ucfirst($status)];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->default('aktif')
                    ->selectablePlaceholder(false),
            ])
            ->columns(2); // Add columns for better layout
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Keahlian' => 'info',
                        'Kejuruan' => 'secondary',
                        'Normatif' => 'success',
                        'Adaptif' => 'purple',
                        'wajib' => 'primary',
                        'pilihan' => 'warning',
                        'muatan-lokal' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options(function () {
                        // Cache the query result for 30 seconds to avoid multiple DB calls
                        // but still reflect new categories quickly
                        return cache()->remember('mata_pelajaran_kategori_options', 30, function () {
                            return \App\Models\MataPelajaran::query()
                                ->select('kategori')
                                ->distinct()
                                ->whereNotNull('kategori')
                                ->where('kategori', '!=', '')
                                ->orderBy('kategori', 'asc')
                                ->pluck('kategori', 'kategori')
                                ->toArray();
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->getOptionLabelUsing(fn($value): string => $value ?? '')
                    ->label('Kategori'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(function () {
                        // Get all unique status from database
                        return \App\Models\MataPelajaran::query()
                            ->select('status')
                            ->distinct()
                            ->whereNotNull('status')
                            ->orderBy('status')
                            ->pluck('status', 'status')
                            ->mapWithKeys(function ($status) {
                                $statusLabels = [
                                    'aktif' => 'Aktif',
                                    'nonaktif' => 'Nonaktif',
                                ];
                                return [$status => $statusLabels[$status] ?? ucfirst($status)];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->label('Status'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()->exports([
                    ExcelExport::make('table')
                        ->fromTable()
                        ->withColumns([
                            Column::make('id'),
                            Column::make('kode'),
                            Column::make('nama'),
                            Column::make('deskripsi'),
                            Column::make('sks'),
                            Column::make('kategori'),
                            Column::make('status'),
                            Column::make('created_at'),
                            Column::make('updated_at'),
                        ])
                        ->withFilename('mata-pelajaran-data-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX),
                ]),
            ])
            ->defaultSort('nama', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\JadwalRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMataPelajarans::route('/'),
            'create' => Pages\CreateMataPelajaran::route('/create'),
            'view' => Pages\ViewMataPelajaran::route('/{record}'),
            'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
        ];
    }
}
