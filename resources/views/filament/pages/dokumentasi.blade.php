<x-filament-panels::page>
    <!-- LOGIKA SAKTI ALPINE.JS: Pencarian Instan, Filter Kategori & Interaktivitas -->
    <div x-data="{
        search: '',
        activeTab: 'all',
        match(keywords) {
            if (!this.search.trim()) return true;
            const q = this.search.toLowerCase().trim();
            const words = q.split(' ');
            const target = keywords.toLowerCase();
            return words.every(w => target.includes(w));
        }
    }">

        <!-- ==============================================================
             1. BANNER HERO DOKUMENTASI SISTEM ABSAKA
             ============================================================== -->
        <div style="
            background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #10b981 100%);
            border-radius: 1rem;
            padding: 2rem 1.75rem;
            color: #ffffff;
            margin-bottom: 1.75rem;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.25);
            position: relative;
            overflow: hidden;
        " class="no-print">
            <div style="position: absolute; right: -25px; bottom: -35px; opacity: 0.12; pointer-events: none;">
                <svg style="width: 260px; height: 260px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>

            <div style="position: relative; z-index: 2; max-width: 820px;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.775rem; font-weight: 700; margin-bottom: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.3);">
                    <span style="display: inline-block; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #34d399;"></span>
                    <span>Pusat Pengetahuan & Panduan Operasional Resmi Absaka v2.5</span>
                </div>
                <h1 style="font-size: 1.85rem; font-weight: 800; line-height: 1.25; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">
                    Buku Panduan & Dokumentasi Sistem Absaka
                </h1>
                <p style="font-size: 0.95rem; line-height: 1.5; color: rgba(255, 255, 255, 0.92); margin: 0;">
                    Panduan operasional komprehensif untuk mengelola absensi santri/siswa & guru berbasis RFID & QR Code, modul perpustakaan digital terintegrasi, rekapitulasi data kelas, otomasi pesan WhatsApp Gateway, serta pemasangan aplikasi PWA pada desktop dan perangkat mobile.
                </p>
            </div>
        </div>

        <!-- ==============================================================
             2. BAR PENCARIAN INSTAN
             ============================================================== -->
        <div style="margin-bottom: 1.25rem;" class="no-print">
            <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                <x-filament::input
                    type="text"
                    placeholder="Cari topik bantuan (misal: RFID, WhatsApp, Alpa, Perpustakaan, PWA, Import Siswa, Denda, Excel)..."
                    x-model="search"
                    style="padding: 0.75rem 1rem; font-size: 0.95rem;"
                />
            </x-filament::input.wrapper>
        </div>

        <!-- ==============================================================
             3. TAB KATEGORI TOPIK PANDUAN
             ============================================================== -->
        <div style="
            display: flex;
            gap: 0.35rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1.75rem;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 0.25rem;
        " class="no-print dark:!border-slate-800">
            <button class="doc-tab-btn" @click="activeTab = 'all'" :class="{ 'doc-tab-btn-active': activeTab === 'all' }">
                <span>📚 Semua Topik</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'starter'" :class="{ 'doc-tab-btn-active': activeTab === 'starter' }">
                <span>🚀 Starter Kit & Setup</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'absensi'" :class="{ 'doc-tab-btn-active': activeTab === 'absensi' }">
                <span>📇 Scanner & Absensi</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'perpus'" :class="{ 'doc-tab-btn-active': activeTab === 'perpus' }">
                <span>📖 Perpustakaan Digital</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'master'" :class="{ 'doc-tab-btn-active': activeTab === 'master' }">
                <span>🏫 Siswa, Guru & Jadwal</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'rekap'" :class="{ 'doc-tab-btn-active': activeTab === 'rekap' }">
                <span>📊 Rekap & Ekspor Excel</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'wa'" :class="{ 'doc-tab-btn-active': activeTab === 'wa' }">
                <span>📲 WhatsApp & Otomasi</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'pwa'" :class="{ 'doc-tab-btn-active': activeTab === 'pwa' }">
                <span>📱 Aplikasi PWA (Install)</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'security'" :class="{ 'doc-tab-btn-active': activeTab === 'security' }">
                <span>🔐 Hak Akses (8 Role)</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'google'" :class="{ 'doc-tab-btn-active': activeTab === 'google' }">
                <span>🔑 Sign In with Google</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'izin'" :class="{ 'doc-tab-btn-active': activeTab === 'izin' }">
                <span>✉️ Ajukan Izin & Guru Piket</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'rating'" :class="{ 'doc-tab-btn-active': activeTab === 'rating' }">
                <span>🏆 Rating & Gamifikasi</span>
            </button>
            <button class="doc-tab-btn" @click="activeTab = 'notif'" :class="{ 'doc-tab-btn-active': activeTab === 'notif' }">
                <span>🔔 Notifikasi & Libur Nasional</span>
            </button>
        </div>

        <!-- ==============================================================
             4. KUMPULAN MATERI DOKUMENTASI (KARTU LENGKAP & HARDCODED)
             ============================================================== -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- ==========================================
                 TOPIK 1: STARTER KIT & INISIALISASI
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'starter') && match('starter kit inisialisasi urutan konfigurasi langkah mulai pertama kali setup jam guru kelas siswa wa sidobe perpus')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">1</span>
                            <span>Starter Kit: Urutan Inisialisasi & Persiapan Sistem Baru</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Selamat Datang di <strong>Absaka (Sistem Manajemen Absensi & Perpustakaan Terpadu)</strong>. Untuk memastikan semua alur otomatisasi, notifikasi WhatsApp, hak akses wali kelas, dan modul sirkulasi perpustakaan berjalan harmonis, silakan ikuti <strong>8 langkah inisialisasi berurutan</strong> berikut:</p>

                        <div class="step-list">
                            <div class="step-item">
                                <div class="step-num">1</div>
                                <div class="step-body">
                                    <strong>Konfigurasi Pengaturan Jam Operasional Absensi</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Pengaturan Jam</em>. Tentukan jendela jam mulai dan selesai absensi masuk, batas toleransi keterlambatan (misal pukul 07:00), jam buka/tutup absensi pulang, serta jadwal jam operasional khusus untuk hari Jumat.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">2</div>
                                <div class="step-body">
                                    <strong>Daftarkan Kalender Libur Lembaga & Nasional</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Kalender Libur</em>. Masukkan tanggal cuti bersama, libur semester, atau hari besar Islam/nasional. Tanggal yang didaftarkan di sini otomatis membuat mesin cron job melompati proses Alpa harian.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">3</div>
                                <div class="step-body">
                                    <strong>Daftarkan Data Guru / Ustadz</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Data Guru</em>. Tambahkan data ustadz beserta NIP dan Nomor HP/WhatsApp aktif (format diawali angka 0 atau 62, contoh: <code class="code-inline">081234567890</code>). Nomor ini digunakan untuk notifikasi laporan rekapitulasi.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">4</div>
                                <div class="step-body">
                                    <strong>Daftarkan Data Kelas & Tunjuk Wali Kelas</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Data Kelas</em>. Tambahkan nama kelas (misal: <em>Kelas 10-A</em>, <em>Kelas 7 Putra</em>) dan pilih salah satu guru yang bertindak sebagai Wali Kelas.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">5</div>
                                <div class="step-body">
                                    <strong>Pendaftaran & Import Masal Data Siswa / Santri</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Data Siswa</em>. Tambahkan siswa secara manual atau gunakan tombol <strong>"Import Siswa"</strong> dengan mengunggah berkas Excel/CSV. Lengkapi juga nomor WhatsApp orang tua/wali santri agar notifikasi kedatangan santri otomatis terkirim.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">6</div>
                                <div class="step-body">
                                    <strong>Daftarkan Akun Pengguna & Hak Akses (Filament Shield)</strong>
                                    <p>Masuk ke menu <em class="highlight-text">User</em> di bawah grup Manajemen Sistem. Buat akun login baru untuk wali kelas atau staf: pilih Tipe Profil "Guru", hubungkan ke profil guru yang bersangkutan, lalu centang peran akses <strong>"Wali Kelas"</strong>. Akun ini secara otomatis hanya bisa melihat santri di kelasnya sendiri.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">7</div>
                                <div class="step-body">
                                    <strong>Integrasi Gerbang WhatsApp (Sidobe API)</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Integrasi WA</em>. Tempelkan API Token Sidobe Anda, simpan, lalu klik tombol <strong>"Tes Koneksi WA"</strong> untuk memvalidasi saldo dan status perangkat WhatsApp yang terhubung.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">8</div>
                                <div class="step-body">
                                    <strong>Konfigurasi Pengaturan Perpustakaan Digital</strong>
                                    <p>Masuk ke menu <em class="highlight-text">Pengaturan Perpustakaan</em>. Atur durasi standar peminjaman buku (default: 7 hari), batas kuota peminjaman per santri, tarif denda keterlambatan per hari, dan bonus poin literasi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 2: ATURAN IMPORT DATA SISWA EXCEL/CSV
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'master') && match('import data siswa excel csv format kolom kelas nis nama wali foto')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">2</span>
                            <span>Aturan Format & Sinkronisasi Import Data Siswa (Excel/CSV)</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Fitur <strong>Import Siswa</strong> pada Absaka dirancang sangat fleksibel dan aman untuk mencegah inkonsistensi relasi data antar kelas. Mohon perhatikan spesifikasi berikut sebelum mengunggah file:</p>

                        <div class="table-container">
                            <table class="doc-table">
                                <thead>
                                    <tr>
                                        <th>Nama Kolom Header</th>
                                        <th>Status</th>
                                        <th>Format / Contoh Nilai</th>
                                        <th>Keterangan Penting</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code class="code-inline">nis</code></td>
                                        <td><span class="badge-required">Wajib</span></td>
                                        <td><code class="code-inline">20260001</code> / Huruf & Angka</td>
                                        <td>Nomor induk santri unik. Nilai ini juga menjadi barcode/kode RFID santri.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">nama</code></td>
                                        <td><span class="badge-required">Wajib</span></td>
                                        <td>Muhammad Al-Fatih</td>
                                        <td>Nama lengkap siswa santri.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">kelas</code></td>
                                        <td><span class="badge-required">Wajib</span></td>
                                        <td>Kelas 10-A (Ula)</td>
                                        <td><strong>Harus sama persis</strong> dengan Nama Kelas yang telah didaftarkan di menu Data Kelas (Bukan ID angka).</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">no_hp_wali</code></td>
                                        <td><span class="badge-optional">Opsional</span></td>
                                        <td><code class="code-inline">081234567890</code></td>
                                        <td>Nomor WhatsApp orang tua/wali santri untuk pengiriman notifikasi otomatis.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">nama_wali</code></td>
                                        <td><span class="badge-optional">Opsional</span></td>
                                        <td>H. Abdullah</td>
                                        <td>Nama orang tua atau wali santri.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert-box alert-info" style="margin-top: 1rem;">
                            <strong>🛡️ Penanganan Baris Gagal (Row-Level Validation):</strong><br>
                            Jika terdapat salah satu baris siswa yang nama kelasnya salah ketik (misal: "Kelas 10-Z" padahal belum didaftarkan di Data Kelas), sistem Filament <strong>hanya akan menggagalkan baris tersebut saja</strong>. Ratusan baris siswa lainnya yang valid akan tetap berhasil masuk ke database, dan laporan baris gagal akan ditampilkan secara transparan di notifikasi.
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 3: MESIN SCANNER ABSENSI HIBRIDA
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'absensi') && match('mesin scanner absensi rfid kamera qr hybrid desktop mobile suara chime')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">3</span>
                            <span>Panduan Operasional Mesin Scanner Absensi Hibrida</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Menu <strong>Scanner Absensi</strong> merupakan garda depan presensi santri dan ustadz. Halaman ini dirancang menggunakan arsitektur hibrida adaptif yang mendukung perangkat keras fisik maupun pemindaian kamera:</p>

                        <div class="grid-2-col">
                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
                                    <span>💳</span> Mode Kartu / RFID (Rekomendasi Operasional)
                                </h4>
                                <p style="font-size: 0.85rem; line-height: 1.5; color: #4b5563; margin-bottom: 0.75rem;">
                                    Dirancang untuk kecepatan tinggi menggunakan alat pemindai fisik seperti <strong>USB RFID Reader 13.56MHz/125KHz</strong> atau <strong>USB Barcode Laser Scanner</strong>.
                                </p>
                                <ul style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0;">
                                    <li><strong>Autofokus Permanen:</strong> Kursor otomatis terkunci di form input scanner meskipun layar disentuh/diklik. Petugas tidak perlu bolak-balik mengarahkan kursor.</li>
                                    <li><strong>Kecepatan Pemrosesan:</strong> Memproses input santri di bawah 200 milidetik tanpa me-refresh seluruh halaman.</li>
                                </ul>
                            </div>

                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #2563eb; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 0.35rem;">
                                    <span>📷</span> Mode Kamera QR (Webcam & Smartphone)
                                </h4>
                                <p style="font-size: 0.85rem; line-height: 1.5; color: #4b5563; margin-bottom: 0.75rem;">
                                    Dirancang untuk presensi fleksibel menggunakan kamera internal laptop atau kamera ponsel smartphone wali kelas / guru piket.
                                </p>
                                <ul style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0;">
                                    <li><strong>Smart Switch:</strong> Klik tombol "Mode Kamera QR" di atas scanner untuk menyalakan lensa kamera secara instan.</li>
                                    <li><strong>Dukungan Multi-Kamera:</strong> Memungkinkan pergantian antara kamera depan (selfie) dan kamera belakang (rear) pada smartphone.</li>
                                    <li><strong>Hemat Baterai:</strong> Kamera otomatis dimatikan secara total saat beralih kembali ke mode RFID demi privasi dan efisiensi daya.</li>
                                </ul>
                            </div>
                        </div>

                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 1.5rem 0 0.5rem 0;" class="dark:!text-white">
                            📱 Tata Letak Cerdas: Desktop 2-Kolom vs Mobile 1-Kolom
                        </h4>
                        <p>Halaman scanner mendeteksi lebar perangkat secara presisi:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem;">
                            <li><strong>Tampilan Desktop / Layar Lebar (&ge; 768px):</strong> Terbagi rapi menjadi <strong>2 kolom sejajar (50% : 50%)</strong>. Kolom kiri untuk alat scan, kolom kanan menampilkan <strong>Card Alert Siswa</strong> dengan foto siswa ukuran besar (<code class="code-inline">9.5rem</code>), nama lengkap, NIS/NIP, kelas, jam presensi, dan status kehadiran. Saat standby, kolom kanan menampilkan kartu siaga hijau emerald.</li>
                            <li><strong>Tampilan Ponsel / Mobile (&lt; 768px):</strong> Menjadi <strong>1 kolom vertikal</strong> ringkas. Card alert hanya akan muncul setelah ada kartu yang discan, sehingga layar HP tidak tertutup oleh kartu standby kosong.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 4: WAKTU, TERLAMBAT, COOLDOWN & AUDIO
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'absensi') && match('batas waktu terlambat toleransi cooldown audio success failed mpeg jumat')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">4</span>
                            <span>Aturan Validasi Waktu, Toleransi Keterlambatan, Cooldown & Audio Feedback</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka menerapkan sistem validasi waktu presisi untuk menentukan integritas data kehadiran:</p>

                        <div class="grid-2-col">
                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 0.95rem; font-weight: 700;">
                                    ⏱️ Logika Jendela Waktu Masuk & Pulang
                                </h4>
                                <ul style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    <li><strong>Tepat Waktu:</strong> Scan masuk dilakukan sebelum atau tepat pada jam batas toleransi (misal: 06:00 - 07:00). Berstatus <strong style="color: #059669;">Tepat Waktu</strong>.</li>
                                    <li><strong>Terlambat:</strong> Scan masuk melewati jam batas toleransi sampai jam tutup masuk (misal: 07:01 - 10:00). Berstatus <strong style="color: #d97706;">Terlambat</strong>.</li>
                                    <li><strong>Scan Pulang:</strong> Dilakukan pada jendela jam pulang yang diatur di Pengaturan Jam. Mencatat jam kepulangan santri secara akurat.</li>
                                </ul>
                            </div>

                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 0.95rem; font-weight: 700;">
                                    🔊 Sistem Audio Feedback Multi-Platform
                                </h4>
                                <ul style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    <li><strong>Audio Success (<code class="code-inline">success.mpeg</code>):</strong> Nada melodi ceria yang diputar seketika saat presensi masuk atau pulang berhasil dicatat.</li>
                                    <li><strong>Audio Failed (<code class="code-inline">failed.mpeg</code>):</strong> Nada peringatan yang diputar jika kartu belum terdaftar, santri sudah absen sebelumnya (dobel absen), atau di luar jendela waktu.</li>
                                    <li><strong>Fitur Cooldown 3 Detik:</strong> Mencegah spam kartu yang sama saat kartu diletakkan terlalu lama di atas reader.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 5: MODUL PERPUSTAKAAN DIGITAL
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'perpus') && match('perpustakaan digital modul buku pinjam kembali sirkulasi kunjungan denda jatuh tempo dasbor literasi')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">5</span>
                            <span>Modul Perpustakaan Digital: Dasbor, Sirkulasi & Gamifikasi Literasi</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka dilengkapi dengan modul <strong>Perpustakaan Digital Mandiri</strong> yang terintegrasi penuh dengan database siswa, kartu santri fisik, dan sistem scanner:</p>

                        <div class="step-list">
                            <div class="step-item">
                                <div class="step-num">A</div>
                                <div class="step-body">
                                    <strong>Dasbor Khusus Perpustakaan (<code class="code-inline">/admin/dashboard-perpustakaan</code>)</strong>
                                    <p>Halaman pemantauan visual khusus petugas perpustakaan yang menyajikan ucapan salam islami, 3 kartu statistik harian (<em>Siswa Berkunjung Hari Ini</em>, <em>Buku Terpinjam Hari Ini</em>, <em>Total Buku Terpinjam</em>), tabel daftar sirkulasi buku hari ini, serta <strong>tabel tenggat buku yang harus segera kembali</strong> (peringatan peminjam yang mendekati atau melewati tanggal jatuh tempo).</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">B</div>
                                <div class="step-body">
                                    <strong>Mesin Scanner Perpustakaan Hibrida (3 Tab Alur Cerdas)</strong>
                                    <p>Terletak pada menu <strong>Scanner Perpustakaan</strong>, menyajikan pemindaian cepat tanpa perlu mengetik formulir manual:</p>
                                    <ul style="margin-top: 0.35rem; padding-left: 1.25rem;">
                                        <li><strong>Tab 1 - Kunjungan:</strong> Cukup tempel kartu santri. Sistem mencatat kunjungan harian dan otomatis memberikan <strong style="color: #059669;">+1 Poin Literasi</strong> santri.</li>
                                        <li><strong>Tab 2 - Pinjam Buku (Alur 2 Langkah):</strong> Langkah 1: Tempel kartu santri peminjam. Langkah 2: Scan barcode koleksi buku. Sistem menampilkan pratinjau buku, durasi pinjam fleksibel, kalkulasi tanggal jatuh tempo otomatis, dan memberi bonus <strong style="color: #059669;">+2 Poin Literasi</strong>.</li>
                                        <li><strong>Tab 3 - Pengembalian Buku:</strong> Cukup scan barcode buku yang dikembalikan. Sistem otomatis memproses pengembalian, mengembalikan jumlah stok buku, dan mengalkulasi denda keterlambatan jika melewati jatuh tempo.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">C</div>
                                <div class="step-body">
                                    <strong>Papan Peringkat Literasi (Leaderboard)</strong>
                                    <p>Menu <em class="highlight-text">Leaderboard Literasi</em> menyajikan pemeringkatan santri paling rajin berkunjung dan membaca buku berdasarkan akumulasi skor poin literasi. Fitur ini dirancang sebagai gamifikasi edukatif untuk mendongkrak budaya membaca santri.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">D</div>
                                <div class="step-body">
                                    <strong>Cetak Label & Barcode Buku Siap Tempel</strong>
                                    <p>Pada menu <em class="highlight-text">Buku</em>, pengelola perpustakaan dapat memilih buku-buku yang ada di katalog, lalu mencetak stiker barcode dan label punggung buku dalam format lembar cetak standar yang siap digunting dan ditempelkan pada fisik buku.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 6: MASTER DATA AKADEMIK & JADWAL
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'master') && match('master data siswa foto guru ustadz kelas jam pelajaran jadwal mengajar')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">6</span>
                            <span>Manajemen Master Data: Siswa & Foto, Guru, Kelas, dan Jadwal Mengajar</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Pengelolaan data akademik pada Absaka saling terhubung untuk mendukung absensi terpadu dan sistem akademik:</p>

                        <ul style="list-style-type: disc; padding-left: 1.25rem;">
                            <li style="margin-bottom: 0.75rem;">
                                <strong>Data Siswa & Pengelolaan Foto Profil:</strong><br>
                                Data siswa kini mendukung kolom <strong>Foto Siswa</strong> (nullable). Format gambar yang didukung meliputi JPG, PNG, dan WEBP dengan kompresi otomatis. Foto siswa ditampilkan saat kartu santri discan di monitor scanner desktop. Jika foto belum diunggah, sistem otomatis menampilkan <em>avatar placeholder</em> yang elegan.
                            </li>
                            <li style="margin-bottom: 0.75rem;">
                                <strong>Data Guru & Penugasan Wali Kelas:</strong><br>
                                Setiap guru memiliki NIP dan nomor WhatsApp aktif. Guru dapat ditugaskan sebagai <strong>Wali Kelas</strong> pada menu Data Kelas. Penunjukan ini menjadi dasar pembatasan hak akses (*data scoping*) pada sistem.
                            </li>
                            <li style="margin-bottom: 0.75rem;">
                                <strong>Jam Pelajaran & Jadwal Mengajar:</strong><br>
                                Menu <em class="highlight-text">Jam Pelajaran</em> memungkinkan pengaturan jam ke-1, jam ke-2, istirahat, dan seterusnya. Menu <em class="highlight-text">Jadwal Mengajar</em> mengaitkan guru, mata pelajaran, kelas, hari, dan jam pelajaran untuk memastikan keteraturan jadwal pembelajaran madrasah/sekolah.
                            </li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 7: REKAPITULASI, KOREKSI & EKSPOR EXCEL
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'rekap') && match('rekapitulasi absensi koreksi manual izin sakit alpa ekspor excel multi sheet matrix')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">7</span>
                            <span>Rekapitulasi Absensi, Koreksi Manual & Ekspor Excel Multi-Sheet</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Menu <strong>Rekap Absensi</strong> menyajikan log kehadiran real-time seluruh santri dengan fitur filter lengkap dan ekspor data berstandar administrasi sekolah:</p>

                        <div class="grid-2-col">
                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 0.95rem; font-weight: 700;">
                                    ✍️ Koreksi Manual & Input Izin / Sakit (Opsi B)
                                </h4>
                                <ul style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    <li><strong>Input Manual Pengganti Kartu:</strong> Jika kartu santri tertinggal atau rusak, klik tombol biru <strong>"Input Absen Manual"</strong> di pojok kanan atas Rekap Absensi untuk memasukkan kehadirannya secara manual.</li>
                                    <li><strong>Koreksi Status Alpa ke Izin/Sakit:</strong> Jika santri yang terlanjur ditandai Alpa oleh cron job membawa surat dokter atau keterangan wali santri, klik tombol <strong>Edit</strong> pada baris siswa tersebut dan ubah statusnya menjadi "Sakit" atau "Izin". Rekapitulasi di dashboard otomatis diperbarui secara instan.</li>
                                </ul>
                            </div>

                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 0.95rem; font-weight: 700;">
                                    📑 Ekspor Excel Matriks Bulanan Berstandar Resmi
                                </h4>
                                <ul style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    <li><strong>Ekspor Per Kelas:</strong> Buka Data Kelas -> View -> klik tombol <strong>"Ekspor Absensi Bulanan"</strong> untuk mengunduh rekap kelas tersebut.</li>
                                    <li><strong>Ekspor Semua Kelas Sekaligus (Multi-Sheet):</strong> Buka Rekap Absensi -> klik <strong>"Ekspor Semua Kelas"</strong>. File Excel yang terunduh otomatis memiliki tab sheet terpisah untuk setiap kelas.</li>
                                </ul>
                            </div>
                        </div>

                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 1.25rem 0 0.5rem 0;" class="dark:!text-white">
                            🎨 Legenda Warna Sel Excel
                        </h4>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="padding: 0.3rem 0.65rem; border-radius: 0.375rem; background-color: #d1fae5; color: #065f46; font-size: 0.8rem; font-weight: 700; border: 1px solid #a7f3d0;">H (Hadir) - Hijau</span>
                            <span style="padding: 0.3rem 0.65rem; border-radius: 0.375rem; background-color: #e0f2fe; color: #075985; font-size: 0.8rem; font-weight: 700; border: 1px solid #bae6fd;">S (Sakit) - Biru</span>
                            <span style="padding: 0.3rem 0.65rem; border-radius: 0.375rem; background-color: #fef3c7; color: #92400e; font-size: 0.8rem; font-weight: 700; border: 1px solid #fde68a;">I (Izin) - Kuning</span>
                            <span style="padding: 0.3rem 0.65rem; border-radius: 0.375rem; background-color: #ffe4e6; color: #9f1239; font-size: 0.8rem; font-weight: 700; border: 1px solid #fecdd3;">A (Alpa) - Merah</span>
                            <span style="padding: 0.3rem 0.65rem; border-radius: 0.375rem; background-color: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 700; border: 1px solid #cbd5e1;">L (Libur) - Abu-abu</span>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 8: WHATSAPP GATEWAY & OTOMASI ALPA
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'wa') && match('whatsapp gateway sidobe api notifikasi wali alpa cron job otomatis rekap pimpinan')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">8</span>
                            <span>Integrasi WhatsApp Gateway (Sidobe) & Otomasi Alpa Harian</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka terhubung secara native dengan penyedia gerbang WhatsApp <strong>Sidobe.com</strong> untuk memberikan transparansi informasi presensi kepada wali santri dan pimpinan lembaga:</p>

                        <div class="step-list">
                            <div class="step-item">
                                <div class="step-num">1</div>
                                <div class="step-body">
                                    <strong>Notifikasi Real-Time ke Orang Tua / Wali Santri</strong>
                                    <p>Saat santri menempelkan kartu di mesin scanner, sistem secara instan mengirimkan pesan WhatsApp ke nomor HP wali santri yang terdaftar:</p>
                                    <ul style="padding-left: 1.25rem; margin-top: 0.25rem;">
                                        <li><strong>Pesan Masuk:</strong> Mengabarkan jam kedatangan santri di madrasah beserta status (Tepat Waktu / Terlambat).</li>
                                        <li><strong>Pesan Pulang:</strong> Mengabarkan jam kepulangan santri setelah memindai kartu absen pulang.</li>
                                        <li><strong>Pesan Alpa:</strong> Mengabarkan santri belum terdeteksi hadir hingga jam batas masuk berakhir.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">2</div>
                                <div class="step-body">
                                    <strong>Mekanisme Otomasi Alpa Harian (Artisan Command)</strong>
                                    <p>Perintah Artisan <code class="code-inline">php artisan absensi:process-daily-alpa</code> dirancang untuk dijalankan otomatis melalui <strong>Cron Job Server</strong> beberapa menit setelah jendela absen masuk ditutup:</p>
                                    <ul style="padding-left: 1.25rem; margin-top: 0.25rem;">
                                        <li>Sistem memeriksa seluruh santri aktif yang belum memiliki rekam presensi hari ini.</li>
                                        <li>Santri yang tidak hadir otomatis dicatat berstatus <strong>Alpa</strong>.</li>
                                        <li>Sistem otomatis melompati hari libur nasional (dari Kalender Libur) atau libur mingguan (Jumat/Ahad).</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-num">3</div>
                                <div class="step-body">
                                    <strong>Laporan Rekapitulasi Harian ke Pimpinan / Super Admin</strong>
                                    <p>Setelah proses Alpa selesai dieksekusi, sistem secara otomatis merangkum data rekapitulasi kehadiran harian (Total Siswa, Hadir Tepat Waktu, Terlambat, Sakit, Izin, Alpa) dan mengirimkannya langsung ke nomor WhatsApp ustadz/pimpinan yang memiliki akun role <strong>super_admin</strong>.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert-box alert-success" style="margin-top: 1rem;">
                            <strong>⚙️ Contoh Konfigurasi Cron Job di cPanel / Hostinger:</strong><br>
                            <code class="code-inline" style="display: block; margin-top: 0.35rem; padding: 0.5rem; background: #064e3b; color: #a7f3d0; border-radius: 0.375rem;">
                                * * * * * cd /home/u12345/public_html && php artisan schedule:run >> /dev/null 2>&1
                            </code>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 9: PWA & INSTALASI APLIKASI
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'pwa') && match('pwa progressive web app install aplikasi android windows desktop ios iphone ipad safari service worker')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">9</span>
                            <span>Progressive Web App (PWA): Panduan Instalasi di Android, Windows, dan iOS</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka telah dikonfigurasi sebagai <strong>Progressive Web App (PWA)</strong> modern. Pengguna dapat memasang aplikasi langsung ke layar utama perangkat tanpa perlu mengunduh file APK dari Play Store atau App Store:</p>

                        <div class="grid-2-col">
                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 0.95rem; font-weight: 700;">
                                    💻 Komputer / Laptop Windows (Chrome / Edge)
                                </h4>
                                <ol style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    <li>Buka Absaka di Google Chrome atau Microsoft Edge.</li>
                                    <li>Klik tombol <strong>"Install Aplikasi Ini"</strong> yang terletak sticky di bagian bawah sidebar Filament, ATAU klik ikon monitor install di bilah alamat (URL bar) browser.</li>
                                    <li>Klik "Install". Aplikasi akan terbuka di jendela mandiri tanpa address bar layaknya aplikasi desktop profesional.</li>
                                </ol>
                            </div>

                            <div class="info-card">
                                <h4 style="margin: 0 0 0.5rem 0; color: #047857; font-size: 0.95rem; font-weight: 700;">
                                    🤖 Smartphone Android (Google Chrome)
                                </h4>
                                <ol style="font-size: 0.85rem; color: #374151; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    <li>Buka tautan Absaka di Google Chrome pada ponsel Anda.</li>
                                    <li>Klik tombol <strong>"Install Aplikasi Ini"</strong> pada sidebar, ATAU buka menu titik tiga di kanan atas browser dan pilih <strong>"Install Aplikasi" / "Tambahkan ke Layar Utama"</strong>.</li>
                                    <li>Ikon aplikasi Absaka akan muncul di layar beranda ponsel dengan pengalaman layar penuh.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="alert-box alert-info" style="margin-top: 1rem;">
                            <strong>🍎 Panduan Khusus Pengguna Apple iOS (iPhone & iPad Safari):</strong><br>
                            Karena kebijakan Apple, browser Safari di iOS tidak mengizinkan pemasangan otomatis lewat tombol skrip. Silakan ikuti langkah manual berikut:<br>
                            1. Buka tautan sistem Absaka menggunakan browser <strong>Safari</strong>.<br>
                            2. Ketuk ikon <strong>Bagikan / Share</strong> (ikon kotak dengan panah mengarah ke atas di bilah bawah Safari).<br>
                            3. Gulir menu ke bawah lalu ketuk opsi <strong>"Add to Home Screen" (Tambahkan ke Layar Utama)</strong>.<br>
                            4. Ketuk <strong>"Add"</strong> di pojok kanan atas. Ikon aplikasi Absaka kini siap digunakan di layar iPhone Anda!
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 10: HAK AKSES, PEMBAGIAN 8 PERAN & STRUKTUR SIDEBAR
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'security') && match('hak akses peran role 8 role wali kelas super admin shield keamanan data scoping multitenancy password staff absensi perpus guru siswa')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">10</span>
                            <span>Manajemen Hak Akses: Pembagian 8 Peran (Role) & Tata Letak Sidebar</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka mengadopsi kontrol akses berbasis peran (RBAC) granular dengan <strong>8 peran resmi</strong> yang disesuaikan secara presisi terhadap tugas pokok dan fungsi (tupoksi) masing-masing civitas:</p>

                        <div class="table-container">
                            <table class="doc-table">
                                <thead>
                                    <tr>
                                        <th>Nama Peran (Role)</th>
                                        <th>Menu yang Tampil di Sidebar</th>
                                        <th>Cakupan & Wewenang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code class="code-inline">super_admin</code></td>
                                        <td>Seluruh Menu Lengkap</td>
                                        <td>Akses penuh mutlak mengelola semua modul sistem: Presensi, Data Akademik, Perpustakaan, Pengaturan Jam & WA, Manajemen Pengguna, serta audit log.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">admin_absensi</code></td>
                                        <td>Presensi & Kehadiran, Data Akademik, Pengaturan Jam/WA/Libur</td>
                                        <td>Mengelola master siswa/guru/kelas, rekap presensi seluruh sekolah, jadwal guru piket, jadwal mengajar, dan verifikasi alpa massal.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">staff_absensi</code></td>
                                        <td>Dashboard Utama & Scanner Presensi</td>
                                        <td>Khusus staf pos gerbang/piket untuk mengoperasikan alat scanner RFID dan barcode QR presensi pagi & sore.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">admin_perpustakaan</code></td>
                                        <td>Modul Perpustakaan Lengkap & Buku Panduan</td>
                                        <td>Mengelola katalog koleksi buku, sirkulasi peminjaman, log kunjungan, rating minat literasi, cetak label barcode, dan aturan denda perpustakaan.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">staff_perpustakaan</code></td>
                                        <td>Dashboard Perpustakaan & Scanner Perpustakaan</td>
                                        <td>Khusus staf meja sirkulasi perpustakaan untuk memindai kartu pengunjung dan kartu peminjam buku.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">wali_kelas</code></td>
                                        <td>Dashboard, Rekap Presensi (Kelas & Pribadi), Data Kelas Binaan, Ajukan Izin</td>
                                        <td><strong>Isolasi Data (Data Scoping):</strong> Hanya melihat presensi siswa dari kelas yang diampunya (read-only tanpa edit/hapus), dapat mengekspor rekap bulanan kelas binaan, dan memantau izin siswa kelasnya.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">guru</code></td>
                                        <td>Dashboard, Rekap Presensi (Pribadi), Ajukan Izin</td>
                                        <td>Murni read-only untuk memantau kehadiran pribadi dan mengajukan permohonan izin/sakit mandiri.</td>
                                    </tr>
                                    <tr>
                                        <td><code class="code-inline">siswa</code></td>
                                        <td>Dashboard, Rekap Presensi (Pribadi), Ajukan Izin</td>
                                        <td>Murni read-only untuk memantau kehadiran pribadi dan mengajukan permohonan izin/sakit mandiri.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="info-card" style="margin-top: 1rem;">
                            <strong style="color: #065f46; display: block; margin-bottom: 0.5rem;">📁 Tata Letak 4 Kelompok Menu Sidebar Baru:</strong>
                            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; line-height: 1.6;">
                                <li><strong>Top Level (Akses Cepat):</strong> <em>Dashboard Utama</em> dan <em>Scanner Presensi</em>.</li>
                                <li><strong>1. Presensi & Kehadiran:</strong> <em>Rekap Absensi, Ajukan Izin, Rating Kedisiplinan, Jadwal Guru Piket, Jadwal Mengajar</em>.</li>
                                <li><strong>2. Data Akademik:</strong> <em>Data Siswa, Data Guru, Data Kelas</em>.</li>
                                <li><strong>3. Perpustakaan:</strong> <em>Dashboard Perpustakaan, Scanner Perpustakaan, Katalog Buku, Peminjaman Buku, Log Kunjungan, Rating Minat Literasi, Pengaturan Perpustakaan</em>.</li>
                                <li><strong>4. Pengaturan & Sistem:</strong> <em>Pengaturan Jam Presensi, Kalender Hari Libur, Integrasi WhatsApp, Manajemen Pengguna, Buku Panduan</em>.</li>
                            </ul>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 11: SIGN IN WITH GOOGLE & VERIFIKASI INTERNAL
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'google') && match('google sign in oauth akun gmail nis nip verifikasi pendaftaran internal keamanan password')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">11</span>
                            <span>Otentikasi "Sign In with Google" & Verifikasi Nomor Induk (NIS/NIP)</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka menyediakan fitur login 1-klik melalui tombol <strong>"Lanjutkan dengan Akun Google"</strong> di halaman login. Karena Absaka adalah aplikasi internal sekolah, diterapkan sistem verifikasi berlapis agar hanya civitas resmi yang dapat masuk:</p>

                        <div class="step-list">
                            <div class="step-item">
                                <div class="step-num">1</div>
                                <div class="step-body">
                                    <strong>Klik Tombol Google di Halaman Login</strong>
                                    <p>Pengguna memilih akun Google mereka melalui dialog resmi OAuth 2.0 Google.</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-num">2</div>
                                <div class="step-body">
                                    <strong>Pemeriksaan Pendaftaran Pertama Kali (Interstitial Form)</strong>
                                    <p>Jika akun Google tersebut belum pernah terdaftar di sistem, sistem akan menampilkan halaman verifikasi internal: <em>"Verifikasi Akun Internal"</em> yang mewajibkan input <strong>Nomor Induk / Nomor Kartu (NIS untuk siswa, atau NIP untuk guru)</strong>.</p>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-num">3</div>
                                <div class="step-body">
                                    <strong>Validasi Database & Penugasan Peran Otomatis (Auto-Role)</strong>
                                    <p>Sistem mencocokkan Nomor Induk ke database sekolah:</p>
                                    <ul style="margin: 0.25rem 0 0 1.25rem; font-size: 0.85rem;">
                                        <li>Bila terdaftar sebagai <strong>Siswa</strong>: Otomatis dibuatkan user siswa terhubung dan diberikan peran <code class="code-inline">siswa</code>.</li>
                                        <li>Bila terdaftar sebagai <strong>Guru</strong>: Otomatis dibuatkan user guru terhubung dan diberikan peran <code class="code-inline">guru</code> (atau <code class="code-inline">wali_kelas</code> jika ia adalah wali kelas aktif).</li>
                                        <li>Bila nomor induk <strong>tidak ditemukan</strong>: Pendaftaran dibatalkan seketika dengan pesan peringatan keamanan.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="step-item">
                                <div class="step-num">4</div>
                                <div class="step-body">
                                    <strong>Login Seterusnya Instan Tanpa Verifikasi Ulang</strong>
                                    <p>Setelah terverifikasi pertama kali, akun Google tersebut telah tertaut permanen dengan profil siswa/guru. Pada kunjungan berikutnya, pengguna cukup klik "Lanjutkan dengan Akun Google" dan langsung masuk ke dashboard pribadinya.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert-box alert-info" style="margin-top: 1rem;">
                            <strong>📄 Kepatuhan Kebijakan Privasi:</strong><br>
                            Sistem hanya mengakses nama, email, dan avatar publik akun Google. Tautan resmi dokumen hukum tersedia pada footer login atau dapat diakses langsung di:
                            <a href="{{ route('legal.privacy') }}" target="_blank" style="text-decoration: underline; font-weight: 700;">Kebijakan Privasi</a> dan 
                            <a href="{{ route('legal.terms') }}" target="_blank" style="text-decoration: underline; font-weight: 700;">Ketentuan Layanan</a>.
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 12: PENGAJUAN IZIN & JADWAL GURU PIKET
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'izin') && match('pengajuan izin sakit guru piket approval setujui tolak dokter surat sync absensi otomatis')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">12</span>
                            <span>Pengajuan Izin/Sakit Mandiri & Jadwal Guru Piket (Approval & Auto-Sync)</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka mengintegrasikan permohonan dispensasi kehadiran digital dengan alur persetujuan terdesentralisasi berbasis petugas piket harian:</p>

                        <div class="grid-2-col">
                            <div class="info-card">
                                <strong style="color: #065f46; display: block; margin-bottom: 0.5rem;">📝 Formulir Ajukan Izin (Siswa & Guru)</strong>
                                <p style="font-size: 0.85rem; line-height: 1.5; margin: 0 0 0.5rem 0;">Siswa maupun Guru dapat mengajukan permohonan izin/sakit melalui menu <em>Ajukan Izin</em> dengan mengisi:</p>
                                <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.825rem; line-height: 1.5;">
                                    <li><strong>Jenis Pengajuan:</strong> Izin atau Sakit.</li>
                                    <li><strong>Rentang Waktu:</strong> Tanggal mulai s/d tanggal selesai izin.</li>
                                    <li><strong>Alasan:</strong> Uraian keperluan izin atau gejala sakit.</li>
                                    <li><strong>Bukti Foto:</strong> Surat dokter atau surat pernyataan wali (opsional, max 2MB).</li>
                                </ul>
                            </div>

                            <div class="info-card">
                                <strong style="color: #065f46; display: block; margin-bottom: 0.5rem;">📅 Jadwal Guru Piket Mingguan</strong>
                                <p style="font-size: 0.85rem; line-height: 1.5; margin: 0 0 0.5rem 0;">Dikelola oleh Admin di menu <em>Jadwal Guru Piket</em>:</p>
                                <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.825rem; line-height: 1.5;">
                                    <li>Mendukung <strong>lebih dari 1 guru piket per hari</strong> (Senin s/d Ahad).</li>
                                    <li>Guru yang bertugas pada hari itu otomatis memiliki wewenang untuk memeriksa, menyetujui, atau menolak izin dari siswa dan sesama guru.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert-box alert-success" style="margin-top: 1rem;">
                            <strong>⚡ Fitur Sakti: Auto-Sync Kehadiran Saat Disetujui (Approved)</strong><br>
                            Ketika Guru Piket atau Admin menekan tombol <strong>"Setujui"</strong> pada permohonan izin, sistem secara otomatis:
                            <ol style="margin: 0.35rem 0 0 1.25rem; padding: 0;">
                                <li>Memperbarui status permohonan menjadi <em>Disetujui</em> lengkap dengan nama petugas approval dan tanggal persetujuan.</li>
                                <li>Mengisi tabel <strong>Rekap Absensi</strong> untuk setiap tanggal dalam rentang izin tersebut dengan status kehadiran <code class="code-inline">Izin</code> atau <code class="code-inline">Sakit</code> tanpa perlu petugas input manual satu per satu!</li>
                            </ol>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 13: RATING KEDISIPLINAN & LITERASI
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'rating') && match('rating kedisiplinan minat literasi leaderboard podium gamifikasi ranking prestasi poin 3 teratas terendah')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">13</span>
                            <span>Rating Kedisiplinan & Rating Minat Literasi (Gamifikasi Prestasi)</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka menyajikan dua papan peringkat (*Leaderboard*) untuk memotivasi kedisiplinan kehadiran dan meningkatkan budaya literasi membaca buku:</p>

                        <div class="grid-2-col">
                            <div class="info-card">
                                <strong style="color: #065f46; display: block; margin-bottom: 0.35rem;">🏆 Rating Kedisiplinan (Siswa & Guru)</strong>
                                <p style="font-size: 0.85rem; line-height: 1.5; margin: 0 0 0.5rem 0;">Menghitung skor kedisiplinan berbasis kehadiran tepat waktu vs keterlambatan/alpa:</p>
                                <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.825rem; line-height: 1.5;">
                                    <li><strong>Podium 3 Teratas:</strong> Visualisasi bertingkat elegan (Emas #1, Perak #2, Perunggu #3).</li>
                                    <li><strong>Tabel 3 Terendah:</strong> Disajikan dalam format tabel untuk evaluasi pembinaan wali kelas & guru BK.</li>
                                    <li>Tersedia tab pemisahan antara Kedisiplinan Siswa dan Kedisiplinan Guru.</li>
                                </ul>
                            </div>

                            <div class="info-card">
                                <strong style="color: #065f46; display: block; margin-bottom: 0.35rem;">📖 Rating Minat Literasi (Perpustakaan)</strong>
                                <p style="font-size: 0.85rem; line-height: 1.5; margin: 0 0 0.5rem 0;">Menghitung keaktifan literasi siswa di perpustakaan:</p>
                                <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.825rem; line-height: 1.5;">
                                    <li>Menggabungkan bobot skor peminjaman buku dan frekuensi kunjungan membaca di perpustakaan.</li>
                                    <li>Podium bertingkat 3 pembaca teraktif untuk apresiasi duta literasi sekolah.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert-box alert-info" style="margin-top: 1rem;">
                            <strong>📅 Filter Waktu Fleksibel (Dropdown Select):</strong><br>
                            Kedua halaman peringkat dilengkapi filter waktu terintegrasi:
                            <strong>Bulan Ini</strong> (default), <strong>Tahun Ini</strong>, <strong>Semua Waktu</strong>, serta <strong>Kustom Tanggal</strong> dengan pemilihan rentang tanggal langsung dari kalender interaktif.
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- ==========================================
                 TOPIK 14: LIBUR NASIONAL & NOTIFIKASI CERDAS
                 ========================================== -->
            <div x-show="(activeTab === 'all' || activeTab === 'notif') && match('libur nasional cuti bersama google calendar sync otomatis notifikasi h-3 lonceng web push warning alpa')" class="doc-card">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 0.375rem; background-color: #ecfdf5; color: #047857; font-size: 0.9rem; font-weight: 800;">14</span>
                            <span>Kalender Libur Nasional Otomatis & Sistem Notifikasi Cerdas (Push Alert)</span>
                        </div>
                    </x-slot>

                    <div class="doc-content">
                        <p>Absaka dilengkapi mesin sinkronisasi kalender hari libur resmi dan mesin notifikasi multi-kondisi yang proaktif:</p>

                        <div class="info-card" style="margin-bottom: 1rem;">
                            <strong style="color: #065f46; display: block; margin-bottom: 0.35rem;">🌐 Sinkronisasi Otomatis Hari Libur Nasional Indonesia (Resmi Google Calendar)</strong>
                            <p style="font-size: 0.85rem; line-height: 1.5; margin: 0 0 0.5rem 0;">
                                Sistem terhubung langsung dengan <em>Official Indonesian Holiday Feed (iCal)</em> dari Google Calendar yang selalu diperbarui berdasarkan SKB 3 Menteri resmi tanpa memerlukan API Key berbayar:
                            </p>
                            <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.825rem; line-height: 1.5;">
                                <li>Di halaman <em>Kalender Hari Libur</em>, Admin cukup mengklik tombol <strong>"Sinkronkan Libur Nasional"</strong> untuk mengunduh 40+ tanggal merah dan cuti bersama resmi tahun berjalan dan tahun depan.</li>
                                <li>Dapat dijalankan secara otomatis melalui jadwal terjadwal terminal: <code class="code-inline">php artisan app:sync-hari-libur</code>.</li>
                            </ul>
                        </div>

                        <strong style="color: #0f172a; font-size: 0.95rem; display: block; margin: 1rem 0 0.5rem 0;">🔔 6 Pemicu Notifikasi Otomatis (Lonceng Topbar & Web Push):</strong>
                        <div class="table-container">
                            <table class="doc-table">
                                <thead>
                                    <tr>
                                        <th>Kondisi Pemicu</th>
                                        <th>Target Penerima</th>
                                        <th>Isi & Tindakan Notifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>5 Menit Sebelum Batas Tepat Waktu</strong></td>
                                        <td>Siswa & Guru Terkait</td>
                                        <td>Peringatan bahwa batas waktu masuk tepat waktu akan berakhir dalam hitungan menit jika yang bersangkutan belum scan presensi dan tidak mengajukan izin.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Batas Akhir Masuk Selesai</strong></td>
                                        <td>Guru Piket Hari Ini</td>
                                        <td>Rekapitulasi siswa yang belum presensi. Mengingatkan guru piket untuk memproses alpa jika metode manual, atau mengabarkan jumlah santri teralpa jika metode otomatis.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pengajuan Izin Baru Masuk</strong></td>
                                        <td>Guru Piket Hari Ini</td>
                                        <td>Notifikasi instan bahwa ada siswa/guru yang mengajukan permohonan izin/sakit dengan tombol langsung untuk meninjau formulir.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Buku Pinjaman Jatuh Tempo Hari Ini</strong></td>
                                        <td>Admin & Staff Perpustakaan</td>
                                        <td>Peringatan terdapat buku yang harus dikembalikan hari ini agar petugas dapat menyiapkan penagihan/pemeriksaan denda.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>5 Menit Setelah Batas Masuk Selesai</strong></td>
                                        <td>Wali Kelas</td>
                                        <td>Laporan rekap kehadiran kelas binaannya: jumlah tepat waktu, terlambat, sakit, izin, alpa, dan belum absen.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>H-3 Hari Libur Nasional</strong></td>
                                        <td>Super Admin & Admin Absensi</td>
                                        <td>Peringatan bahwa 3 hari ke depan adalah tanggal merah libur nasional dengan tombol <em>"Ubah / Tinjau"</em> untuk memastikan kesiapan kalender sekolah.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert-box alert-success" style="margin-top: 1rem;">
                            <strong>📱 Tombol Pintar di Sidebar Bawah:</strong><br>
                            Tombol <em>"Aktifkan Notifikasi"</em> berada di bagian bawah sidebar tepat di atas tombol install. Tombol ini otomatis menghilang secara mandiri ketika izin notifikasi browser telah diizinkan (*granted*).
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- KARTU JIKA PENCARIAN TIDAK DITEMUKAN -->
            <div x-show="
                !match('starter kit inisialisasi urutan konfigurasi langkah mulai pertama kali setup jam guru kelas siswa wa sidobe perpus') &&
                !match('import data siswa excel csv format kolom kelas nis nama wali foto') &&
                !match('mesin scanner absensi rfid kamera qr hybrid desktop mobile suara chime') &&
                !match('batas waktu terlambat toleransi cooldown audio success failed mpeg jumat') &&
                !match('perpustakaan digital modul buku pinjam kembali sirkulasi kunjungan denda jatuh tempo dasbor literasi') &&
                !match('master data siswa foto guru ustadz kelas jam pelajaran jadwal mengajar') &&
                !match('rekapitulasi absensi koreksi manual izin sakit alpa ekspor excel multi sheet matrix') &&
                !match('whatsapp gateway sidobe api notifikasi wali alpa cron job otomatis rekap pimpinan') &&
                !match('pwa progressive web app install aplikasi android windows desktop ios iphone ipad safari service worker') &&
                !match('hak akses peran role 8 role wali kelas super admin shield keamanan data scoping multitenancy password staff absensi perpus guru siswa') &&
                !match('google sign in oauth akun gmail nis nip verifikasi pendaftaran internal keamanan password') &&
                !match('pengajuan izin sakit guru piket approval setujui tolak dokter surat sync absensi otomatis') &&
                !match('rating kedisiplinan minat literasi leaderboard podium gamifikasi ranking prestasi poin 3 teratas terendah') &&
                !match('libur nasional cuti bersama google calendar sync otomatis notifikasi h-3 lonceng web push warning alpa')
            " style="text-align: center; padding: 3rem 1.5rem; background: #ffffff; border-radius: 1rem; border: 1.5px dashed #d1d5db;" class="dark:!bg-slate-900 dark:!border-slate-800">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: #1f2937; margin: 0 0 0.35rem 0;" class="dark:!text-white">Topik Tidak Ditemukan</h3>
                <p style="font-size: 0.85rem; color: #6b7280; margin: 0 0 1rem 0;" class="dark:!text-slate-400">
                    Tidak ada materi dokumentasi yang cocok dengan kata kunci "<span x-text="search" style="font-weight: 700; color: #047857;"></span>".
                </p>
                <button type="button" @click="search = ''; activeTab = 'all'" style="padding: 0.5rem 1rem; border-radius: 0.375rem; background: #10b981; color: white; border: none; font-size: 0.8rem; font-weight: 700; cursor: pointer;">
                    Reset Pencarian
                </button>
            </div>

        </div>

        <!-- ==============================================================
             5. STYLING TERISOLASI DOKUMENTASI (EMERALD & DARK MODE)
             ============================================================== -->
        <style>
            .doc-card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .doc-content {
                font-size: 0.925rem;
                line-height: 1.65;
                color: #374151;
            }

            .doc-content p {
                margin-top: 0;
                margin-bottom: 0.85rem;
            }

            .highlight-text {
                color: #047857;
                font-style: normal;
                font-weight: 700;
            }

            .code-inline {
                background-color: #f1f5f9;
                color: #0f766e;
                padding: 0.15rem 0.4rem;
                border-radius: 0.25rem;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                font-size: 0.85rem;
                border: 1px solid #e2e8f0;
            }

            /* Tombol Navigasi Tab */
            .doc-tab-btn {
                background: none;
                border: none;
                padding: 0.65rem 1.15rem;
                font-size: 0.85rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                color: #64748b;
                border-bottom: 2px solid transparent;
                border-radius: 0.375rem 0.375rem 0 0;
            }

            .doc-tab-btn:hover {
                color: #047857;
                background-color: rgba(16, 185, 129, 0.05);
            }

            .doc-tab-btn-active {
                color: #047857 !important;
                border-bottom: 2.5px solid #10b981 !important;
                font-weight: 800 !important;
                background-color: rgba(16, 185, 129, 0.08);
            }

            /* Daftar Langkah / Steps */
            .step-list {
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
                margin-top: 1rem;
            }

            .step-item {
                display: flex;
                gap: 0.85rem;
                align-items: flex-start;
            }

            .step-num {
                width: 1.75rem;
                height: 1.75rem;
                border-radius: 9999px;
                background-color: #ecfdf5;
                color: #047857;
                font-weight: 800;
                font-size: 0.8rem;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                margin-top: 0.15rem;
                border: 1px solid #a7f3d0;
            }

            .step-body {
                flex: 1;
            }

            .step-body strong {
                font-size: 0.95rem;
                color: #0f172a;
                display: block;
                margin-bottom: 0.2rem;
            }

            .step-body p {
                font-size: 0.875rem;
                color: #4b5563;
                margin: 0;
                line-height: 1.5;
            }

            /* Kotak Info 2-Kolom */
            .grid-2-col {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1rem;
                margin: 1rem 0;
            }

            .info-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                padding: 1.15rem;
            }

            /* Kotak Alert Notifikasi */
            .alert-box {
                padding: 0.85rem 1.15rem;
                border-radius: 0.625rem;
                font-size: 0.85rem;
                line-height: 1.55;
            }

            .alert-info {
                background-color: #f0fdf4;
                border: 1px solid #bbf7d0;
                color: #166534;
            }

            .alert-warning {
                background-color: #fffbeb;
                border: 1px solid #fde68a;
                color: #92400e;
            }

            .alert-success {
                background-color: #ecfdf5;
                border: 1px solid #a7f3d0;
                color: #065f46;
            }

            /* Tabel Dokumentasi */
            .table-container {
                overflow-x: auto;
                margin: 1rem 0;
                border-radius: 0.625rem;
                border: 1px solid #e5e7eb;
            }

            .doc-table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
                font-size: 0.85rem;
            }

            .doc-table th {
                background-color: #f8fafc;
                color: #334155;
                padding: 0.75rem 1rem;
                font-weight: 700;
                border-bottom: 1px solid #e2e8f0;
            }

            .doc-table td {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid #f1f5f9;
                color: #475569;
            }

            .doc-table tr:last-child td {
                border-bottom: none;
            }

            .badge-required {
                display: inline-block;
                padding: 0.15rem 0.5rem;
                border-radius: 9999px;
                background-color: #ffe4e6;
                color: #9f1239;
                font-size: 0.7rem;
                font-weight: 700;
            }

            .badge-optional {
                display: inline-block;
                padding: 0.15rem 0.5rem;
                border-radius: 9999px;
                background-color: #f1f5f9;
                color: #475569;
                font-size: 0.7rem;
                font-weight: 700;
            }

            /* ==============================================================
               PENYESUAIAN DARK MODE
               ============================================================== */
            .dark .doc-content {
                color: #cbd5e1 !important;
            }

            .dark .highlight-text {
                color: #34d399 !important;
            }

            .dark .code-inline {
                background-color: #1e293b !important;
                color: #2dd4bf !important;
                border-color: #334155 !important;
            }

            .dark .doc-tab-btn {
                color: #94a3b8 !important;
            }

            .dark .doc-tab-btn:hover {
                color: #34d399 !important;
                background-color: rgba(52, 211, 153, 0.08) !important;
            }

            .dark .doc-tab-btn-active {
                color: #34d399 !important;
                border-bottom-color: #10b981 !important;
                background-color: rgba(52, 211, 153, 0.12) !important;
            }

            .dark .step-num {
                background-color: #064e3b !important;
                color: #6ee7b7 !important;
                border-color: #047857 !important;
            }

            .dark .step-body strong {
                color: #f8fafc !important;
            }

            .dark .step-body p {
                color: #94a3b8 !important;
            }

            .dark .info-card {
                background: #0f172a !important;
                border-color: #1e293b !important;
            }

            .dark .info-card p,
            .dark .info-card ul {
                color: #94a3b8 !important;
            }

            .dark .table-container {
                border-color: #1e293b !important;
            }

            .dark .doc-table th {
                background-color: #1e293b !important;
                color: #e2e8f0 !important;
                border-bottom-color: #334155 !important;
            }

            .dark .doc-table td {
                color: #94a3b8 !important;
                border-bottom-color: #1e293b !important;
            }

            .dark .alert-info {
                background-color: #064e3b !important;
                border-color: #047857 !important;
                color: #a7f3d0 !important;
            }

            .dark .alert-warning {
                background-color: #451a03 !important;
                border-color: #78350f !important;
                color: #fde68a !important;
            }

            .dark .alert-success {
                background-color: #064e3b !important;
                border-color: #047857 !important;
                color: #a7f3d0 !important;
            }

            .dark .badge-optional {
                background-color: #1e293b !important;
                color: #94a3b8 !important;
            }
        </style>
    </div>
</x-filament-panels::page>