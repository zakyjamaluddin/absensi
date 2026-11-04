@php
    $periode = $get('modal_periode') ?? 'bulan_ini';
    $dari = $get('modal_dari');
    $sampai = $get('modal_sampai');

    $stats = \App\Models\Absensi::getStatsForSubject($record, $periode, $dari, $sampai);
    $records = $stats['records'] ?? collect();
    $isSiswa = ($record instanceof \App\Models\Siswa);
@endphp

<div style="display: flex; flex-direction: column; gap: 1.25rem; width: 100%;">

    <!-- 1. KARTU HEADER PROFIL -->
    <div style="
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.04) 100%);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 0.85rem;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    " class="dark:!bg-emerald-950/20 dark:!border-emerald-800/40">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <!-- Foto Avatar -->
            <div style="
                width: 4rem;
                height: 4rem;
                border-radius: 9999px;
                overflow: hidden;
                border: 2.5px solid #10b981;
                box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
                background: #f1f5f9;
                flex-shrink: 0;
            ">
                <img
                    src="{{ !empty($record->foto_url) ? $record->foto_url : asset('images/default-avatar.svg') }}"
                    alt="{{ $record->nama }}"
                    style="width: 100%; height: 100%; object-fit: cover;"
                    onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.svg') }}';"
                />
            </div>

            <div>
                <h3 style="margin: 0 0 0.25rem 0; font-size: 1.2rem; font-weight: 800; color: #0f172a;" class="dark:!text-white">
                    {{ $record->nama }}
                </h3>
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="
                        padding: 0.15rem 0.5rem;
                        border-radius: 0.375rem;
                        font-size: 0.75rem;
                        font-weight: 700;
                        background: #ecfdf5;
                        color: #047857;
                        border: 1px solid #a7f3d0;
                    " class="dark:!bg-emerald-950 dark:!text-emerald-300">
                        {{ $isSiswa ? ($record->kelas?->nama_kelas ?? 'Tanpa Kelas') : 'Ustadz / Guru' }}
                    </span>
                    <span style="font-size: 0.8rem; color: #64748b; font-family: monospace;" class="dark:!text-slate-400">
                        {{ $isSiswa ? 'NIS: ' . ($record->nis ?? '-') : 'NIP: ' . ($record->nip ?? '-') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Persentase Kehadiran Badge -->
        <div style="
            text-align: right;
            background: #ffffff;
            padding: 0.65rem 1rem;
            border-radius: 0.625rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        " class="dark:!bg-slate-800 dark:!border-slate-700">
            <div style="font-size: 1.35rem; font-weight: 800; color: #10b981;">
                {{ $stats['persentase'] }}%
            </div>
            <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase;">
                Tingkat Kehadiran
            </div>
        </div>
    </div>

    <!-- 2. 5 KARTU STATISTIK KEHADIRAN (GRID ELEGAN) -->
    <div style="
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 0.75rem;
    ">
        <!-- 1. Hadir Tepat Waktu -->
        <div style="
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.625rem;
            padding: 0.85rem 0.75rem;
            text-align: center;
        " class="dark:!bg-emerald-950/40 dark:!border-emerald-800/40">
            <div style="font-size: 1.5rem; font-weight: 800; color: #15803d;" class="dark:!text-emerald-300">
                {{ $stats['tepat_waktu'] }}
            </div>
            <div style="font-size: 0.725rem; font-weight: 700; color: #166534; text-transform: uppercase; margin-top: 0.15rem;" class="dark:!text-emerald-400">
                Tepat Waktu
            </div>
        </div>

        <!-- 2. Terlambat -->
        <div style="
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 0.625rem;
            padding: 0.85rem 0.75rem;
            text-align: center;
        " class="dark:!bg-amber-950/40 dark:!border-amber-800/40">
            <div style="font-size: 1.5rem; font-weight: 800; color: #b45309;" class="dark:!text-amber-300">
                {{ $stats['terlambat'] }}
            </div>
            <div style="font-size: 0.725rem; font-weight: 700; color: #92400e; text-transform: uppercase; margin-top: 0.15rem;" class="dark:!text-amber-400">
                Terlambat
            </div>
        </div>

        <!-- 3. Sakit -->
        <div style="
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.625rem;
            padding: 0.85rem 0.75rem;
            text-align: center;
        " class="dark:!bg-blue-950/40 dark:!border-blue-800/40">
            <div style="font-size: 1.5rem; font-weight: 800; color: #1d4ed8;" class="dark:!text-blue-300">
                {{ $stats['sakit'] }}
            </div>
            <div style="font-size: 0.725rem; font-weight: 700; color: #1e40af; text-transform: uppercase; margin-top: 0.15rem;" class="dark:!text-blue-400">
                Sakit (S)
            </div>
        </div>

        <!-- 4. Izin -->
        <div style="
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.625rem;
            padding: 0.85rem 0.75rem;
            text-align: center;
        " class="dark:!bg-slate-800 dark:!border-slate-700">
            <div style="font-size: 1.5rem; font-weight: 800; color: #475569;" class="dark:!text-slate-300">
                {{ $stats['izin'] }}
            </div>
            <div style="font-size: 0.725rem; font-weight: 700; color: #334155; text-transform: uppercase; margin-top: 0.15rem;" class="dark:!text-slate-400">
                Izin (I)
            </div>
        </div>

        <!-- 5. Alpa -->
        <div style="
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.625rem;
            padding: 0.85rem 0.75rem;
            text-align: center;
        " class="dark:!bg-rose-950/40 dark:!border-rose-800/40">
            <div style="font-size: 1.5rem; font-weight: 800; color: #b91c1c;" class="dark:!text-rose-300">
                {{ $stats['alpa'] }}
            </div>
            <div style="font-size: 0.725rem; font-weight: 700; color: #9f1239; text-transform: uppercase; margin-top: 0.15rem;" class="dark:!text-rose-400">
                Alpa (A)
            </div>
        </div>
    </div>

    <!-- 3. TABEL RIWAYAT LOG PRESENSI TERAKHIR -->
    <div style="
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
    " class="dark:!bg-slate-900 dark:!border-slate-800">
        <div style="
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.825rem;
            font-weight: 700;
            color: #374151;
            display: flex;
            justify-content: space-between;
            align-items: center;
        " class="dark:!bg-slate-800 dark:!border-slate-700 dark:!text-slate-200">
            <span>📋 Riwayat Presensi ({{ $records->count() }} Data Ditemukan)</span>
            <span style="font-size: 0.75rem; color: #6b7280;">Urutan tanggal terbaru</span>
        </div>

        <div style="max-height: 240px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.825rem;">
                <thead style="position: sticky; top: 0; background: #ffffff; z-index: 1;" class="dark:!bg-slate-900">
                    <tr style="border-bottom: 1px solid #e5e7eb; color: #64748b;" class="dark:!border-slate-800 dark:!text-slate-400">
                        <th style="padding: 0.6rem 0.85rem;">Tanggal</th>
                        <th style="padding: 0.6rem 0.85rem; text-align: center;">Status Masuk</th>
                        <th style="padding: 0.6rem 0.85rem; text-align: center;">Jam Masuk</th>
                        <th style="padding: 0.6rem 0.85rem; text-align: center;">Jam Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        @php
                            $badgeColor = match($rec->status_masuk) {
                                'Tepat Waktu' => 'background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;',
                                'Terlambat' => 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;',
                                'Sakit' => 'background: #e0f2fe; color: #075985; border: 1px solid #bae6fd;',
                                'Izin' => 'background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;',
                                'Alpa' => 'background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3;',
                                default => 'background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb;',
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9;" class="dark:!border-slate-800">
                            <td style="padding: 0.55rem 0.85rem; font-weight: 600; color: #1e293b;" class="dark:!text-slate-200">
                                {{ \Carbon\Carbon::parse($rec->tanggal)->translatedFormat('l, d F Y') }}
                            </td>
                            <td style="padding: 0.55rem 0.85rem; text-align: center;">
                                <span style="
                                    display: inline-block;
                                    padding: 0.15rem 0.6rem;
                                    border-radius: 9999px;
                                    font-size: 0.725rem;
                                    font-weight: 700;
                                    {{ $badgeColor }}
                                ">
                                    {{ $rec->status_masuk ?? '-' }}
                                </span>
                            </td>
                            <td style="padding: 0.55rem 0.85rem; text-align: center; font-family: monospace; color: #475569;" class="dark:!text-slate-300">
                                {{ $rec->jam_masuk ? substr($rec->jam_masuk, 0, 5) : '--:--' }}
                            </td>
                            <td style="padding: 0.55rem 0.85rem; text-align: center; font-family: monospace; color: #475569;" class="dark:!text-slate-300">
                                {{ $rec->jam_pulang ? substr($rec->jam_pulang, 0, 5) : '--:--' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 1.5rem; text-align: center; color: #9ca3af;">
                                Tidak ada rekaman presensi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
