<x-filament-panels::page>
    <!-- LOGIKA SAKTI ALPINE.JS: Menangani Pencarian & Filter Kategori Instan -->
    <div x-data="{
        search: '',
        activeTab: 'all',
        match(text) {
            return text.toLowerCase().includes(this.search.toLowerCase());
        }
    }">

        <!-- 1. BAR PENCARIAN DINAMIS (Pencarian Instan Tanpa Delay) -->
        <div style="margin-bottom: 1.5rem;" class="no-print">
            <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                <x-filament::input
                    type="text"
                    placeholder="Ketik kata kunci untuk mencari topik panduan (misal: WhatsApp, Alpa, RFID, Import, Scoping, Jumat)..."
                    x-model="search"
                    style="padding: 0.75rem;"
                />
            </x-filament::input.wrapper>
        </div>

        <!-- 2. TAB KATEGORI PURE HTML (Mulus, Elegan & Bebas dari Tabrakan Blade PHP) -->
        <div style="display: flex; gap: 0.5rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.1px; margin-bottom: 1.5rem; flex-wrap: wrap;" class="no-print dark:border-gray-800">
            <button class="custom-tab-btn" @click="activeTab = 'all'" :style="activeTab === 'all' ? 'border-bottom: 2px solid #10b981; color: #10b981; font-weight: 700;' : ''">
                Semua Panduan
            </button>
            <button class="custom-tab-btn" @click="activeTab = 'starter'" :style="activeTab === 'starter' ? 'border-bottom: 2px solid #10b981; color: #10b981; font-weight: 700;' : ''">
                Starter Kit & Persiapan
            </button>
            <button class="custom-tab-btn" @click="activeTab = 'scanner'" :style="activeTab === 'scanner' ? 'border-bottom: 2px solid #10b981; color: #10b981; font-weight: 700;' : ''">
                Mesin Scanner
            </button>
            <button class="custom-tab-btn" @click="activeTab = 'report'" :style="activeTab === 'report' ? 'border-bottom: 2px solid #10b981; color: #10b981; font-weight: 700;' : ''">
                Laporan & Excel
            </button>
            <button class="custom-tab-btn" @click="activeTab = 'wa'" :style="activeTab === 'wa' ? 'border-bottom: 2px solid #10b981; color: #10b981; font-weight: 700;' : ''">
                Integrasi WA & Otomasi
            </button>
            <button class="custom-tab-btn" @click="activeTab = 'security'" :style="activeTab === 'security' ? 'border-bottom: 2px solid #10b981; color: #10b981; font-weight: 700;' : ''">
                Akses Wali Kelas
            </button>
        </div>

        <!-- 3. KUMPULAN MATERI PANDUAN LENGKAP -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- TOPIK 1: STARTER KIT (Langkah Berurutan Memulai) -->
            <div x-show="(activeTab === 'all' || activeTab === 'starter') && (search === '' || match('starter') || match('langkah') || match('mula') || match('pandu') || match('persiapan'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Starter Kit: Panduan Berurutan Inisialisasi Sistem Baru
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Selamat Datang di Aplikasi Absensi Digital. Silakan ikuti langkah-langkah berurutan di bawah ini untuk mengonfigurasi dan memulai penggunaan aplikasi absensi pertama kali secara benar setelah database dibersihkan:</p>
                        <ol style="list-style-type: decimal; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Konfigurasi Jam Operasional Absensi</strong>:<br> Masuk ke menu <em class="highlight-text">Pengaturan Jam</em>. Tentukan jam mulai dan selesai absensi masuk, batas jam toleransi keterlambatan, dan jendela absen pulang. Simpan pengaturan Anda.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Daftarkan Data Guru / Ustadz</strong>:<br> Masuk ke menu <em class="highlight-text">Data Guru</em>, tambahkan ustadz beserta NIP dan Nomor HP/WhatsApp aktif (format HP diawali angka 0, misal: 0812345xxx).</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Daftarkan Data Kelas</strong>:<br> Masuk ke menu <em class="highlight-text">Data Kelas</em>, tambahkan nama kelas dan tunjuk ustadz pengampu kelas tersebut sebagai Wali Kelas.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Pendaftaran & Import Data Siswa</strong>:<br> Masuk ke menu <em class="highlight-text">Data Siswa</em>. Anda bisa menginput manual atau mengunduh template CSV untuk meng-import data santri masal (Pastikan menulis kolom nama kelas persis sesuai data Kelas).</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Daftarkan Akun Login Wali Kelas</strong>:<br> Masuk ke menu <em class="highlight-text">User</em>, buat akun login baru, pilih Tipe Profil "Guru", pilih namanya, lalu centang peran akses <strong>"Wali Kelas"</strong>.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Tautkan WhatsApp Gateway</strong>:<br> Masuk ke menu <em class="highlight-text">Integrasi WA</em>, masukkan Token API Sidobe Anda, klik simpan, lalu gunakan tombol kuning "Tes Koneksi WA" di pojok atas untuk memvalidasi status koneksi.</li>
                        </ol>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 2: ATURAN FORMAT IMPORT SISWA -->
            <div x-show="(activeTab === 'all' || activeTab === 'starter') && (search === '' || match('import') || match('csv') || match('excel') || match('siswa') || match('gagal') || match('kolom'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Aturan Format & Sinkronisasi Import Data Siswa (Excel/CSV)
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Fitur import siswa dirancang sangat fleksibel namun tetap ketat menjaga kebersihan relasi database. Pastikan berkas CSV/Excel Anda mematuhi aturan berikut:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Struktur Kolom Wajib</strong>:<br> File CSV Anda wajib memiliki minimal 3 kolom header dengan nama persis (case-sensitive):
                                <code class="code-block">nis, nama, kelas</code>
                            </li>
                            <li style="margin-bottom: 0.5rem;"><strong>Peta Nama Kelas (Bukan ID)</strong>:<br> Kolom <code class="code-block">kelas</code> harus diisi dengan nama kelas yang sudah terdaftar di menu Data Kelas secara persis, contoh: <strong style="color: #1e3a8a;">Kelas 10-A (Ula)</strong>. Pengisian berupa ID angka tidak diperbolehkan.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Penanganan Baris Gagal (Auto-validation)</strong>:<br> Jika nama kelas di file Excel salah ketik (misal: "Kelas 10 Z" yang tidak ada di database), Filament secara otomatis <strong>hanya akan menggagalkan baris siswa tersebut saja</strong> (baris siswa lain yang benar tetap sukses masuk) dan memunculkan laporan error penulisan kelas di pojok kanan atas.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 3: MESIN SCANNER (RFID vs KAMERA) -->
            <div x-show="(activeTab === 'all' || activeTab === 'scanner') && (search === '' || match('scanner') || match('kamera') || match('rfid') || match('barcode') || match('suara') || match('beep') || match('focus'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Panduan Operasional Mesin Scanner Absensi Hibrida
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Halaman <strong>Scanner Absensi</strong> mendukung dua mode pemindaian hibrida cerdas yang bisa diubah secara instan lewat tombol Toggle di atas halaman:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Mode Kartu / RFID (Rekomendasi)</strong>:<br> Digunakan jika santri/guru membawa kartu fisik RFID atau Barcode. Sistem secara otomatis mengunci kursor ke kolom input. Tempelkan kartu, komputer akan otomatis mengeluarkan bunyi instrumen:
                                <ul style="list-style-type: circle; padding-left: 1.25rem; margin-top: 0.25rem;">
                                    <li><strong style="color: #10b981;">Ti-ring! (Melodi Ceria Naik - Sol-Do)</strong>: Menandakan absen masuk atau pulang sukses dicatat.</li>
                                    <li><strong style="color: #3b82f6;">Tet-tet! (Nada Datar Ganda)</strong>: Menandakan santri sudah melakukan scan hari ini (mencegah dobel absen).</li>
                                    <li><strong style="color: #ef4444;">Bzaaa! (Buzzer Rendah Kasar)</strong>: Menandakan nomor kartu tidak terdaftar di database sekolah.</li>
                                </ul>
                            </li>
                            <li style="margin-bottom: 0.5rem;"><strong>Mode Kamera QR</strong>:<br> Digunakan jika memindai kode QR menggunakan kamera ponsel atau webcam. Pada mode ini, pengunci kursor otomatis dimatikan sehingga wali kelas bisa leluasa memilih kamera depan/belakang di HP tanpa gangguan fokus.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 4: BATAS WAKTU & TERLAMBAT -->
            <div x-show="(activeTab === 'all' || activeTab === 'scanner') && (search === '' || match('batas') || match('waktu') || match('terlambat') || match('toleransi') || match('cooldown') || match('absen'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Aturan Batas Waktu Absensi, Toleransi Terlambat, & Cooldown
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Sistem memiliki mesin validasi waktu yang sangat presisi untuk menyaring kedatangan santri:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Logika Batas Terlambat</strong>:<br> Jika jendela masuk diatur pukul <code class="code-block">06:00</code> s.d <code class="code-block">10:00</code>, dan batas terlambat diatur pukul <code class="code-block">07:00</code>:
                                <ul style="list-style-type: circle; padding-left: 1.25rem; margin-top: 0.25rem;">
                                    <li>Scan pukul <strong>06:00 - 07:00</strong>: Terbaca sukses dengan status <strong>Tepat Waktu</strong> (Notifikasi Hijau).</li>
                                    <li>Scan pukul <strong>07:01 - 10:00</strong>: Terbaca sukses namun dicatat status <strong>Terlambat</strong> (Notifikasi Merah).</li>
                                    <li>Scan pukul <strong>10:01 ke atas</strong>: Sistem otomatis menolak scan masuk karena jendela sudah ditutup.</li>
                                </ul>
                            </li>
                            <li style="margin-bottom: 0.5rem;"><strong>Proteksi Jeda Cooldown 3 Detik</strong>:<br> Untuk mencegah pengiriman data beruntun (spamming) saat santri menaruh kartunya terlalu lama di depan alat scan/kamera, sistem akan mengunci pembacaan kartu yang sama selama 3 detik sebelum siap memindai kartu berikutnya.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 5: OTOMASI ALPA & WHATSAPP -->
            <div x-show="(activeTab === 'all' || activeTab === 'wa') && (search === '' || match('wa') || match('whatsapp') || match('sidobe') || match('alpa') || match('cron') || match('otomatis') || match('rekap'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Sistem Otomasi Alpa & Integrasi WhatsApp Gateway (Sidobe)
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Sistem ini terhubung penuh dengan platform gerbang WhatsApp <strong>Sidobe.com</strong> untuk mengirimkan laporan harian otomatis:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Mekanisme Pencatatan Alpa Otomatis</strong>:<br> Cron Job Hostinger memantau server setiap 10 menit. Begitu jam server melewati batas jam tutup absen masuk yang Anda atur di menu Integrasi WA, sistem otomatis menyisir santri yang membolos, mencatatnya sebagai <strong>"Alpa"</strong>, dan merangkum rekapnya untuk dikirimkan secara serentak ke HP ustadz admin.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Filter Hari Libur & Jumat Otomatis</strong>:<br> Sistem otomatis mengabaikan proses Alpa harian pada hari <strong>Minggu/Jumat</strong> (libur mingguan) serta hari-hari libur nasional yang Anda daftarkan di menu <em class="highlight-text">Kalender Libur</em>.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Target Penerima Laporan</strong>:<br> Laporan dikirim ke seluruh Guru yang memiliki akun login ber-role <strong>"super_admin"</strong> (Nomor WA diambil dari kolom `No. HP` di dalam menu Data Guru).</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 6: EDIT MANUAL & OPSI B -->
            <div x-show="(activeTab === 'all' || activeTab === 'wa') && (search === '' || match('manual') || match('absen') || match('edit') || match('izin') || match('sakit') || match('alpa') || match('opsi b'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Panduan Koreksi Absensi Manual & Input Izin / Sakit (Opsi B)
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Bagaimana jika ada surat izin/sakit menyusul setelah proses Alpa harian terlanjur dijalankan sistem?</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Mengubah Status Alpa</strong>:<br> Wali kelas/admin cukup masuk ke menu <em class="highlight-text">Rekap Absensi</em>, cari nama siswa hari itu, klik tombol <strong>Edit</strong> (Modal popup), lalu ubah kolom Status dari "Alpa" menjadi "Sakit" atau "Izin". Angka di database otomatis ter-update dan grafik di Dashboard ikut menyesuaikan.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Input Manual Pengganti Kartu Rusak</strong>:<br> Jika kartu santri rusak/hilang, klik tombol biru <strong>"Input Absen Manual"</strong> di kanan atas halaman Rekap Absensi untuk memasukkan kehadirannya secara mandiri tanpa scan kartu.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 7: EKSPOR SPREADSHEET MATRIX -->
            <div x-show="(activeTab === 'all' || activeTab === 'report') && (search === '' || match('ekspor') || match('excel') || match('sheet') || match('matrix') || match('bulan') || match('rekap'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Panduan Ekspor Laporan Bulanan (Per Kelas & Semua Kelas Multi-Sheet)
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Sistem ini dirancang untuk mempermudah cetak laporan absensi bulanan model matriks kelas berstandar tinggi:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Unduh Laporan Per Kelas</strong>:<br> Buka menu <em class="highlight-text">Data Kelas</em> -> klik tombol mata (<strong>View</strong>) pada kelas tertentu -> klik tombol hijau <strong>"Ekspor Absensi Bulanan"</strong> di kanan atas halaman detail.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Unduh Laporan Semua Kelas Sekaligus (Multi-Sheet)</strong>:<br> Buka menu <em class="highlight-text">Rekap Absensi</em> -> klik tombol hijau <strong>"Ekspor Semua Kelas"</strong>. Anda akan mengunduh satu file Excel di mana <strong>setiap kelas otomatis terpecah menjadi tab-sheet di bagian bawah Excel</strong>.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Penyaringan Bulan Aktif</strong>:<br> Dropdown bulan dan tahun pada form ekspor hanya akan memunculkan bulan/tahun di mana santri <strong>benar-benar pernah melakukan scan</strong>, sehingga menjamin file laporan tidak kosong.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 8: LEGENDA WARNA EXCEL -->
            <div x-show="(activeTab === 'all' || activeTab === 'report') && (search === '' || match('warna') || match('legenda') || match('sel') || match('indikator') || match('jumat') || match('libur'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Legenda Warna & Indikator Sel Laporan Absensi Excel
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Ketika file laporan dibuka di Microsoft Excel, sel-sel absensi akan diwarnai secara otomatis dengan sangat rapi dan presisi untuk memudahkan pemantauan:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>H (Hadir) - Latar Hijau</strong>: Siswa hadir di kelas (Tepat Waktu maupun Terlambat).</li>
                            <li style="margin-bottom: 0.5rem;"><strong>S (Sakit) - Latar Biru</strong>: Siswa tidak hadir dengan keterangan Sakit.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>I (Izin) - Latar Abu-abu Terang</strong>: Siswa tidak hadir dengan keterangan Izin resmi.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>A (Alpa) - Latar Merah</strong>: Siswa bolos sekolah/tanpa keterangan.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>L (Libur) - Latar Abu-abu Gelap</strong>: Hari libur (Otomatis hari Jumat atau hari libur nasional di kalender).</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Kalkulasi Paling Kanan</strong>: Excel akan menghitung otomatis jumlah total <strong>S, I, dan A</strong> masing-masing siswa pada kolom paling kanan secara akurat.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

            <!-- TOPIK 9: AKSES WALI KELAS & MULTITENANCY -->
            <div x-show="(activeTab === 'all' || activeTab === 'security') && (search === '' || match('wali') || match('kelas') || match('akses') || match('scoping') || match('multitenancy') || match('pembatasan'))" class="custom-card">
                <x-filament::section>
                    <x-slot name="heading">
                        Sistem Pembatasan Akses Wali Kelas (Data Scoping)
                    </x-slot>
                    <div class="custom-markdown" style="font-size: 0.925rem; line-height: 1.6; color: #374151;">
                        <p>Aplikasi ini memiliki sistem pembatasan akses data (<em>Data Scoping</em>) tingkat lanjut untuk menjaga kerahasiaan data kelas:</p>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; margin-top: 0.5rem;">
                            <li style="margin-bottom: 0.5rem;"><strong>Bagaimana Wali Kelas Melihat Data?</strong>:<br> Ketika seorang ustadz ber-role <strong>"Wali Kelas"</strong> login, sistem secara otomatis mendeteksi kelas yang dia ampu melalui relasi polymorphic `userable`.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Pembatasan Akses</strong>:<br> Wali kelas tersebut <strong>hanya diperbolehkan</strong> melihat daftar siswa dan log rekap absensi khusus dari kelas yang dia ampu saja. Dia diblokir dari melihat data santri di kelas lain.</li>
                            <li style="margin-bottom: 0.5rem;"><strong>Akses Super Admin</strong>:<br> Khusus untuk akun ber-role <strong>"super_admin"</strong> (kepala sekolah/staf IT), pembatasan ini dilewati sehingga bisa melihat seluruh data kelas dan mengedit semua log secara penuh tanpa batas.</li>
                        </ul>
                    </div>
                </x-filament::section>
            </div>

        </div>

        <!-- TIMPAAN CSS KHUSUS UNTUK DARK MODE & MARGINS (Bebas Kompilasi) -->
        <style>
            .custom-markdown p { margin-bottom: 0.75rem; }
            .custom-markdown strong { font-weight: bold;}
            .custom-markdown li { margin-bottom: 0.5rem; }
            .highlight-text { color: #0d8d09; font-style: normal; font-weight: bold; }
            .code-block { background-color: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 0.85rem; color: #b91c1c; }

            /* Kerapian Tab Button Kustom */
            .custom-tab-btn {
                background: none;
                border: none;
                padding: 0.65rem 1.15rem;
                font-size: 0.875rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s;
                color: #6b7280;
                border-bottom: 2px solid transparent;
            }
            .custom-tab-btn:hover {
                color: #0d8d09;
            }

            /* Skenario Penyesuaian Dark Mode */
            .dark .custom-markdown {
                color: #d1d5db !important;
            }
            .dark .custom-markdown strong {
                color: #21c97a !important;
            }
            .dark .highlight-text {
                color: #21c97a !important;
            }
            .dark .code-block {
                background-color: #1f2937 !important;
                color: #f87171 !important;
            }
            .dark .custom-tab-btn {
                color: #9ca3af;
            }
            .dark .custom-tab-btn:hover {
                color: #21c97a;
            }
        </style>

    </div>
</x-filament-panels::page>
