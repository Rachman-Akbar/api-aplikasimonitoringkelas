<?php

namespace App\Filament\Admin\Resources\KehadiranGurus;

use App\Filament\Admin\Resources\KehadiranGurus\Pages\CreateKehadiranGuru;
use App\Filament\Admin\Resources\KehadiranGurus\Pages\EditKehadiranGuru;
use App\Filament\Admin\Resources\KehadiranGurus\Pages\ListKehadiranGurus;
use App\Filament\Admin\Resources\KehadiranGurus\Pages\ViewKehadiranGuru;
use App\Filament\Admin\Resources\KehadiranGurus\Schemas\KehadiranGuruForm;
use App\Filament\Admin\Resources\KehadiranGurus\Schemas\KehadiranGuruInfolist;
use App\Filament\Admin\Resources\KehadiranGurus\Tables\KehadiranGurusTable;
use App\Models\KehadiranGuru;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KehadiranGuruResource extends Resource
{
    protected static ?string $model = KehadiranGuru::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kehadiran Guru';
    protected static ?string $modelLabel = 'Kehadiran Guru';
    protected static ?string $pluralModelLabel = 'Kehadiran Guru';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return KehadiranGuruForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KehadiranGuruInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KehadiranGurusTable::configure($table);
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
            'index' => ListKehadiranGurus::route('/'),
            'view' => ViewKehadiranGuru::route('/{record}'),
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
