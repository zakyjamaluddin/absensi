<x-filament-widgets::widget>
    <x-filament::section>

        <!-- TATA LETAK FLEXBOX UTAMA (Inline CSS - 100% Aman & Responsif) -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; padding: 0.5rem 0;">

            <!-- SISI KIRI: Sapaan Hangat Dinamis -->
            <div style="flex: 1; min-width: 250px;">
                @php
                    $hour = now()->hour;
                    if ($hour >= 5 && $hour < 11) {
                        $greeting = 'Selamat pagi';
                    } elseif ($hour >= 11 && $hour < 15) {
                        $greeting = 'Selamat siang';
                    } elseif ($hour >= 15 && $hour < 18) {
                        $greeting = 'Selamat sore';
                    } else {
                        $greeting = 'Selamat malam';
                    }
                @endphp

                <h1 class="custom-title" style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.025em; margin: 0; color: #111827;">
                    {{ $greeting }}, {{ auth()->user()->name }}! 👋
                </h1>

                <p class="custom-subtitle" style="font-size: 0.875rem; margin: 0.35rem 0 0 0; color: #6b7280; line-height: 1.4;">
                    Hari ini adalah <strong style="color: #3b82f6;">{{ now()->translatedFormat('l, d F Y') }}</strong>.
                    Semoga aktivitas belajar mengajar hari ini diberkahi Allah SWT.
                </p>
            </div>

            <!-- SISI KANAN: Denyut Lampu Hijau Server Aktif -->
            <div class="custom-badge" style="
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background-color: #ecfdf5;
                border: 1px solid #a7f3d0;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                transition: all 0.2s;
            ">
                <!-- Lampu Denyut Hijau (Animasi CSS bawaan) -->
                <span style="display: inline-block; position: relative; width: 0.75rem; height: 0.75rem;">
                    <span class="animate-ping" style="
                        position: absolute;
                        display: inline-flex;
                        width: 100%;
                        height: 100%;
                        border-radius: 9999px;
                        background-color: #34d399;
                        opacity: 0.75;
                    "></span>
                    <span style="
                        position: relative;
                        display: inline-flex;
                        border-radius: 9999px;
                        width: 0.75rem;
                        height: 0.75rem;
                        background-color: #10b981;
                    "></span>
                </span>

                <span class="custom-badge-text" style="font-size: 0.75rem; font-weight: 700; color: #047857;">
                    Sistem Absensi Online Aktif
                </span>
            </div>

        </div>

        <!-- TRIK AGAR OTOMATIS COMPATIBLE DENGAN DARK MODE (Bebas Kompilasi) -->
        <style>
            /* Ketika Admin Panel dalam keadaan Dark Mode, timpa warnanya di sini */
            .dark .custom-title {
                color: #ffffff !important;
            }
            .dark .custom-subtitle {
                color: #9ca3af !important;
            }
            .dark .custom-badge {
                background-color: #064e3b !important; /* Hijau gelap */
                border-color: #047857 !important;
            }
            .dark .custom-badge-text {
                color: #a7f3d0 !important; /* Teks hijau terang */
            }
        </style>

    </x-filament::section>
</x-filament-widgets::widget>
