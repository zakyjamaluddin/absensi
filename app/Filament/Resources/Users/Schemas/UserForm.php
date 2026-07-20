<?php
namespace App\Filament\Resources\Users\Schemas;

use App\Models\Guru;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function get(): array
    {
        return [
            // 1. DATA UTAMA USER
            Section::make('Informasi Akun')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Akun')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email Login')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state)) // Hanya update password jika diisi
                        ->required(fn (string $context): bool => $context === 'create') // Wajib diisi saat buat baru
                        ->maxLength(255),
                ])->columns(1),

            // 2. TAUTAN PROFIL POLIMORFIS
            Section::make('Tautan Profil Akun')
                ->description('Tautkan akun login ini dengan profil akademis (kosongkan jika Admin Umum/Staf IT).')
                ->schema([
                    Select::make('userable_type')
                        ->label('Tipe Profil')
                        ->options([
                            Guru::class => 'Guru / Ustadz',
                            Siswa::class => 'Siswa / Santri',
                        ])
                        ->nullable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('userable_id', null)), // Mengosongkan nama jika tipe diubah

                    Select::make('userable_id')
                        ->label('Pilih Nama')
                        ->options(function (callable $get) {
                            $type = $get('userable_type');
                            if ($type === Guru::class) {
                                return Guru::pluck('nama', 'id');
                            }
                            if ($type === Siswa::class) {
                                return Siswa::pluck('nama', 'id');
                            }
                            return [];
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])->columns(1),

            // 3. PEMBERIAN ROLE DARI SHIELD (Ditampilkan sebagai Checkbox List yang indah)
            Section::make('Hak Akses (Role)')
                ->description('Pilih satu atau beberapa peran akses untuk akun ini.')
                ->schema([
                    CheckboxList::make('roles')
                        ->label('Pilihan Peran')
                        ->relationship('roles', 'name') // Menghubungkan relasi spatie ke database
                        ->searchable()
                        ->columns(3) // Susun ke kanan sebanyak 3 kolom agar rapi
                        ->required(),
                ]),
        ];
    }
}
