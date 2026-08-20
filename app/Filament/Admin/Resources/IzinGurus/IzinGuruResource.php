<?php

namespace App\Filament\Admin\Resources\IzinGurus;

use App\Filament\Admin\Resources\IzinGurus\Pages\CreateIzinGuru;
use App\Filament\Admin\Resources\IzinGurus\Pages\EditIzinGuru;
use App\Filament\Admin\Resources\IzinGurus\Pages\ListIzinGurus;
use App\Filament\Admin\Resources\IzinGurus\Pages\ViewIzinGuru;
use App\Filament\Admin\Resources\IzinGurus\Schemas\IzinGuruForm;
use App\Filament\Admin\Resources\IzinGurus\Schemas\IzinGuruInfolist;
use App\Filament\Admin\Resources\IzinGurus\Tables\IzinGurusTable;
use App\Models\IzinGuru;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class IzinGuruResource extends Resource
{
    protected static ?string $model = IzinGuru::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Izin Guru';
    protected static ?string $modelLabel = 'Izin Guru';
    protected static ?string $pluralModelLabel = 'Izin Guru';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return IzinGuruForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IzinGuruInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IzinGurusTable::configure($table);
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
            'index' => ListIzinGurus::route('/'),
            'view' => ViewIzinGuru::route('/{record}'),
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
