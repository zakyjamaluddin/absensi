<x-filament-widgets::widget>
    <!-- KONTENER GRADASI EMERALD LUXURY -->
    <div style="
        background: linear-gradient(135deg, #064e3b 0%, #0d9488 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.15), 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #047857;
    ">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.25rem;">

            <!-- SISI KIRI: Sapaan Hangat & Tanggal Emas Lembut -->
            <div style="flex: 1; min-width: 280px;">
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

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">📚</span>
                    <h1 style="font-size: 1.625rem; font-weight: 800; letter-spacing: -0.025em; margin: 0; color: #ffffff;">
                        {{ $greeting }}, {{ auth()->user()?->name ?? 'Pengguna' }}! 👋
                    </h1>
                </div>

                <p style="font-size: 0.9rem; margin: 0.45rem 0 0 0; color: #f0fdf4; line-height: 1.5;">
                    Hari ini adalah <strong style="color: #fcd34d; font-weight: 700;">{{ now()->translatedFormat('l, d F Y') }}</strong>.
                    Selamat datang di Dasbor Perpustakaan Digital Absaka. Pantau aktivitas kunjungan siswa, sirkulasi peminjaman, dan jatuh tempo pengembalian buku secara real-time.
                </p>
            </div>

            <!-- SISI KANAN: Kapsul Transparan Berdenyut (Glassmorphism) & Quick Shortcut -->
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
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
                            background-color: #10b981;
                        "></span>
                    </span>

                    <span style="font-size: 0.75rem; font-weight: 700; color: #ffffff; letter-spacing: 0.05em; text-transform: uppercase;">
                        Layanan Perpustakaan Aktif
                    </span>
                </div>

                <a href="{{ route('filament.admin.pages.scan-perpustakaan') }}" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 0.4rem;
                    background-color: #ffffff;
                    color: #064e3b;
                    font-size: 0.8rem;
                    font-weight: 700;
                    padding: 0.6rem 1rem;
                    border-radius: 9999px;
                    text-decoration: none;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    transition: transform 0.15s ease, background-color 0.15s ease;
                " onmouseover="this.style.backgroundColor='#ecfdf5'; this.style.transform='translateY(-1px)';" onmouseout="this.style.backgroundColor='#ffffff'; this.style.transform='translateY(0)';">
                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Buka Scanner</span>
                </a>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>
