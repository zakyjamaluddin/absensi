<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PengajuanIzin extends Model
{
    protected $fillable = [
        'izinable_type',
        'izinable_id',
        'tipe_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti_foto',
        'status',
        'disetujui_oleh_id',
        'catatan_petugas',
        'tanggal_approval',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'tanggal_approval' => 'datetime',
        ];
    }

    public function izinable(): MorphTo
    {
        return $this->morphTo();
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh_id');
    }

    public function getBuktiFotoUrlAttribute(): ?string
    {
        if (!empty($this->bukti_foto)) {
            return asset('storage/' . $this->bukti_foto);
        }
        return null;
    }

    protected static function booted(): void
    {
        static::created(function (PengajuanIzin $pengajuan) {
            try {
                // Notifikasi instan ke seluruh Guru Piket hari ini + Super Admin, Admin Absensi, & Staff Absensi
                $guruPiketUsers = JadwalGuruPiket::getUserPiketHariIni();
                $adminUsers = User::role(['super_admin', 'admin_absensi', 'staff_absensi'])->get();
                $targetUsers = $guruPiketUsers->merge($adminUsers)->unique('id');

                $pemohonNama = $pengajuan->izinable?->nama ?? 'Siswa/Guru';

                foreach ($targetUsers as $targetUser) {
                    \Filament\Notifications\Notification::make()
                        ->title('📩 Pengajuan Izin Baru Masuk')
                        ->body("{$pemohonNama} mengajukan {$pengajuan->tipe_izin}: {$pengajuan->alasan}")
                        ->warning()
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('tinjau')
                                ->label('Tinjau')
                                ->url(url('/admin/pengajuan-izins')),
                        ])
                        ->sendToDatabase($targetUser);
                }
            } catch (\Throwable $e) {
                // Ignore silent fail
            }
        });
    }

    /**
     * Setujui izin dan sinkronisasikan ke tabel absensi
     */
    public function approve(int $userId, ?string $notes = null): bool
    {
        $this->update([
            'status' => 'Disetujui',
            'disetujui_oleh_id' => $userId,
            'catatan_petugas' => $notes,
            'tanggal_approval' => now(),
        ]);

        $this->syncToAbsensi();

        // Notifikasi ke akun pemohon (Siswa atau Guru)
        $userPemohon = $this->izinable?->user;
        if ($userPemohon) {
            Notification::make()
                ->title("Pengajuan Izin {$this->tipe_izin} Disetujui")
                ->body($notes ? "Catatan petugas: {$notes}" : "Permohonan izin Anda telah disetujui dan dicatat pada presensi.")
                ->icon('heroicon-o-check-circle')
                ->success()
                ->sendToDatabase($userPemohon);
        }

        return true;
    }

    /**
     * Tolak permohonan izin
     */
    public function reject(int $userId, ?string $notes = null): bool
    {
        $updated = $this->update([
            'status' => 'Ditolak',
            'disetujui_oleh_id' => $userId,
            'catatan_petugas' => $notes,
            'tanggal_approval' => now(),
        ]);

        // Notifikasi ke akun pemohon (Siswa atau Guru)
        $userPemohon = $this->izinable?->user;
        if ($userPemohon) {
            Notification::make()
                ->title("Pengajuan Izin {$this->tipe_izin} Ditolak")
                ->body($notes ? "Alasan penolakan: {$notes}" : "Permohonan izin Anda belum dapat disetujui.")
                ->icon('heroicon-o-x-circle')
                ->danger()
                ->sendToDatabase($userPemohon);
        }

        return $updated;
    }

    /**
     * Sinkronisasikan kehadiran ke tabel Absensi untuk setiap hari dalam rentang izin
     */
    public function syncToAbsensi(): void
    {
        if ($this->status !== 'Disetujui') {
            return;
        }

        $period = CarbonPeriod::create($this->tanggal_mulai, $this->tanggal_selesai);

        foreach ($period as $date) {
            $dateString = $date->toDateString();

            Absensi::updateOrCreate(
                [
                    'absensable_type' => $this->izinable_type,
                    'absensable_id'   => $this->izinable_id,
                    'tanggal'         => $dateString,
                ],
                [
                    'status_masuk'  => $this->tipe_izin, // 'Izin' atau 'Sakit'
                    'status_pulang' => $this->tipe_izin,
                    'jam_masuk'     => null,
                    'jam_pulang'    => null,
                ]
            );
        }
    }
}
