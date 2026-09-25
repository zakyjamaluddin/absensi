<x-filament-widgets::widget>
    <!-- KONTENER GRADASI EMERALD LUXURY -->
    <div style="
        background: linear-gradient(135deg, #064e3b 0%, #0d9488 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #047857;
    ">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">

            <!-- SISI KIRI: Sapaan Hangat & Tanggal Emas Lembut -->
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

                <h1 style="font-size: 1.625rem; font-weight: 800; letter-spacing: -0.025em; margin: 0; color: #ffffff;">
                    {{ $greeting }}, {{ auth()->user()->name }}! 👋
                </h1>

                <p style="font-size: 0.9rem; margin: 0.45rem 0 0 0; color: #f0fdf4; line-height: 1.5;">
                    Hari ini adalah <strong style="color: #fcd34d; font-weight: 700;">{{ now()->translatedFormat('l, d F Y') }}</strong>.
                    Semoga aktivitas belajar mengajar hari ini diberkahi Allah SWT.
                </p>
            </div>

            <!-- SISI KANAN: Kapsul Transparan Berdenyut (Glassmorphism) -->
            <div style="
                display: flex;
                align-items: center;
                gap: 0.5rem;
                background-color: rgba(255, 255, 255, 0.12);
                border: 1px solid rgba(255, 255, 255, 0.2);
                padding: 0.6rem 1.15rem;
                border-radius: 9999px;
                backdrop-filter: blur(4px);
            ">
                <!-- Lampu Denyut Putih-Hijau di dalam Kapsul Transparan -->
                <span style="display: inline-block; position: relative; width: 0.65rem; height: 0.65rem;">
                    <!-- MENGGUNAKAN ANIMASI KUSTOM: custom-ping-animation -->
                    <span class="custom-ping-animation" style="
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
                        width: 0.65rem;
                        height: 0.65rem;
                    "></span>
                </span>

                <span style="font-size: 0.75rem; font-weight: 700; color: #ffffff; letter-spacing: 0.05em; text-transform: uppercase;">
                    Sistem Absensi Online Aktif
                </span>
            </div>

        </div>
    </div>

    <!-- BLOK CSS ANIMASI DENYUT MURNI & DARK MODE -->
    <style>
        /* ANIMASI DENYUT CSS MURNI (100% Lancar di Semua Browser & Bebas Gagal Kompilasi) */
        @keyframes custom-ping-keyframes {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }
            70% {
                transform: scale(2.2);
                opacity: 0;
            }
            100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        .custom-ping-animation {
            animation: custom-ping-keyframes 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        /* Skenario Penyesuaian Dark Mode */
        .dark .custom-title {
            color: #ffffff !important;
        }
        .dark .custom-subtitle {
            color: #9ca3af !important;
        }
        .dark .custom-badge {
            background-color: #064e3b !important;
            border-color: #047857 !important;
        }
        .dark .custom-badge-text {
            color: #a7f3d0 !important;
        }
    </style>
</x-filament-widgets::widget>
