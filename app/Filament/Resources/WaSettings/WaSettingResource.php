<?php

namespace App\Filament\Resources\WaSettings;

use App\Filament\Resources\WaSettings\Pages\ManageWaSettings;

use App\Models\WaSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class WaSettingResource extends Resource
{
    protected static ?string $model = WaSetting::class;


    protected static ?string $navigationLabel = 'Integrasi WA';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string | UnitEnum | null $navigationGroup = 'Pengaturan';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Koneksi WhatsApp Gateway')
                    ->description('Masukkan Token API Anda yang didapatkan dari dashboard Sidobe.com.')
                    ->schema([
                        TextInput::make('token')
                            ->label('API Token Sidobe')
                            ->password() // Sembunyikan token demi keamanan layar
                            ->revealable() // Admin bisa klik ikon mata untuk melihat token
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('token')
                    ->label('API Token Sidobe')
                    ->formatStateUsing(fn ($state) => '••••••••••••' . substr($state, -5)) // Sembunyikan sebagian token di tabel
                    ->placeholder('Belum dikonfigurasi'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWaSettings::route('/'),
        ];
    }
}
