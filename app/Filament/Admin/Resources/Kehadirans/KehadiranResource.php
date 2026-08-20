<?php

namespace App\Filament\Admin\Resources\Kehadirans;

use App\Filament\Admin\Resources\Kehadirans\Pages\CreateKehadiran;
use App\Filament\Admin\Resources\Kehadirans\Pages\EditKehadiran;
use App\Filament\Admin\Resources\Kehadirans\Pages\ListKehadirans;
use App\Filament\Admin\Resources\Kehadirans\Pages\ViewKehadiran;
use App\Filament\Admin\Resources\Kehadirans\Schemas\KehadiranForm;
use App\Filament\Admin\Resources\Kehadirans\Schemas\KehadiranInfolist;
use App\Filament\Admin\Resources\Kehadirans\Tables\KehadiransTable;
use App\Models\Kehadiran;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KehadiranResource extends Resource
{
    protected static ?string $model = Kehadiran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kehadiran Siswa';
    protected static ?string $modelLabel = 'Kehadiran Siswa';
    protected static ?string $pluralModelLabel = 'Kehadiran Siswa';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Kehadiran';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return KehadiranForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KehadiranInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KehadiransTable::configure($table);
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
            'index' => ListKehadirans::route('/'),
            'create' => CreateKehadiran::route('/create'),
            'view' => ViewKehadiran::route('/{record}'),
            'edit' => EditKehadiran::route('/{record}/edit'),
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
