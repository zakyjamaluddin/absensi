<x-filament-panels::page>
    <style>
        @keyframes scanCardFadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* 2-Column Responsive Scanner Layout */
        .scanner-grid-container {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            width: 100%;
        }

        .scanner-left-panel {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .scanner-mobile-alert {
            display: block;
        }

        .scanner-right-panel {
            display: none;
            width: 100%;
        }

        /* Desktop & Tablet View (Layar >= 768px) */
        @media (min-width: 768px) {
            .scanner-grid-container {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 1.75rem !important;
                align-items: start !important;
            }

            .scanner-left-panel {
                width: 100% !important;
            }

            .scanner-mobile-alert {
                display: none !important;
            }

            .scanner-right-panel {
                display: block !important;
                position: sticky !important;
                top: 1.5rem !important;
                width: 100% !important;
            }
        }
    </style>

    <div style="width: 100%; max-width: 1150px; margin: 0 auto;">
        <!-- GRID UTAMA: Mobile 1 Kolom, Desktop 2 Kolom (Kiri: Scanner, Kanan: Alert) -->
        <div class="scanner-grid-container">

            <!-- ==========================================
                 SISI KIRI: SCANNER PERPUSTAKAAN
                 ========================================== -->
            <div class="scanner-left-panel">

                <!-- TAB NAVIGATION (KUNJUNGAN / PINJAM / KEMBALI) -->
                <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    <!-- Tab 1: Kunjungan -->
                    <button
                        type="button"
                        wire:click="setTab('kunjungan')"
                        style="
                            flex: 1;
                            min-width: 130px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.35rem;
                            padding: 0.65rem 0.85rem;
                            border-radius: 0.5rem;
                            font-weight: 700;
                            font-size: 0.8rem;
                            cursor: pointer;
                            transition: all 0.2s;
                            border: 1px solid {{ $activeTab === 'kunjungan' ? '#10b981' : '#d1d5db' }};
                            background-color: {{ $activeTab === 'kunjungan' ? '#10b981' : '#ffffff' }};
                            color: {{ $activeTab === 'kunjungan' ? '#ffffff' : '#374151' }};
                        "
                    >
                        <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        <span>Kunjungan</span>
                    </button>

                    <!-- Tab 2: Peminjaman -->
                    <button
                        type="button"
                        wire:click="setTab('pinjam')"
                        style="
                            flex: 1;
                            min-width: 130px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.35rem;
                            padding: 0.65rem 0.85rem;
                            border-radius: 0.5rem;
                            font-weight: 700;
                            font-size: 0.8rem;
                            cursor: pointer;
                            transition: all 0.2s;
                            border: 1px solid {{ $activeTab === 'pinjam' ? '#3b82f6' : '#d1d5db' }};
                            background-color: {{ $activeTab === 'pinjam' ? '#3b82f6' : '#ffffff' }};
                            color: {{ $activeTab === 'pinjam' ? '#ffffff' : '#374151' }};
                        "
                    >
                        <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Pinjam Buku</span>
                    </button>

                    <!-- Tab 3: Pengembalian -->
                    <button
                        type="button"
                        wire:click="setTab('kembali')"
                        style="
                            flex: 1;
                            min-width: 130px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            gap: 0.35rem;
                            padding: 0.65rem 0.85rem;
                            border-radius: 0.5rem;
                            font-weight: 700;
                            font-size: 0.8rem;
                            cursor: pointer;
                            transition: all 0.2s;
                            border: 1px solid {{ $activeTab === 'kembali' ? '#8b5cf6' : '#d1d5db' }};
                            background-color: {{ $activeTab === 'kembali' ? '#8b5cf6' : '#ffffff' }};
                            color: {{ $activeTab === 'kembali' ? '#ffffff' : '#374151' }};
                        "
                    >
                        <svg style="width: 1.1rem; height: 1.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Pengembalian</span>
                    </button>
                </div>

                <!-- (KHUSUS MOBILE) ALERT CARD: Hanya muncul ketika mode mobile DAN ada data scan / pesan -->
                <div class="scanner-mobile-alert">
                    @if($lastScanned)
                        @php
                            $statusBadge = $lastScanned['status_badge'] ?? 'info';
                            $isSuccess = ($statusBadge === 'success');
                            $isDanger = ($statusBadge === 'danger');
                            $isInfo = ($statusBadge === 'info');

                            $badgeBg = $isSuccess
                                ? 'background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;'
                                : ($isDanger
                                    ? 'background-color: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3;'
                                    : 'background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd;');

                            $cardBorder = $isSuccess
                                ? 'border-color: rgba(16, 185, 129, 0.4); box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.15);'
                                : ($isDanger
                                    ? 'border-color: rgba(239, 68, 68, 0.4); box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.15);'
                                    : 'border-color: rgba(59, 130, 246, 0.4); box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.15);');

                            $accentGradient = $isSuccess
                                ? 'background: linear-gradient(90deg, #10b981 0%, #059669 100%);'
                                : ($isDanger
                                    ? 'background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);'
                                    : 'background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);');

                            $ringColor = $isSuccess ? '#10b981' : ($isDanger ? '#ef4444' : '#3b82f6');
                        @endphp

                        <div style="
                            margin-bottom: 1rem;
                            border-radius: 1rem;
                            border-width: 1.5px;
                            border-style: solid;
                            overflow: hidden;
                            background-color: #ffffff;
                            animation: scanCardFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                            {{ $cardBorder }}
                        " class="dark:!bg-[#0f172a] dark:!border-slate-700/80">
                            <div style="height: 4px; width: 100%; {{ $accentGradient }}"></div>
                            <div style="padding: 1rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.75rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; {{ $badgeBg }}">
                                        {{ $lastScanned['status_kehadiran'] }}
                                    </span>
                                    <span style="font-size: 0.75rem; font-family: monospace; color: #64748b;" class="dark:!text-slate-400">
                                        {{ $lastScanned['jam'] }} WIB
                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 4.5rem; height: 4.5rem; border-radius: 9999px; overflow: hidden; border: 3px solid {{ $ringColor }}; flex-shrink: 0; background: #f1f5f9;">
                                        @if(!empty($lastScanned['foto']))
                                            <img src="{{ $lastScanned['foto'] }}" alt="{{ $lastScanned['nama'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.svg') }}';" />
                                        @else
                                            <img src="{{ asset('images/default-avatar.svg') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;" />
                                        @endif
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.7rem; font-weight: 600; color: #10b981; margin-bottom: 0.15rem;">
                                            {{ $lastScanned['kelas'] }} • {{ $lastScanned['nomor_induk_label'] }}: {{ $lastScanned['nomor_induk'] }}
                                        </div>
                                        <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 0.25rem 0;" class="dark:!text-white truncate">
                                            {{ $lastScanned['nama'] }}
                                        </h3>
                                        @if(isset($lastScanned['skor']))
                                            <div style="font-size: 0.75rem; font-weight: 700; color: #059669; margin-bottom: 0.25rem;">
                                                ★ {{ $lastScanned['skor'] }} Poin Literasi
                                            </div>
                                        @endif
                                        <div style="font-size: 0.75rem; font-weight: 600; color: {{ $isSuccess ? '#047857' : ($isDanger ? '#b91c1c' : '#0369a1') }};" class="dark:!text-slate-300">
                                            {{ $message }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($message)
                        <div style="
                            margin-bottom: 1rem;
                            border-radius: 0.75rem;
                            border: 1.5px solid rgba(239, 68, 68, 0.4);
                            padding: 0.875rem 1rem;
                            background-color: #fee2e2;
                            color: #991b1b;
                            font-size: 0.85rem;
                            font-weight: 700;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                        " class="dark:!bg-rose-950/60 dark:!text-rose-300 dark:!border-rose-900/60">
                            <svg style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @endif
                </div>

                <!-- MAIN SCANNER SECTION -->
                <x-filament::section>
                    <x-slot name="heading">
                        @if($activeTab === 'kunjungan')
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.25rem;">🎟️</span>
                                <span>Scanner Kunjungan Perpustakaan</span>
                            </div>
                        @elseif($activeTab === 'pinjam')
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.25rem;">📚</span>
                                <span>Scanner Peminjaman Buku</span>
                            </div>
                        @else
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 1.25rem;">🔄</span>
                                <span>Scanner Pengembalian Buku</span>
                            </div>
                        @endif
                    </x-slot>

                    <p style="font-size: 0.85rem; color: #6b7280; margin-bottom: 1.25rem; text-align: center;">
                        @if($activeTab === 'kunjungan')
                            Dekatkan Kartu RFID atau Barcode Kartu Siswa untuk mencatat kehadiran.
                        @elseif($activeTab === 'pinjam')
                            @if(!$pinjamSiswa)
                                <span style="color: #2563eb; font-weight: 700;">[Langkah 1]</span> Tempelkan Kartu RFID Siswa peminjam.
                            @else
                                <span style="color: #059669; font-weight: 700;">[Langkah 2]</span> Arahkan scanner ke Kode/Barcode Buku.
                            @endif
                        @else
                            Arahkan Scanner ke Kode/Barcode Buku yang dikembalikan.
                        @endif
                    </p>

                    <!-- SCAN INPUT FIELD -->
                    <form wire:submit.prevent="processScan" style="margin-bottom: 1rem;">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="text"
                                id="perpustakaanScannerInput"
                                wire:model="scannedCode"
                                placeholder="{{ $activeTab === 'kunjungan' ? 'Tempel kartu RFID siswa...' : ($activeTab === 'pinjam' ? (!$pinjamSiswa ? 'Langkah 1: Tempel kartu RFID siswa...' : 'Langkah 2: Scan kode buku...') : 'Scan kode buku yang dikembalikan...') }}"
                                style="text-align: center; font-size: 1.125rem; padding: 0.75rem; font-weight: 600;"
                                autofocus
                                autocomplete="off"
                            />
                        </x-filament::input.wrapper>
                    </form>

                    <!-- AREA KONFIRMASI PEMINJAMAN (HANYA TAB PINJAM) -->
                    @if($activeTab === 'pinjam' && ($pinjamSiswa || $pinjamBuku))
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; margin-top: 1rem;" class="dark:!bg-slate-900/60 dark:!border-slate-800">
                            <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                                <!-- Box Siswa -->
                                <div style="background: #ffffff; padding: 0.85rem; border-radius: 0.5rem; border: 1px solid #d1d5db;" class="dark:!bg-slate-800 dark:!border-slate-700">
                                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;">
                                        Data Siswa Peminjam
                                    </div>
                                    @if($pinjamSiswa)
                                        <div style="font-size: 1.05rem; font-weight: 800; color: #111827;" class="dark:!text-white">{{ $pinjamSiswa['nama'] }}</div>
                                        <div style="font-size: 0.8rem; color: #4b5563;" class="dark:!text-slate-400">Kelas: <span style="font-weight: 600;">{{ $pinjamSiswa['kelas'] }}</span> • NIS: {{ $pinjamSiswa['nis'] }}</div>
                                    @else
                                        <div style="font-size: 0.8rem; color: #9ca3af; font-style: italic;">Menunggu scan kartu siswa...</div>
                                    @endif
                                </div>

                                <!-- Box Buku -->
                                <div style="background: #ffffff; padding: 0.85rem; border-radius: 0.5rem; border: 1px solid #d1d5db;" class="dark:!bg-slate-800 dark:!border-slate-700">
                                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;">
                                        Koleksi Buku Terdeteksi
                                    </div>
                                    @if($pinjamBuku)
                                        <div style="font-size: 1.05rem; font-weight: 800; color: #111827;" class="dark:!text-white">{{ $pinjamBuku['judul'] }}</div>
                                        <div style="font-size: 0.8rem; color: #4b5563;" class="dark:!text-slate-400">Kode: <span style="font-family: monospace; font-weight: 700;">{{ $pinjamBuku['kode_buku'] }}</span> • Rak: {{ $pinjamBuku['rak'] }}</div>
                                    @else
                                        <div style="font-size: 0.8rem; color: #9ca3af; font-style: italic;">Menunggu scan kode buku...</div>
                                    @endif
                                </div>
                            </div>

                            <!-- PENGATURAN DURASI PINJAM -->
                            @if($pinjamSiswa && $pinjamBuku)
                                <div style="background: #ffffff; padding: 0.85rem; border-radius: 0.5rem; border: 1px solid #d1d5db; margin-bottom: 1rem;" class="dark:!bg-slate-800 dark:!border-slate-700">
                                    <div style="font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 0.35rem;" class="dark:!text-slate-300">
                                        ⏱️ Batas Waktu Pengembalian:
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <input
                                                type="number"
                                                min="1"
                                                max="60"
                                                wire:model.live="lamaPinjamHari"
                                                style="width: 70px; padding: 0.3rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-weight: 700; text-align: center;"
                                            />
                                            <span style="font-size: 0.8rem; color: #4b5563;">Hari</span>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #059669; font-weight: 700;">
                                            📅 {{ \Carbon\Carbon::parse($tanggalJatuhTempo)->translatedFormat('l, d F Y') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- TOMBOL KONFIRMASI -->
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <button
                                        type="button"
                                        wire:click="resetScannerState"
                                        style="
                                            padding: 0.5rem 1rem;
                                            border-radius: 0.375rem;
                                            font-weight: 600;
                                            font-size: 0.8rem;
                                            background: #f3f4f6;
                                            color: #4b5563;
                                            border: 1px solid #d1d5db;
                                            cursor: pointer;
                                        "
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="konfirmasiPinjam"
                                        style="
                                            padding: 0.5rem 1.25rem;
                                            border-radius: 0.375rem;
                                            font-weight: 700;
                                            font-size: 0.8rem;
                                            background: #10b981;
                                            color: #ffffff;
                                            border: none;
                                            cursor: pointer;
                                            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
                                        "
                                    >
                                        ✓ Konfirmasi Pinjam
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </x-filament::section>

            </div>

            <!-- ==========================================
                 SISI KANAN: ALERT CARD DESKTOP
                 (Tersembunyi di mobile, selalu tampil di desktop / sticky)
                 ========================================== -->
            <div class="scanner-right-panel">
                @if($lastScanned)
                    @php
                        $statusBadge = $lastScanned['status_badge'] ?? 'info';
                        $isSuccess = ($statusBadge === 'success');
                        $isDanger = ($statusBadge === 'danger');
                        $isInfo = ($statusBadge === 'info');

                        $badgeBg = $isSuccess
                            ? 'background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;'
                            : ($isDanger
                                ? 'background-color: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3;'
                                : 'background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd;');

                        $cardBorder = $isSuccess
                            ? 'border-color: rgba(16, 185, 129, 0.4); box-shadow: 0 10px 30px -5px rgba(16, 185, 129, 0.18), 0 4px 6px -2px rgba(0, 0, 0, 0.05);'
                            : ($isDanger
                                ? 'border-color: rgba(239, 68, 68, 0.4); box-shadow: 0 10px 30px -5px rgba(239, 68, 68, 0.18), 0 4px 6px -2px rgba(0, 0, 0, 0.05);'
                                : 'border-color: rgba(59, 130, 246, 0.4); box-shadow: 0 10px 30px -5px rgba(59, 130, 246, 0.18), 0 4px 6px -2px rgba(0, 0, 0, 0.05);');

                        $accentGradient = $isSuccess
                            ? 'background: linear-gradient(90deg, #10b981 0%, #059669 100%);'
                            : ($isDanger
                                ? 'background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);'
                                : 'background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);');

                        $ringColor = $isSuccess ? '#10b981' : ($isDanger ? '#ef4444' : '#3b82f6');
                    @endphp

                    <div style="
                        border-radius: 1.25rem;
                        border-width: 1.5px;
                        border-style: solid;
                        overflow: hidden;
                        background-color: #ffffff;
                        animation: scanCardFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                        {{ $cardBorder }}
                    " class="dark:!bg-[#0f172a] dark:!border-slate-700/80">
                        <!-- Top Accent Bar -->
                        <div style="height: 6px; width: 100%; {{ $accentGradient }}"></div>

                        <div style="padding: 1.75rem 1.5rem; text-align: center;">
                            <!-- Header Top Bar: Badge Status & Waktu -->
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(229, 231, 235, 0.8);" class="dark:!border-slate-800">
                                <span style="
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 0.375rem;
                                    padding: 0.25rem 0.85rem;
                                    border-radius: 9999px;
                                    font-size: 0.8rem;
                                    font-weight: 700;
                                    {{ $badgeBg }}
                                ">
                                    @if($isSuccess)
                                        <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($isDanger)
                                        <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    @else
                                        <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                    {{ $lastScanned['status_kehadiran'] }}
                                </span>

                                <div style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; font-family: monospace; color: #64748b;" class="dark:!text-slate-400">
                                    <svg style="width: 0.95rem; height: 0.95rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>{{ $lastScanned['jam'] }} WIB</span>
                                </div>
                            </div>

                            <!-- FOTO SISWA VERSI DESKTOP (DIPERBESAR) -->
                            <div style="display: flex; justify-content: center; margin-bottom: 1.25rem;">
                                <div style="position: relative;">
                                    <div style="
                                        width: 9.5rem;
                                        height: 9.5rem;
                                        border-radius: 9999px;
                                        overflow: hidden;
                                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
                                        border: 4px solid {{ $ringColor }};
                                        background-color: #f1f5f9;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    " class="dark:!bg-slate-800">
                                        @if(!empty($lastScanned['foto']))
                                            <img
                                                src="{{ $lastScanned['foto'] }}"
                                                alt="{{ $lastScanned['nama'] }}"
                                                style="width: 100%; height: 100%; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.svg') }}';"
                                            />
                                        @else
                                            <img
                                                src="{{ asset('images/default-avatar.svg') }}"
                                                alt="Avatar Siswa"
                                                style="width: 100%; height: 100%; object-fit: cover;"
                                            />
                                        @endif
                                    </div>

                                    <!-- Indikator Titik Status -->
                                    <span style="
                                        position: absolute;
                                        bottom: 6px;
                                        right: 6px;
                                        width: 1.35rem;
                                        height: 1.35rem;
                                        border-radius: 9999px;
                                        background-color: {{ $ringColor }};
                                        border: 3px solid #ffffff;
                                        box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
                                    " class="dark:!border-slate-900"></span>
                                </div>
                            </div>

                            <!-- NAMA SISWA DI BAWAH FOTO -->
                            <h2 style="
                                font-size: 1.35rem;
                                font-weight: 800;
                                color: #0f172a;
                                line-height: 1.3;
                                margin: 0 0 0.5rem 0;
                            " class="dark:!text-white" title="{{ $lastScanned['nama'] }}">
                                {{ $lastScanned['nama'] }}
                            </h2>

                            <!-- KELAS & NOMOR INDUK DI BAWAH NAMA -->
                            <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                <span style="
                                    font-size: 0.75rem;
                                    font-weight: 700;
                                    padding: 0.2rem 0.65rem;
                                    border-radius: 0.375rem;
                                    background-color: #ecfdf5;
                                    color: #047857;
                                    border: 1px solid #d1fae5;
                                " class="dark:!bg-emerald-950/60 dark:!text-emerald-300 dark:!border-emerald-800/60">
                                    {{ $lastScanned['tipe_label'] ?? 'Siswa' }}
                                </span>

                                <span style="
                                    font-size: 0.75rem;
                                    font-weight: 700;
                                    padding: 0.2rem 0.65rem;
                                    border-radius: 0.375rem;
                                    background-color: #f1f5f9;
                                    color: #334155;
                                    border: 1px solid #e2e8f0;
                                " class="dark:!bg-slate-800 dark:!text-slate-300 dark:!border-slate-700">
                                    {{ $lastScanned['kelas'] }}
                                </span>

                                <span style="
                                    font-size: 0.75rem;
                                    font-family: monospace;
                                    font-weight: 700;
                                    padding: 0.2rem 0.65rem;
                                    border-radius: 0.375rem;
                                    background-color: #f8fafc;
                                    color: #475569;
                                    border: 1px solid #e2e8f0;
                                " class="dark:!bg-slate-800 dark:!text-emerald-400 dark:!border-slate-700">
                                    {{ $lastScanned['nomor_induk_label'] }}: {{ $lastScanned['nomor_induk'] }}
                                </span>
                            </div>

                            <!-- STATS LITERASI CHIPS (TAB KUNJUNGAN/PINJAM) -->
                            @if(isset($lastScanned['skor']))
                                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 0.85rem; text-align: center;">
                                    <div style="padding: 0.5rem 0.25rem; border-radius: 0.5rem; background-color: #ecfdf5; border: 1px solid #d1fae5;" class="dark:!bg-emerald-950/40 dark:!border-emerald-800/40">
                                        <div style="font-size: 1.1rem; font-weight: 800; color: #047857;" class="dark:!text-emerald-300">★ {{ $lastScanned['skor'] }}</div>
                                        <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #059669;" class="dark:!text-emerald-400">Poin Literasi</div>
                                    </div>
                                    <div style="padding: 0.5rem 0.25rem; border-radius: 0.5rem; background-color: #f8fafc; border: 1px solid #e2e8f0;" class="dark:!bg-slate-800 dark:!border-slate-700">
                                        <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;" class="dark:!text-slate-200">{{ $lastScanned['total_kunjungan'] ?? 0 }}</div>
                                        <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748b;" class="dark:!text-slate-400">Kunjungan</div>
                                    </div>
                                    <div style="padding: 0.5rem 0.25rem; border-radius: 0.5rem; background-color: #f8fafc; border: 1px solid #e2e8f0;" class="dark:!bg-slate-800 dark:!border-slate-700">
                                        <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b;" class="dark:!text-slate-200">{{ $lastScanned['total_pinjam'] ?? 0 }}</div>
                                        <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748b;" class="dark:!text-slate-400">Dipinjam</div>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($lastScanned['extra_info']))
                                <div style="font-size: 0.775rem; font-weight: 600; padding: 0.5rem 0.75rem; border-radius: 0.5rem; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; text-align: center; margin-bottom: 0.85rem;" class="dark:!bg-emerald-950/50 dark:!border-emerald-800/50 dark:!text-emerald-300">
                                    📖 {{ $lastScanned['extra_info'] }}
                                </div>
                            @endif

                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 1rem;" class="dark:!text-slate-400">
                                {{ $lastScanned['tanggal'] }}
                            </div>

                            <!-- BANNER PESAN STATUS -->
                            <div style="
                                padding: 0.85rem 1rem;
                                border-radius: 0.625rem;
                                font-size: 0.85rem;
                                font-weight: 600;
                                line-height: 1.4;
                                {{ $badgeBg }}
                            ">
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                @elseif($message)
                    <!-- KARTU ERROR / PERINGATAN DESKTOP -->
                    <div style="
                        border-radius: 1.25rem;
                        border: 1.5px solid rgba(239, 68, 68, 0.4);
                        box-shadow: 0 10px 30px -5px rgba(239, 68, 68, 0.18);
                        overflow: hidden;
                        background-color: #ffffff;
                        animation: scanCardFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                    " class="dark:!bg-[#0f172a] dark:!border-rose-900/60">
                        <div style="height: 6px; width: 100%; background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);"></div>
                        <div style="padding: 2.5rem 1.75rem; text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <div style="
                                width: 5rem;
                                height: 5rem;
                                border-radius: 9999px;
                                background-color: #fee2e2;
                                color: #dc2626;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin-bottom: 1.25rem;
                                border: 3px solid rgba(239, 68, 68, 0.25);
                            " class="dark:!bg-rose-950/60 dark:!text-rose-400">
                                <svg style="width: 2.5rem; height: 2.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>

                            <h3 style="font-size: 1.25rem; font-weight: 800; color: #b91c1c; margin: 0 0 0.5rem 0;" class="dark:!text-rose-400">
                                Scan Tidak Berhasil
                            </h3>
                            <p style="font-size: 0.95rem; font-weight: 600; color: #334155; margin: 0 0 0.75rem 0; line-height: 1.4;" class="dark:!text-slate-300">
                                {{ $message }}
                            </p>
                            <p style="font-size: 0.775rem; color: #64748b; margin: 0;" class="dark:!text-slate-400">
                                Pastikan kode buku atau kartu RFID siswa sudah terdaftar pada database sistem.
                            </p>
                        </div>
                    </div>
                @else
                    <!-- KARTU SIAGA / STANDBY DESKTOP (Sebelum ada scan) -->
                    <div style="
                        border-radius: 1.25rem;
                        border: 1.5px dashed rgba(16, 185, 129, 0.35);
                        background-color: #ffffff;
                        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.08);
                        padding: 3rem 1.5rem;
                        text-align: center;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        min-height: 420px;
                    " class="dark:!bg-[#0f172a] dark:!border-emerald-800/40">
                        <div style="
                            position: relative;
                            width: 6.5rem;
                            height: 6.5rem;
                            border-radius: 9999px;
                            background-color: #ecfdf5;
                            border: 2px solid #a7f3d0;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-bottom: 1.25rem;
                        " class="dark:!bg-emerald-950/40 dark:!border-emerald-800/50">
                            <span style="font-size: 2.75rem;">📖</span>
                        </div>

                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem 0;" class="dark:!text-white">
                            Scanner Perpustakaan Siaga
                        </h3>
                        <p style="font-size: 0.825rem; color: #64748b; max-width: 290px; margin: 0 0 1.5rem 0; line-height: 1.45;" class="dark:!text-slate-400">
                            Pilih tab mode di sebelah kiri (Kunjungan / Pinjam / Kembali) lalu tempelkan kartu siswa atau scan barcode buku.
                        </p>

                        <span style="
                            display: inline-flex;
                            align-items: center;
                            gap: 0.5rem;
                            padding: 0.4rem 1rem;
                            border-radius: 9999px;
                            background-color: #d1fae5;
                            color: #065f46;
                            font-size: 0.775rem;
                            font-weight: 700;
                        " class="dark:!bg-emerald-950/60 dark:!text-emerald-300">
                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #10b981; display: inline-block;"></span>
                            Siap Memindai
                        </span>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- SYNTHESIZED AUDIO CHIMES & AUTOFOCUS LISTENER -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('perpustakaanScannerInput');
            if (input) input.focus();

            // Re-focus setelah setiap Livewire update agar scanner siap terus
            Livewire.hook('morph.updated', () => {
                const el = document.getElementById('perpustakaanScannerInput');
                if (el) el.focus();
            });
        });

        // AUDIO PLAYER (success.mpeg & failed.mpeg)
        const audioSuccess = new Audio("{{ asset('success.mpeg') }}");
        const audioFailed = new Audio("{{ asset('failed.mpeg') }}");
        audioSuccess.preload = 'auto';
        audioFailed.preload = 'auto';

        // Pemanasan audio pipeline pada interaksi pertama (unlock autoplay)
        let audioUnlocked = false;
        function unlockAudio() {
            if (audioUnlocked) return;
            audioSuccess.load();
            audioFailed.load();
            audioUnlocked = true;
        }
        document.addEventListener('click', unlockAudio, { once: true });
        document.addEventListener('keydown', unlockAudio, { once: true });

        // Listener event play-sound dari backend
        window.addEventListener('play-sound', event => {
            const type = event.detail?.type
                ?? (Array.isArray(event.detail) ? event.detail[0]?.type : null)
                ?? 'danger';

            playAudio(type);
        });

        function playAudio(type) {
            const isSuccess = (type === 'success');
            const targetAudio = isSuccess ? audioSuccess : audioFailed;

            try {
                targetAudio.pause();
                targetAudio.currentTime = 0;
                const playPromise = targetAudio.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.warn('Audio play restricted or failed:', error);
                        try {
                            const clone = new Audio(isSuccess ? "{{ asset('success.mpeg') }}" : "{{ asset('failed.mpeg') }}");
                            clone.play().catch(e => console.warn('Audio clone failed:', e));
                        } catch (e) {}
                    });
                }
            } catch (err) {
                console.warn('Audio playback error:', err);
            }
        }
    </script>
</x-filament-panels::page>
