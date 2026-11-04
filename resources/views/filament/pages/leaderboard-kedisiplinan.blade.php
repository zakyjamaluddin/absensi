<x-filament-panels::page>
    @php
        $data = $this->getRatingData();
        $allItems = $data['all'] ?? [];
        $top3 = $data['top3'] ?? [];
        $bottom3 = $data['bottom3'] ?? [];
        $kelases = $this->getKelases();

        $top1 = $top3[0] ?? null;
        $top2 = $top3[1] ?? null;
        $top3Item = $top3[2] ?? null;
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- 1. BAR KONTROL & FILTER UTAMA (SOLID WHITE OPACITY 1) -->
        <div style="
            background: #ffffff !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        " class="dark:!bg-slate-900 dark:!border-slate-800">

            <!-- TOGGLE TAB SISWA VS GURU (SEGMENTED CONTROL MODERN) -->
            <div style="
                display: inline-flex;
                background: #f1f5f9;
                padding: 0.25rem;
                border-radius: 0.625rem;
                gap: 0.25rem;
                border: 1px solid #e2e8f0;
            " class="dark:!bg-slate-800 dark:!border-slate-700">
                <button
                    type="button"
                    wire:click="setTab('siswa')"
                    style="
                        padding: 0.45rem 1rem;
                        border-radius: 0.5rem;
                        font-size: 0.85rem;
                        font-weight: 700;
                        cursor: pointer;
                        border: none;
                        transition: all 0.2s;
                        background: {{ $tab === 'siswa' ? '#ffffff' : 'transparent' }};
                        color: {{ $tab === 'siswa' ? '#047857' : '#64748b' }};
                        box-shadow: {{ $tab === 'siswa' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none' }};
                    "
                    class="dark:{{ $tab === 'siswa' ? '!bg-slate-900 !text-emerald-400' : '!text-slate-400' }}"
                >
                    Kedisiplinan Siswa
                </button>

                <button
                    type="button"
                    wire:click="setTab('guru')"
                    style="
                        padding: 0.45rem 1rem;
                        border-radius: 0.5rem;
                        font-size: 0.85rem;
                        font-weight: 700;
                        cursor: pointer;
                        border: none;
                        transition: all 0.2s;
                        background: {{ $tab === 'guru' ? '#ffffff' : 'transparent' }};
                        color: {{ $tab === 'guru' ? '#047857' : '#64748b' }};
                        box-shadow: {{ $tab === 'guru' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none' }};
                    "
                    class="dark:{{ $tab === 'guru' ? '!bg-slate-900 !text-emerald-400' : '!text-slate-400' }}"
                >
                    Kedisiplinan Guru / Ustadz
                </button>
            </div>

            <!-- DROPDOWN PERIODE WAKTU & FILTER KELAS -->
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <!-- Select Periode -->
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <label style="font-size: 0.825rem; font-weight: 700; color: #475569;" class="dark:!text-slate-300">
                        Periode:
                    </label>
                    <select
                        wire:model.live="periode"
                        style="
                            padding: 0.45rem 0.85rem;
                            border-radius: 0.5rem;
                            font-size: 0.825rem;
                            font-weight: 600;
                            border: 1px solid #d1d5db;
                            background: #ffffff;
                            color: #1e293b;
                            cursor: pointer;
                            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                        "
                        class="dark:!bg-slate-800 dark:!text-white dark:!border-slate-700"
                    >
                        <option value="bulan_ini">Bulan Ini ({{ now()->translatedFormat('F Y') }})</option>
                        <option value="tahun_ini">Tahun Ini ({{ now()->year }})</option>
                        <option value="all">Semua Waktu</option>
                        <option value="custom">Custom (Pilih Rentang Tanggal)</option>
                    </select>
                </div>

                <!-- Input Rentang Tanggal Kalender (Jika Pilih Custom) -->
                @if($periode === 'custom')
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <input
                            type="date"
                            wire:model.live="startDate"
                            style="
                                padding: 0.4rem 0.65rem;
                                border-radius: 0.375rem;
                                border: 1px solid #10b981;
                                font-size: 0.825rem;
                                font-weight: 600;
                            "
                            class="dark:!bg-slate-800 dark:!text-white"
                        />
                        <span style="font-size: 0.8rem; color: #64748b;">s/d</span>
                        <input
                            type="date"
                            wire:model.live="endDate"
                            style="
                                padding: 0.4rem 0.65rem;
                                border-radius: 0.375rem;
                                border: 1px solid #10b981;
                                font-size: 0.825rem;
                                font-weight: 600;
                            "
                            class="dark:!bg-slate-800 dark:!text-white"
                        />
                    </div>
                @endif

                <!-- Filter Kelas (Khusus Tab Siswa) -->
                @if($tab === 'siswa')
                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                        <label style="font-size: 0.825rem; font-weight: 700; color: #475569;" class="dark:!text-slate-300">
                            Kelas:
                        </label>
                        <select
                            wire:model.live="kelasId"
                            style="
                                padding: 0.45rem 0.85rem;
                                border-radius: 0.5rem;
                                font-size: 0.825rem;
                                font-weight: 600;
                                border: 1px solid #d1d5db;
                                background: #ffffff;
                                color: #1e293b;
                                cursor: pointer;
                                box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                            "
                            class="dark:!bg-slate-800 dark:!text-white dark:!border-slate-700"
                        >
                            <option value="all">Semua Kelas</option>
                            @foreach($kelases as $id => $namaKelas)
                                <option value="{{ $id }}">{{ $namaKelas }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

        </div>

        <!-- 2. ATURAN PERHITUNGAN POIN (MINIMALIS & ELEGAN) -->
        <div style="
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.06) 0%, rgba(5, 150, 105, 0.02) 100%);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        ">
            <span style="font-size: 0.825rem; font-weight: 800; color: #065f46; letter-spacing: 0.02em;" class="dark:!text-emerald-300">
                Sistem Poin Kedisiplinan:
            </span>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <span style="padding: 0.2rem 0.65rem; border-radius: 9999px; background: #f0fdf4; border: 1px solid #86efac; color: #15803d; font-size: 0.75rem; font-weight: 800;" class="dark:!bg-emerald-950 dark:!text-emerald-300">
                    🌟 Hadir Sangat Pagi (&ge;15 mnt): +12 Poin
                </span>
                <span style="padding: 0.2rem 0.65rem; border-radius: 9999px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; font-size: 0.75rem; font-weight: 700;" class="dark:!bg-emerald-950 dark:!text-emerald-300">
                    Tepat Waktu: +10 Poin
                </span>
                <span style="padding: 0.2rem 0.65rem; border-radius: 9999px; background: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-size: 0.75rem; font-weight: 700;" class="dark:!bg-amber-950 dark:!text-amber-300">
                    Terlambat: +3 Poin
                </span>
                <span style="padding: 0.2rem 0.65rem; border-radius: 9999px; background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-size: 0.75rem; font-weight: 700;" class="dark:!bg-slate-800 dark:!text-slate-300">
                    Sakit & Izin: 0 Poin
                </span>
                <span style="padding: 0.2rem 0.65rem; border-radius: 9999px; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; font-size: 0.75rem; font-weight: 700;" class="dark:!bg-rose-950 dark:!text-rose-300">
                    Alpa: -10 Poin
                </span>
            </div>
        </div>

        <!-- ==============================================================
             3. PODIUM 3 TERBAIK (TOP 3) - BERTINGKAT TANPA FOTO
             (SAMA PERSIS DENGAN VISUALISASI RATING MINAT LITERASI)
             ============================================================== -->
        @if(count($top3) > 0)
            <div>
                <div style="margin-bottom: 0.85rem;">
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;" class="dark:!text-white">
                        3 Teratas Kedisiplinan (Top 3 {{ $tab === 'siswa' ? 'Siswa' : 'Guru / Ustadz' }})
                    </h2>
                    <p style="font-size: 0.775rem; color: #64748b; margin: 0.15rem 0 0 0;" class="dark:!text-slate-400">
                        Peringkat teladan dengan akumulasi skor kedisiplinan tertinggi
                    </p>
                </div>

                <div style="
                    background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
                    border: 1px solid #cbd5e1;
                    border-radius: 1rem;
                    padding: 2.5rem 1rem 1rem;
                    display: flex;
                    align-items: flex-end;
                    justify-content: center;
                    gap: 1.25rem;
                    flex-wrap: wrap;
                " class="dark:!bg-slate-900/60 dark:!border-slate-800">

                    <!-- JUARA 2 (PERAK - SEBELAH KIRI) -->
                    @if($top2)
                        <div style="
                            flex: 1;
                            min-width: 210px;
                            max-width: 250px;
                            background: #ffffff;
                            border-radius: 0.75rem 0.75rem 0 0;
                            border: 2px solid #94a3b8;
                            border-bottom: none;
                            padding: 1.25rem 1rem;
                            text-align: center;
                            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
                            height: 235px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                        " class="dark:!bg-slate-800 dark:!border-slate-600">
                            <div>
                                <div style="font-size: 2rem; margin-bottom: 0.25rem;">🥈</div>
                                <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">
                                    Juara 2
                                </div>
                                <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-top: 0.35rem; line-height: 1.3;" class="dark:!text-white">
                                    {{ $top2['nama'] }}
                                </div>
                                <div style="font-size: 0.775rem; color: #64748b; margin-top: 0.2rem;" class="dark:!text-slate-400">
                                    {{ $top2['kelas'] }} • {{ $top2['nomor_induk_label'] }}: {{ $top2['nomor_induk'] }}
                                </div>
                            </div>
                            <div style="background: #f1f5f9; padding: 0.6rem 0.5rem; border-radius: 0.5rem; margin-top: 0.5rem;" class="dark:!bg-slate-700/60">
                                <div style="font-size: 1.25rem; font-weight: 900; color: #334155;" class="dark:!text-slate-200">
                                    {{ $top2['skor'] }} Poin
                                </div>
                                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.15rem;" class="dark:!text-slate-400">
                                    {{ $top2['tepat_waktu'] }} Tepat • {{ $top2['terlambat'] }} Lambat • {{ $top2['alpa'] }} Alpa
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- JUARA 1 (EMAS - PALING TINGGI DI TENGAH) -->
                    @if($top1)
                        <div style="
                            flex: 1;
                            min-width: 230px;
                            max-width: 270px;
                            background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
                            border-radius: 1rem 1rem 0 0;
                            border: 3px solid #f59e0b;
                            border-bottom: none;
                            padding: 1.5rem 1rem;
                            text-align: center;
                            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.25);
                            height: 275px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            position: relative;
                        " class="dark:!bg-slate-800 dark:!border-amber-500">
                            <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); font-size: 2.25rem;">
                                👑
                            </div>
                            <div style="margin-top: 0.5rem;">
                                <div style="font-size: 2.25rem; margin-bottom: 0.25rem;">🥇</div>
                                <div style="font-size: 0.8rem; font-weight: 900; color: #b45309; text-transform: uppercase;">
                                    Juara 1 • Teladan Kedisiplinan
                                </div>
                                <div style="font-size: 1.25rem; font-weight: 900; color: #78350f; margin-top: 0.35rem; line-height: 1.3;" class="dark:!text-amber-300">
                                    {{ $top1['nama'] }}
                                </div>
                                <div style="font-size: 0.8rem; color: #92400e; font-weight: 600; margin-top: 0.2rem;" class="dark:!text-amber-200">
                                    {{ $top1['kelas'] }} • {{ $top1['nomor_induk_label'] }}: {{ $top1['nomor_induk'] }}
                                </div>
                            </div>
                            <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 0.65rem 0.5rem; border-radius: 0.5rem;" class="dark:!bg-amber-950/60 dark:!border-amber-800">
                                <div style="font-size: 1.45rem; font-weight: 900; color: #92400e;" class="dark:!text-amber-300">
                                    ★ {{ $top1['skor'] }} Poin
                                </div>
                                <div style="font-size: 0.725rem; color: #b45309; font-weight: 700; margin-top: 0.15rem;" class="dark:!text-amber-400">
                                    {{ $top1['tepat_waktu'] }} Tepat • {{ $top1['terlambat'] }} Lambat • {{ $top1['alpa'] }} Alpa
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- JUARA 3 (PERUNGGU - SEBELAH KANAN) -->
                    @if($top3Item)
                        <div style="
                            flex: 1;
                            min-width: 210px;
                            max-width: 250px;
                            background: #ffffff;
                            border-radius: 0.75rem 0.75rem 0 0;
                            border: 2px solid #b45309;
                            border-bottom: none;
                            padding: 1.25rem 1rem;
                            text-align: center;
                            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
                            height: 205px;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                        " class="dark:!bg-slate-800 dark:!border-amber-700">
                            <div>
                                <div style="font-size: 2rem; margin-bottom: 0.25rem;">🥉</div>
                                <div style="font-size: 0.75rem; font-weight: 800; color: #b45309; text-transform: uppercase;">
                                    Juara 3
                                </div>
                                <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-top: 0.35rem; line-height: 1.3;" class="dark:!text-white">
                                    {{ $top3Item['nama'] }}
                                </div>
                                <div style="font-size: 0.775rem; color: #64748b; margin-top: 0.2rem;" class="dark:!text-slate-400">
                                    {{ $top3Item['kelas'] }} • {{ $top3Item['nomor_induk_label'] }}: {{ $top3Item['nomor_induk'] }}
                                </div>
                            </div>
                            <div style="background: #fff7ed; padding: 0.6rem 0.5rem; border-radius: 0.5rem; margin-top: 0.5rem;" class="dark:!bg-amber-950/40">
                                <div style="font-size: 1.25rem; font-weight: 900; color: #9a3412;" class="dark:!text-amber-400">
                                    {{ $top3Item['skor'] }} Poin
                                </div>
                                <div style="font-size: 0.7rem; color: #9a3412; margin-top: 0.15rem;" class="dark:!text-amber-500">
                                    {{ $top3Item['tepat_waktu'] }} Tepat • {{ $top3Item['terlambat'] }} Lambat • {{ $top3Item['alpa'] }} Alpa
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        @endif

        <!-- ==============================================================
             4. 3 TERENDAH KEDISIPLINAN - DISAJIKAN SEBAGAI TABEL
             ============================================================== -->
        @if(!empty($bottom3))
            <div style="
                background: #ffffff !important;
                opacity: 1 !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                border: 1px solid #fecaca;
                border-radius: 0.75rem;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            " class="dark:!bg-slate-900 dark:!border-rose-900/60">

                <div style="
                    padding: 0.85rem 1.25rem;
                    background: #fef2f2;
                    border-bottom: 1px solid #fee2e2;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                " class="dark:!bg-rose-950/40 dark:!border-rose-900/60">
                    <div>
                        <h3 style="font-size: 0.95rem; font-weight: 800; color: #b91c1c; margin: 0;" class="dark:!text-rose-300">
                            3 Kedisiplinan Terendah (Perlu Evaluasi & Pembinaan)
                        </h3>
                        <p style="font-size: 0.75rem; color: #991b1b; margin: 0.15rem 0 0 0;" class="dark:!text-rose-400">
                            Daftar {{ $tab === 'siswa' ? 'siswa' : 'guru' }} dengan akumulasi pelanggaran alpa/keterlambatan tertinggi
                        </p>
                    </div>
                    <span style="
                        padding: 0.2rem 0.65rem;
                        border-radius: 9999px;
                        background: #ffffff;
                        color: #b91c1c;
                        border: 1px solid #fecaca;
                        font-size: 0.75rem;
                        font-weight: 700;
                    " class="dark:!bg-rose-950 dark:!text-rose-300">
                        {{ count($bottom3) }} Data Evaluasi
                    </span>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.825rem;">
                        <thead>
                            <tr style="background: #fff5f5; border-bottom: 1px solid #fed7d7; color: #7f1d1d; font-weight: 700;" class="dark:!bg-rose-950/20 dark:!border-rose-900/40 dark:!text-rose-300">
                                <th style="padding: 0.65rem 1rem; width: 80px; text-align: center;">Peringkat</th>
                                <th style="padding: 0.65rem 1rem;">Nama Lengkap</th>
                                <th style="padding: 0.65rem 1rem;">{{ $tab === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
                                <th style="padding: 0.65rem 1rem; text-align: center; color: #047857;">Tepat</th>
                                <th style="padding: 0.65rem 1rem; text-align: center; color: #b45309;">Lambat</th>
                                <th style="padding: 0.65rem 1rem; text-align: center; color: #b91c1c; font-weight: 800;">Alpa</th>
                                <th style="padding: 0.65rem 1rem; text-align: center; font-weight: 800;">Total Skor</th>
                                <th style="padding: 0.65rem 1rem; text-align: center;">Kehadiran</th>
                                <th style="padding: 0.65rem 1rem; text-align: center;">Status Pembinaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bottom3 as $item)
                                <tr style="border-bottom: 1px solid #fee2e2;" class="hover:bg-rose-50/50 dark:!border-rose-950 dark:hover:!bg-rose-950/30">
                                    <td style="padding: 0.65rem 1rem; text-align: center; font-weight: 800; color: #b91c1c;">
                                        #{{ $item['rank'] }}
                                    </td>
                                    <td style="padding: 0.65rem 1rem;">
                                        <div style="font-weight: 700; color: #1e293b;" class="dark:!text-white">
                                            {{ $item['nama'] }}
                                        </div>
                                        <div style="font-size: 0.725rem; color: #64748b;" class="dark:!text-slate-400">
                                            {{ $item['nomor_induk_label'] }}: {{ $item['nomor_induk'] }}
                                        </div>
                                    </td>
                                    <td style="padding: 0.65rem 1rem; color: #475569;" class="dark:!text-slate-300">
                                        {{ $item['kelas'] }}
                                    </td>
                                    <td style="padding: 0.65rem 1rem; text-align: center; font-weight: 700; color: #047857;">
                                        {{ $item['tepat_waktu'] }}
                                    </td>
                                    <td style="padding: 0.65rem 1rem; text-align: center; font-weight: 700; color: #b45309;">
                                        {{ $item['terlambat'] }}
                                    </td>
                                    <td style="padding: 0.65rem 1rem; text-align: center; font-weight: 800; color: #b91c1c;">
                                        {{ $item['alpa'] }}
                                    </td>
                                    <td style="padding: 0.65rem 1rem; text-align: center;">
                                        <span style="
                                            display: inline-block;
                                            padding: 0.15rem 0.6rem;
                                            border-radius: 9999px;
                                            font-weight: 800;
                                            font-size: 0.775rem;
                                            background: #fee2e2;
                                            color: #b91c1c;
                                            border: 1px solid #fecaca;
                                        ">
                                            {{ $item['skor'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.65rem 1rem; text-align: center; font-weight: 700; color: #64748b;">
                                        {{ $item['persentase'] }}%
                                    </td>
                                    <td style="padding: 0.65rem 1rem; text-align: center;">
                                        <span style="
                                            display: inline-block;
                                            padding: 0.15rem 0.55rem;
                                            border-radius: 0.375rem;
                                            font-size: 0.7rem;
                                            font-weight: 700;
                                            background: #fff1f2;
                                            color: #be123c;
                                            border: 1px solid #fecdd3;
                                        ">
                                            Konseling / Pembinaan
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif

        <!-- ==============================================================
             5. TABEL LENGKAP SEMUA PERINGKAT KEDISIPLINAN
             ============================================================== -->
        <div style="
            background: #ffffff !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        " class="dark:!bg-slate-900 dark:!border-slate-800">

            <div style="
                padding: 1rem 1.25rem;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 0.5rem;
            " class="dark:!border-slate-800">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; color: #1f2937; margin: 0;" class="dark:!text-white">
                        Daftar Peringkat Lengkap ({{ count($allItems) }} {{ $tab === 'siswa' ? 'Siswa' : 'Guru / Ustadz' }})
                    </h3>
                    <p style="font-size: 0.775rem; color: #64748b; margin: 0.15rem 0 0 0;" class="dark:!text-slate-400">
                        Diurutkan berdasarkan skor kedisiplinan tertinggi
                    </p>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 700;" class="dark:!bg-slate-800 dark:!border-slate-700 dark:!text-slate-300">
                            <th style="padding: 0.75rem 1rem; text-align: center; width: 60px;">Rank</th>
                            <th style="padding: 0.75rem 1rem;">Nama Lengkap</th>
                            <th style="padding: 0.75rem 1rem;">{{ $tab === 'siswa' ? 'Kelas' : 'Jabatan' }}</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: #059669;">Tepat</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: #d97706;">Lambat</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: #2563eb;">Sakit</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: #4b5563;">Izin</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; color: #dc2626;">Alpa</th>
                            <th style="padding: 0.75rem 1rem; text-align: center; font-weight: 800;">Total Poin</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Kehadiran</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allItems as $item)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;" class="hover:bg-slate-50 dark:!border-slate-800 dark:hover:!bg-slate-800/50">
                                <!-- Rank -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 800;">
                                    @if($item['rank'] === 1)
                                        <span style="display: inline-block; width: 28px; height: 28px; line-height: 28px; border-radius: 9999px; background: #fef3c7; color: #b45309; font-weight: 900; border: 1px solid #fde68a;">1</span>
                                    @elseif($item['rank'] === 2)
                                        <span style="display: inline-block; width: 28px; height: 28px; line-height: 28px; border-radius: 9999px; background: #f1f5f9; color: #475569; font-weight: 900; border: 1px solid #cbd5e1;">2</span>
                                    @elseif($item['rank'] === 3)
                                        <span style="display: inline-block; width: 28px; height: 28px; line-height: 28px; border-radius: 9999px; background: #ffedd5; color: #9a3412; font-weight: 900; border: 1px solid #fed7aa;">3</span>
                                    @else
                                        <span style="color: #64748b; font-weight: 700;">#{{ $item['rank'] }}</span>
                                    @endif
                                </td>

                                <!-- Nama & Foto Avatar kecil (opsional di tabel) -->
                                <td style="padding: 0.75rem 1rem;">
                                    <div style="font-weight: 700; color: #0f172a;" class="dark:!text-white">
                                        {{ $item['nama'] }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748b; font-family: monospace;" class="dark:!text-slate-400">
                                        {{ $item['nomor_induk_label'] }}: {{ $item['nomor_induk'] }}
                                    </div>
                                </td>

                                <!-- Kelas / Jabatan -->
                                <td style="padding: 0.75rem 1rem; color: #334155; font-weight: 500;" class="dark:!text-slate-300">
                                    {{ $item['kelas'] }}
                                </td>

                                <!-- Tepat Waktu -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: #059669;">
                                    <div>{{ $item['tepat_waktu'] }}</div>
                                    @if(!empty($item['lebih_awal']) && $item['lebih_awal'] > 0)
                                        <div style="font-size: 0.68rem; color: #15803d; font-weight: 800; background: #dcfce7; border-radius: 9999px; padding: 1px 6px; margin-top: 3px; display: inline-block;" title="{{ $item['lebih_awal'] }} kali hadir >= 15 menit sebelum batas (+12 Poin)">
                                            ★ {{ $item['lebih_awal'] }} Pagi
                                        </div>
                                    @endif
                                </td>

                                <!-- Terlambat -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: #d97706;">
                                    {{ $item['terlambat'] }}
                                </td>

                                <!-- Sakit -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: #2563eb;">
                                    {{ $item['sakit'] }}
                                </td>

                                <!-- Izin -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: #475569;">
                                    {{ $item['izin'] }}
                                </td>

                                <!-- Alpa -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 800; color: {{ $item['alpa'] > 0 ? '#dc2626' : '#9ca3af' }};">
                                    {{ $item['alpa'] }}
                                </td>

                                <!-- Total Skor Poin -->
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <span style="
                                        display: inline-block;
                                        padding: 0.2rem 0.75rem;
                                        border-radius: 9999px;
                                        font-weight: 900;
                                        font-size: 0.85rem;
                                        background: {{ $item['skor'] >= 0 ? '#ecfdf5' : '#fef2f2' }};
                                        color: {{ $item['skor'] >= 0 ? '#047857' : '#b91c1c' }};
                                        border: 1px solid {{ $item['skor'] >= 0 ? '#a7f3d0' : '#fecaca' }};
                                    ">
                                        {{ $item['skor'] }}
                                    </span>
                                </td>

                                <!-- Persentase Kehadiran -->
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 800; color: #0284c7;">
                                    {{ $item['persentase'] }}%
                                </td>

                                <!-- Predikat Badge -->
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    <span style="
                                        display: inline-block;
                                        padding: 0.2rem 0.65rem;
                                        border-radius: 0.375rem;
                                        font-size: 0.75rem;
                                        font-weight: 700;
                                        background: {{ $item['badge_color'] }}15;
                                        color: {{ $item['badge_color'] }};
                                        border: 1px solid {{ $item['badge_color'] }}30;
                                    ">
                                        {{ $item['badge'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="padding: 2.5rem; text-align: center; color: #9ca3af;">
                                    Tidak ada data kedisiplinan yang ditemukan untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</x-filament-panels::page>
