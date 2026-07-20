<?php

namespace App\Filament\Resources\Gurus;

use App\Filament\Resources\Gurus\Pages\ManageGurus;
use App\Models\Guru;
use App\Models\Siswa;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; // <--- Menggunakan Schema baru Filament v5
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use UnitEnum;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;
    protected static string | UnitEnum | null $navigationGroup = 'Data';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Data Guru';
    protected static ?string $pluralModelLabel = 'Data Guru';
    protected static ?string $modelLabel = 'Guru';

    // Type-hint diubah ke Schema sesuai dokumentasi terbaru
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([ // <--- Menggunakan components() untuk mendefinisikan field
                Forms\Components\TextInput::make('nip')
                    ->label('NIP / ID Card')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: 19810101')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap')
                    ->required()
                    ->placeholder('Nama Lengkap beserta Gelar')
                    ->maxLength(255),

                Forms\Components\TextInput::make('no_hp')
                    ->label('No. HP / WhatsApp')
                    ->tel()
                    ->placeholder('Contoh: 081234567890')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(function (Guru $record) {
                        return $record->nip ? "NIP: {$record->nip}" : "NIP Belum diisi";
                    }),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('unduh_qr')
                        ->label('Unduh QR')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->action(function (Guru $record) {
                            $namaClean = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ' '], '_', $record->nama);

                            return response()->streamDownload(function () use ($record) {
                                echo QrCode::size(300)
                                    ->margin(1)
                                    ->generate($record->nip);
                            }, "Guru_{$namaClean}.svg", [
                                'Content-Type' => 'image/svg+xml',
                            ]);
                        }),
                ]),
                // EditAction::make(),
                // DeleteAction::make(),
                // Action::make('unduh_qr')
                //     ->label('Unduh QR')
                //     ->icon('heroicon-o-arrow-down-tray')
                //     ->color('success')
                //     ->action(function (Guru $record) {
                //         $namaClean = str_replace(['/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ' '], '_', $record->nama);

                //         return response()->streamDownload(function () use ($record) {
                //             echo QrCode::size(300)
                //                 ->margin(1)
                //                 ->generate($record->nip);
                //         }, "Guru_{$namaClean}.svg", [
                //             'Content-Type' => 'image/svg+xml',
                //         ]);
                //     }),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGurus::route('/'),
        ];
    }
}
