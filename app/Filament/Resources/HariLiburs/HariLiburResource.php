<?php

namespace App\Filament\Resources\HariLiburs;

use App\Filament\Resources\HariLiburs\Pages\ManageHariLiburs;
use App\Models\HariLibur;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class HariLiburResource extends Resource
{
    protected static ?string $model = HariLibur::class;
    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Kalender Libur';
    protected static ?string $pluralModelLabel = 'Kalender Libur';
    protected static ?string $modelLabel = 'Hari Libur';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Libur')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('keterangan')
                    ->label('Keterangan Libur')
                    ->required()
                    ->placeholder('Contoh: Libur Awal Ramadhan')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->searchable(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHariLiburs::route('/'),
        ];
    }
}
