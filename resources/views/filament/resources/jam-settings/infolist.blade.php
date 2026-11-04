@php
    $setting = \App\Models\JamSetting::first();
    $dayNames = [
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        'Sunday'    => 'Ahad / Minggu',
    ];
    $liburPekanan = $setting?->libur_pekanan ?? ['Friday'];
    if (is_string($liburPekanan)) {
        $liburPekanan = json_decode($liburPekanan, true) ?: [$liburPekanan];
    }
@endphp

<div class="jam-infolist-container">
    <style>
        .jam-infolist-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            width: 100%;
        }
        .jam-settings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            width: 100%;
        }
        @media (min-width: 768px) {
            .jam-settings-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1200px) {
            .jam-settings-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        .jam-kv-list {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-top: 0.25rem;
        }
        .jam-kv-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.625rem 0.85rem;
            border-radius: 0.625rem;
            background-color: rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(15, 23, 42, 0.06);
            transition: all 0.15s ease-in-out;
        }
        .dark .jam-kv-item {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }
        .jam-kv-item-highlight {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            border-radius: 0.625rem;
            background-color: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            transition: all 0.15s ease-in-out;
        }
        .dark .jam-kv-item-highlight {
            background-color: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.22);
        }
        .jam-label {
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
        }
        .dark .jam-label {
            color: #94a3b8;
        }
        .jam-time-text {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            font-size: 0.9375rem;
            letter-spacing: 0.025em;
        }
        .jam-note-box {
            font-size: 0.75rem;
            color: #64748b;
            line-height: 1.45;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px dashed rgba(15, 23, 42, 0.08);
        }
        .dark .jam-note-box {
            color: #94a3b8;
            border-top-color: rgba(255, 255, 255, 0.08);
        }
    </style>

    {{-- Grid 3 Kartu Jendela Presensi --}}
    <div class="jam-settings-grid">
        {{-- Kartu A: Jendela Absen Masuk --}}
        <x-filament::section
            heading="Jendela Absen Masuk"
            description="Presensi Datang / Pagi"
            icon="heroicon-o-arrow-left-on-rectangle"
        >
            <x-slot name="afterHeader">
                <x-filament::badge color="success">
                    Pagi
                </x-filament::badge>
            </x-slot>

            <div class="jam-kv-list">
                <div class="jam-kv-item">
                    <span class="jam-label">Mulai Boleh Tap:</span>
                    <span class="jam-time-text" style="color: #059669;">
                        {{ $setting?->mulai_masuk ? substr($setting->mulai_masuk, 0, 5) : '--:--' }} WIB
                    </span>
                </div>

                <div class="jam-kv-item-highlight">
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <x-filament::icon icon="heroicon-m-exclamation-triangle" style="width: 1rem; height: 1rem; color: #d97706;" />
                        <span style="font-size: 0.8125rem; font-weight: 700; color: #b45309;">Batas Terlambat:</span>
                    </div>
                    <span class="jam-time-text" style="color: #b45309;">
                        {{ $setting?->batas_terlambat ? substr($setting->batas_terlambat, 0, 5) : '--:--' }} WIB
                    </span>
                </div>

                <div class="jam-kv-item">
                    <span class="jam-label">Batas Akhir Masuk:</span>
                    <span class="jam-time-text" style="color: #0284c7;">
                        {{ $setting?->selesai_masuk ? substr($setting->selesai_masuk, 0, 5) : '--:--' }} WIB
                    </span>
                </div>
            </div>

            <div class="jam-note-box">
                💡 Siswa yang hadir <strong>&ge;15 menit sebelum batas terlambat</strong> otomatis mendapatkan apresiasi bonus <strong>+12 poin</strong> kedisiplinan.
            </div>
        </x-filament::section>

        {{-- Kartu B: Jendela Absen Pulang --}}
        <x-filament::section
            heading="Jendela Absen Pulang"
            description="Presensi Pulang / Siang & Sore"
            icon="heroicon-o-arrow-right-on-rectangle"
        >
            <x-slot name="afterHeader">
                <x-filament::badge color="info">
                    Siang / Sore
                </x-filament::badge>
            </x-slot>

            <div class="jam-kv-list">
                <div class="jam-kv-item">
                    <span class="jam-label">Mulai Boleh Pulang:</span>
                    <span class="jam-time-text" style="color: #0284c7;">
                        {{ $setting?->mulai_pulang ? substr($setting->mulai_pulang, 0, 5) : '--:--' }} WIB
                    </span>
                </div>

                <div class="jam-kv-item">
                    <span class="jam-label">Batas Akhir Pulang:</span>
                    <span class="jam-time-text" style="color: #6366f1;">
                        {{ $setting?->selesai_pulang ? substr($setting->selesai_pulang, 0, 5) : '--:--' }} WIB
                    </span>
                </div>
            </div>

            <div class="jam-note-box">
                🔒 Tap pulang di luar rentang waktu di atas akan ditolak scanner untuk mencegah presensi kepulangan sebelum waktunya.
            </div>
        </x-filament::section>

        {{-- Kartu C: Libur Mingguan & Mode Guru --}}
        <x-filament::section
            heading="Libur Pekanan & Guru"
            description="Hari Libur & Mode Guru"
            icon="heroicon-o-calendar-days"
        >
            <x-slot name="afterHeader">
                <x-filament::badge color="danger">
                    Kalender
                </x-filament::badge>
            </x-slot>

            <div class="jam-kv-list">
                <div style="margin-bottom: 0.35rem;">
                    <span class="jam-label" style="display: block; margin-bottom: 0.4rem; font-weight: 600;">Hari Libur Mingguan:</span>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                        @forelse($liburPekanan as $day)
                            <x-filament::badge color="danger">
                                {{ $dayNames[$day] ?? $day }}
                            </x-filament::badge>
                        @empty
                            <span style="font-size: 0.8125rem; color: #94a3b8;">Tidak ada hari libur</span>
                        @endforelse
                    </div>
                </div>

                <div class="jam-kv-item">
                    <span class="jam-label">Mode Guru:</span>
                    <x-filament::badge :color="($setting?->mode_absensi_guru ?? 'statis') === 'jadwal' ? 'info' : 'gray'">
                        {{ ($setting?->mode_absensi_guru ?? 'statis') === 'jadwal' ? 'Dinamis (Jadwal)' : 'Sama Setiap Hari' }}
                    </x-filament::badge>
                </div>

                @if(($setting?->mode_absensi_guru ?? 'statis') === 'statis' && $setting?->batas_terlambat_guru)
                    <div class="jam-kv-item">
                        <span class="jam-label">Batas Terlambat Guru:</span>
                        <span class="jam-time-text" style="color: #b45309;">
                            {{ substr($setting->batas_terlambat_guru, 0, 5) }} WIB
                        </span>
                    </div>
                @endif
            </div>

            <div class="jam-note-box">
                📅 Guru hanya wajib hadir pada hari jadwal mengajar jika mode Dinamis aktif.
            </div>
        </x-filament::section>
    </div>
</div>
