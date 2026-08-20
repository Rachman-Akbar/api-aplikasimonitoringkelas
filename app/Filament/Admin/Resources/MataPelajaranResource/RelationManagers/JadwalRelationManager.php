<?php

namespace App\Filament\Admin\Resources\MataPelajaranResource\RelationManagers;

use App\Models\Jadwal;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JadwalRelationManager extends RelationManager
{
    protected static string $relationship = 'jadwals';

    protected static ?string $title = 'Jadwal';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kelas_id')
                    ->relationship('kelas', 'nama')
                    ->required(),
                Forms\Components\Select::make('guru_id')
                    ->relationship('guru', 'nama')
                    ->required(),
                Forms\Components\Select::make('hari')
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jam_ke')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('ruang')
                    ->maxLength(20),
                Forms\Components\Toggle::make('aktif')
                    ->required()
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('kelas.nama'),
                Tables\Columns\TextColumn::make('guru.nama'),
                Tables\Columns\TextColumn::make('hari')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'senin' => 'primary',
                        'selasa' => 'secondary',
                        'rabu' => 'warning',
                        'kamis' => 'info',
                        'jumat' => 'success',
                        'sabtu' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jam_ke'),
                Tables\Columns\TextColumn::make('ruang'),
                Tables\Columns\IconColumn::make('aktif')
                    ->boolean(),
            ])
            ->filters([
                // Filters removed to avoid intl extension issues
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Bulk actions removed to avoid intl extension issues
            ]);
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getOwnerRecordUrl();
    }
}