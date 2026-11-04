<x-filament-panels::page>
    @php
        $leaderboard = $this->getLeaderboardData();
        $top1 = $leaderboard[0] ?? null;
        $top2 = $leaderboard[1] ?? null;
        $top3 = $leaderboard[2] ?? null;
        $kelases = $this->getKelases();
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- FILTER BAR (SOLID WHITE OPACITY 1) -->
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
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        " class="dark:!bg-slate-900 dark:!border-slate-800">
            <!-- Filter Periode (Dropdown Select) -->
            <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                <label style="font-size: 0.875rem; font-weight: 700; color: #4b5563;" class="dark:!text-slate-300">
                    📅 Periode:
                </label>
                <select
                    wire:model.live="periode"
                    style="
                        padding: 0.5rem 1rem;
                        border-radius: 0.5rem;
                        font-size: 0.875rem;
                        font-weight: 600;
                        border: 1px solid #d1d5db;
                        background: #ffffff;
                        color: #1f2937;
                        cursor: pointer;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                    "
                    class="dark:!bg-slate-800 dark:!text-white dark:!border-slate-700"
                >
                    <option value="bulan_ini">Bulan Ini ({{ now()->translatedFormat('F Y') }})</option>
                    <option value="tahun_ini">Tahun Ini ({{ now()->year }})</option>
                    <option value="all">Semua Waktu</option>
                    <option value="custom">Custom (Pilih Rentang Tanggal)</option>
                </select>

                <!-- Input Rentang Tanggal Kalender (Jika Pilih Custom) -->
                @if($periode === 'custom')
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <input
                            type="date"
                            wire:model.live="startDate"
                            style="
                                padding: 0.45rem 0.75rem;
                                border-radius: 0.375rem;
                                border: 1px solid #10b981;
                                font-size: 0.85rem;
                                font-weight: 600;
                            "
                            class="dark:!bg-slate-800 dark:!text-white"
                        />
                        <span style="font-size: 0.85rem; color: #6b7280; font-weight: 600;">s/d</span>
                        <input
                            type="date"
                            wire:model.live="endDate"
                            style="
                                padding: 0.45rem 0.75rem;
                                border-radius: 0.375rem;
                                border: 1px solid #10b981;
                                font-size: 0.85rem;
                                font-weight: 600;
                            "
                            class="dark:!bg-slate-800 dark:!text-white"
                        />
                    </div>
                @endif
            </div>

            <!-- Filter Kelas (Dropdown) -->
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label style="font-size: 0.875rem; font-weight: 700; color: #4b5563;">Kelas:</label>
                <select
                    wire:model.live="kelasId"
                    style="
                        padding: 0.5rem 1rem;
                        border-radius: 0.375rem;
                        border: 1px solid #d1d5db;
                        font-size: 0.875rem;
                        font-weight: 600;
                        color: #374151;
                        background: white;
                    "
                >
                    <option value="all">Semua Kelas</option>
                    @foreach($kelases as $id => $namaKelas)
                        <option value="{{ $id }}">{{ $namaKelas }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- PODIUM TOP 3 JUARA LITERASI -->
        @if(count($leaderboard) > 0 && ($top1['total_skor'] > 0 || $top2['total_skor'] ?? 0 > 0))
            <div style="
                background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
                border: 1px solid #cbd5e1;
                border-radius: 1rem;
                padding: 2rem 1rem 1rem;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                gap: 1rem;
                flex-wrap: wrap;
            ">
                <!-- JUARA 2 (PERAK) -->
                @if($top2 && $top2['total_skor'] > 0)
                    <div style="
                        flex: 1;
                        min-width: 200px;
                        max-width: 240px;
                        background: #ffffff;
                        border-radius: 0.75rem 0.75rem 0 0;
                        border: 2px solid #94a3b8;
                        border-bottom: none;
                        padding: 1.25rem 1rem;
                        text-align: center;
                        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
                        height: 230px;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                    ">
                        <div>
                            <div style="font-size: 2rem; margin-bottom: 0.25rem;">🥈</div>
                            <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase;">Juara 2</div>
                            <div style="font-size: 1.1rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">{{ $top2['nama'] }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">{{ $top2['kelas'] }}</div>
                        </div>
                        <div style="background: #f1f5f9; padding: 0.5rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                            <div style="font-size: 1.25rem; font-weight: 900; color: #334155;">{{ $top2['total_skor'] }} Poin</div>
                            <div style="font-size: 0.7rem; color: #64748b;">{{ $top2['total_kunjungan'] }} Kunjungan • {{ $top2['total_pinjam'] }} Pinjam</div>
                        </div>
                    </div>
                @endif

                <!-- JUARA 1 (EMAS - PALING TINGGI) -->
                @if($top1 && $top1['total_skor'] > 0)
                    <div style="
                        flex: 1;
                        min-width: 220px;
                        max-width: 260px;
                        background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
                        border-radius: 1rem 1rem 0 0;
                        border: 3px solid #f59e0b;
                        border-bottom: none;
                        padding: 1.5rem 1rem;
                        text-align: center;
                        box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.2);
                        height: 270px;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        position: relative;
                    ">
                        <div style="position: absolute; top: -18px; left: 50%; transform: translateX(-50%); font-size: 2.25rem;">
                            👑
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <div style="font-size: 2.25rem; margin-bottom: 0.25rem;">🥇</div>
                            <div style="font-size: 0.8rem; font-weight: 900; color: #b45309; text-transform: uppercase;">Juara 1 • Duta Literasi</div>
                            <div style="font-size: 1.25rem; font-weight: 900; color: #78350f; margin-top: 0.25rem;">{{ $top1['nama'] }}</div>
                            <div style="font-size: 0.85rem; color: #92400e; font-weight: 600;">{{ $top1['kelas'] }}</div>
                        </div>
                        <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 0.5rem; border-radius: 0.5rem;">
                            <div style="font-size: 1.5rem; font-weight: 900; color: #92400e;">★ {{ $top1['total_skor'] }} Poin</div>
                            <div style="font-size: 0.75rem; color: #b45309; font-weight: 600;">{{ $top1['total_kunjungan'] }} Kunjungan • {{ $top1['total_pinjam'] }} Pinjam</div>
                        </div>
                    </div>
                @endif

                <!-- JUARA 3 (PERUNGGU) -->
                @if($top3 && $top3['total_skor'] > 0)
                    <div style="
                        flex: 1;
                        min-width: 200px;
                        max-width: 240px;
                        background: #ffffff;
                        border-radius: 0.75rem 0.75rem 0 0;
                        border: 2px solid #b45309;
                        border-bottom: none;
                        padding: 1.25rem 1rem;
                        text-align: center;
                        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
                        height: 200px;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                    ">
                        <div>
                            <div style="font-size: 1.75rem; margin-bottom: 0.25rem;">🥉</div>
                            <div style="font-size: 0.75rem; font-weight: 800; color: #92400e; text-transform: uppercase;">Juara 3</div>
                            <div style="font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">{{ $top3['nama'] }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">{{ $top3['kelas'] }}</div>
                        </div>
                        <div style="background: #fffbeb; padding: 0.4rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                            <div style="font-size: 1.15rem; font-weight: 900; color: #78350f;">{{ $top3['total_skor'] }} Poin</div>
                            <div style="font-size: 0.7rem; color: #92400e;">{{ $top3['total_kunjungan'] }} Kunjungan • {{ $top3['total_pinjam'] }} Pinjam</div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- TABEL LENGKAP LEADERBOARD -->
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span>🏆</span>
                        <span>Daftar Peringkat Minat Literasi</span>
                    </div>
                    <span style="font-size: 0.875rem; color: #6b7280; font-weight: normal;">
                        Total Siswa: {{ count($leaderboard) }}
                    </span>
                </div>
            </x-slot>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e5e7eb; color: #6b7280; text-transform: uppercase; font-size: 0.75rem;">
                            <th style="padding: 0.75rem; text-align: center; width: 80px;">Rank</th>
                            <th style="padding: 0.75rem;">Nama Siswa</th>
                            <th style="padding: 0.75rem;">Kelas</th>
                            <th style="padding: 0.75rem; text-align: center;">Kunjungan</th>
                            <th style="padding: 0.75rem; text-align: center;">Buku Dipinjam</th>
                            <th style="padding: 0.75rem; text-align: center;">Predikat</th>
                            <th style="padding: 0.75rem; text-align: right; width: 130px;">Total Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaderboard as $row)
                            <tr style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.15s;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                                <!-- RANK -->
                                <td style="padding: 0.875rem 0.75rem; text-align: center;">
                                    @if($row['rank'] === 1 && $row['total_skor'] > 0)
                                        <span style="font-size: 1.25rem;">🥇</span>
                                    @elseif($row['rank'] === 2 && $row['total_skor'] > 0)
                                        <span style="font-size: 1.25rem;">🥈</span>
                                    @elseif($row['rank'] === 3 && $row['total_skor'] > 0)
                                        <span style="font-size: 1.25rem;">🥉</span>
                                    @else
                                        <span style="
                                            display: inline-block;
                                            width: 28px;
                                            height: 28px;
                                            line-height: 28px;
                                            background: #f1f5f9;
                                            border-radius: 9999px;
                                            font-weight: 700;
                                            color: #475569;
                                        ">
                                            {{ $row['rank'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- NAMA SISWA -->
                                <td style="padding: 0.875rem 0.75rem;">
                                    <div style="font-weight: 700; color: #111827;">{{ $row['nama'] }}</div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">NIS: {{ $row['nis'] }}</div>
                                </td>

                                <!-- KELAS -->
                                <td style="padding: 0.875rem 0.75rem; font-weight: 600; color: #374151;">
                                    {{ $row['kelas'] }}
                                </td>

                                <!-- KUNJUNGAN -->
                                <td style="padding: 0.875rem 0.75rem; text-align: center;">
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 0.25rem 0.625rem; border-radius: 9999px; font-weight: 700;">
                                        {{ $row['total_kunjungan'] }}x
                                    </span>
                                </td>

                                <!-- BUKU DIPINJAM -->
                                <td style="padding: 0.875rem 0.75rem; text-align: center;">
                                    <span style="background: #f3e8ff; color: #7e22ce; padding: 0.25rem 0.625rem; border-radius: 9999px; font-weight: 700;">
                                        {{ $row['total_pinjam'] }} buku
                                    </span>
                                </td>

                                <!-- PREDIKAT / BADGE -->
                                <td style="padding: 0.875rem 0.75rem; text-align: center;">
                                    <span style="
                                        display: inline-block;
                                        padding: 0.25rem 0.625rem;
                                        border-radius: 0.375rem;
                                        font-size: 0.75rem;
                                        font-weight: 700;
                                        color: {{ $row['badge_color'] }};
                                        background: {{ $row['badge_color'] }}15;
                                        border: 1px solid {{ $row['badge_color'] }}40;
                                    ">
                                        {{ $row['badge'] }}
                                    </span>
                                </td>

                                <!-- TOTAL SKOR -->
                                <td style="padding: 0.875rem 0.75rem; text-align: right;">
                                    <span style="
                                        font-size: 1.125rem;
                                        font-weight: 900;
                                        color: {{ $row['total_skor'] > 0 ? '#059669' : '#9ca3af' }};
                                    ">
                                        {{ $row['total_skor'] }} Pts
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 2rem; text-align: center; color: #6b7280;">
                                    Belum ada data aktivitas perpustakaan pada periode yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- KETERANGAN SISTEM SCORING -->
            <div style="
                margin-top: 1.5rem;
                padding: 1rem;
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                border-radius: 0.5rem;
                font-size: 0.8125rem;
                color: #166534;
            ">
                <div style="font-weight: 700; margin-bottom: 0.25rem;">💡 Cara Perhitungan Skor Literasi:</div>
                <div>
                    • Setiap <strong>Kunjungan Perpustakaan</strong> yang dicatat = <strong>+{{ \App\Models\PerpustakaanSetting::getSetting()->poin_kunjungan }} Poin</strong><br>
                    • Setiap <strong>Buku yang Dipinjam</strong> = <strong>+{{ \App\Models\PerpustakaanSetting::getSetting()->poin_peminjaman }} Poin</strong><br>
                    <em>Bobot poin dan batas hari pinjam dapat diubah kapan saja melalui menu <strong>Pengaturan Perpustakaan</strong>.</em>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
