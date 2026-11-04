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
                 SISI KIRI: SCANNER INTERFACE
                 ========================================== -->
            <div class="scanner-left-panel">

                <!-- TAB TOMBOL PILIHAN MODE (RFID vs KAMERA) -->
                <div style="display: flex; gap: 0.75rem; margin-bottom: 0.25rem;" class="no-print">
                    <button id="btn-rfid" onclick="switchMode('rfid')" style="
                        flex: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        padding: 0.75rem 1rem;
                        border-radius: 0.5rem;
                        font-weight: bold;
                        font-size: 0.875rem;
                        cursor: pointer;
                        border: 1px solid #d1d5db;
                        background-color: #16804e;
                        color: white;
                        transition: all 0.2s;
                    ">
                        <!-- Icon Card/RFID -->
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                        </svg>
                        Mode Kartu / RFID
                    </button>

                    <button id="btn-camera" onclick="switchMode('camera')" style="
                        flex: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                        padding: 0.75rem 1rem;
                        border-radius: 0.5rem;
                        font-weight: bold;
                        font-size: 0.875rem;
                        cursor: pointer;
                        border: 1px solid #d1d5db;
                        background-color: #ffffff;
                        color: #374151;
                        transition: all 0.2s;
                    ">
                        <!-- Icon Kamera -->
                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Mode Kamera QR
                    </button>
                </div>

                <!-- (KHUSUS MOBILE) ALERT CARD: Hanya muncul ketika mode mobile DAN ada scan / pesan -->
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

                <!-- SECTION 1: RFID SCANNER (Aktif Pertama Kali) -->
                <div id="section-rfid" style="display: block;">
                    <x-filament::section>
                        <x-slot name="heading">
                            Input Scanner (USB / RFID)
                        </x-slot>

                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1.25rem; text-align: center;">
                            Dekatkan Kartu RFID atau arahkan Barcode ke alat scanner Anda.
                        </p>

                        <form wire:submit.prevent="processScan" style="margin-bottom: 0.5rem;">
                            <x-filament::input.wrapper>
                                <x-filament::input
                                    type="text"
                                    id="scannerInput"
                                    wire:model="scannedCode"
                                    placeholder="Tempel kartu RFID atau scan barcode..."
                                    style="text-align: center; font-size: 1.125rem; padding: 0.75rem; font-weight: 600;"
                                    autofocus
                                    autocomplete="off"
                                />
                            </x-filament::input.wrapper>
                        </form>
                    </x-filament::section>
                </div>

                <!-- SECTION 2: KAMERA SCANNER (Awalnya Tersembunyi) -->
                <div id="section-camera" style="display: none;">
                    <x-filament::section>
                        <x-slot name="heading">
                            Scan Menggunakan Kamera HP / Laptop
                        </x-slot>

                        <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                            <!-- Box pembungkus kamera dengan wire:ignore -->
                            <div id="reader" wire:ignore style="
                                width: 100%;
                                max-width: 350px;
                                border-radius: 0.75rem;
                                overflow: hidden;
                                border: 1px solid #d1d5db;
                                background-color: #f3f4f6;
                            "></div>
                        </div>
                    </x-filament::section>
                </div>

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
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(229, 231, 235, 0.8);" class="dark:!border-slate-800">
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
                                font-size: 1.4rem;
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
                                    {{ $lastScanned['tipe_label'] }}
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

                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 1.25rem;" class="dark:!text-slate-400">
                                {{ $lastScanned['tanggal'] }} • Scan {{ $lastScanned['tipe_absen'] }}
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
                                Pastikan kartu RFID atau barcode QR sudah terdaftar di sistem.
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
                            <svg style="width: 3rem; height: 3rem; color: #10b981;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0a2 2 0 100-4 2 2 0 000 4zm-8 4v-4m0 0a2 2 0 100-4 2 2 0 000 4zm14-8a2 2 0 11-4 0 2 2 0 014 0zM4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
                            </svg>
                        </div>

                        <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem 0;" class="dark:!text-white">
                            Scanner Siaga (Standby)
                        </h3>
                        <p style="font-size: 0.825rem; color: #64748b; max-width: 280px; margin: 0 0 1.5rem 0; line-height: 1.45;" class="dark:!text-slate-400">
                            Dekatkan Kartu RFID atau arahkan Barcode ke alat scanner untuk memproses absensi santri/ustadz secara instan.
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

    <!-- Script Javascript Toggle Mode & Integrasi Kamera QR -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('scannerInput');

            // Variabel Status Mode Aktif (Default: 'rfid')
            let activeMode = 'rfid';
            let html5QrcodeScanner = null;

            // Fokus otomatis pertama kali dimuat
            setTimeout(() => { if (input) input.focus(); }, 150);

            // JAGA AUTOFOCUS HANYA JIKA MODE RFID AKTIF!
            document.addEventListener('click', () => {
                if (activeMode === 'rfid' && input) {
                    input.focus();
                }
            });

            // Re-focus setelah Livewire render
            Livewire.hook('morph.updated', () => {
                if (activeMode === 'rfid' && input) {
                    input.focus();
                }
            });

            // LOGIKA TOGGLE SWITCH MODE
            window.switchMode = function(mode) {
                activeMode = mode;

                const btnRfid = document.getElementById('btn-rfid');
                const btnCamera = document.getElementById('btn-camera');
                const secRfid = document.getElementById('section-rfid');
                const secCamera = document.getElementById('section-camera');

                if (mode === 'rfid') {
                    // Tampilkan RFID, Sembunyikan Kamera
                    secRfid.style.display = 'block';
                    secCamera.style.display = 'none';

                    // Update Style Tombol
                    btnRfid.style.backgroundColor = '#16804e';
                    btnRfid.style.color = '#ffffff';
                    btnCamera.style.backgroundColor = '#ffffff';
                    btnCamera.style.color = '#374151';

                    // Fokuskan kursor kembali ke input RFID
                    setTimeout(() => { if (input) input.focus(); }, 100);

                    // MATIKAN KAMERA TOTAL (Untuk privasi & hemat baterai)
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear().then(() => {
                            html5QrcodeScanner = null;
                        }).catch(err => console.error(err));
                    }
                } else {
                    // Tampilkan Kamera, Sembunyikan RFID
                    secRfid.style.display = 'none';
                    secCamera.style.display = 'block';

                    // Update Style Tombol
                    btnCamera.style.backgroundColor = '#16804e';
                    btnCamera.style.color = '#ffffff';
                    btnRfid.style.backgroundColor = '#ffffff';
                    btnRfid.style.color = '#374151';

                    // Jalankan Inisialisasi Kamera
                    startCamera();
                }
            }

            // FUNGSI MENYALAKAN KAMERA
            function startCamera() {
                if (html5QrcodeScanner) return; // Mencegah dobel inisialisasi

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    {
                        fps: 10,
                        qrbox: { width: 220, height: 220 },
                        aspectRatio: 1.0,
                        showTorchButtonIfSupported: true
                    },
                    /* verbose= */ false
                );

                let isCooldown = false;

                function onScanSuccess(decodedText, decodedResult) {
                    if (isCooldown) return;
                    isCooldown = true;

                    if (input) input.value = decodedText;

                    // Kirim data langsung dalam 1 roundtrip Livewire (Ultra-low latency)
                    @this.call('processScan', decodedText);

                    // Jeda 2.5 detik agar tidak spam scan
                    setTimeout(() => {
                        isCooldown = false;
                    }, 2500);
                }

                html5QrcodeScanner.render(onScanSuccess);
            }

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

            // LISTENER SUARA DARI BACKEND
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
        });
    </script>
</x-filament-panels::page>
