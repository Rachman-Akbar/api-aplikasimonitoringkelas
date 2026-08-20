<?php

namespace App\Filament\Admin\Resources\GuruPenggantis;

use App\Filament\Admin\Resources\GuruPenggantis\Pages\CreateGuruPengganti;
use App\Filament\Admin\Resources\GuruPenggantis\Pages\EditGuruPengganti;
use App\Filament\Admin\Resources\GuruPenggantis\Pages\ListGuruPenggantis;
use App\Filament\Admin\Resources\GuruPenggantis\Pages\ViewGuruPengganti;
use App\Filament\Admin\Resources\GuruPenggantis\Schemas\GuruPenggantiForm;
use App\Filament\Admin\Resources\GuruPenggantis\Schemas\GuruPenggantiInfolist;
use App\Filament\Admin\Resources\GuruPenggantis\Tables\GuruPenggantisTable;
use App\Models\GuruPengganti;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class GuruPenggantiResource extends Resource
{
    protected static ?string $model = GuruPengganti::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Guru Pengganti';
    protected static ?string $modelLabel = 'Guru Pengganti';
    protected static ?string $pluralModelLabel = 'Guru Pengganti';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return GuruPenggantiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GuruPenggantiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuruPenggantisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuruPenggantis::route('/'),
            'view' => ViewGuruPengganti::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
