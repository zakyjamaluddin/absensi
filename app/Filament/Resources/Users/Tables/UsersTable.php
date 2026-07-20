<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) => $record->email)
                    ->label('Nama'),
                // Menampilkan Tipe Profil (Guru / Siswa)
                TextColumn::make('userable_type')
                    ->label('Tipe Profil')
                    ->formatStateUsing(fn ($state) => $state ? (str_contains($state, 'Siswa') ? 'Siswa' : 'Guru') : '-')
                    ->badge()
                    ->color(fn ($state) => str_contains($state, 'Siswa') ? 'info' : 'success')
                    ->placeholder('-'),

                TextColumn::make('roles.name')
                    ->label('Hak Akses')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
