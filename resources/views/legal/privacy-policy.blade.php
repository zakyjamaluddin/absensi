<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Absaka</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('logo-navbar.png') }}" alt="Absaka Logo" class="h-9 w-auto">
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('legal.terms') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition">Ketentuan Layanan</a>
                <a href="{{ url('/admin/login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                    Masuk ke Sistem
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 flex-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-10">
            
            <!-- Title Header -->
            <div class="border-b border-slate-100 pb-6 mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Dokumen Resmi Perlindungan Data
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kebijakan Privasi (Privacy Policy)</h1>
                <p class="text-sm text-slate-500 mt-2">Terakhir diperbarui: 30 Oktober 2026 • Berlaku untuk seluruh pengguna aplikasi Absaka</p>
            </div>

            <!-- Content Body -->
            <div class="space-y-8 text-slate-700 text-sm sm:text-base leading-relaxed">
                
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">1.</span> Pendahuluan
                    </h2>
                    <p>
                        Selamat datang di <strong>Absaka</strong> (Sistem Presensi & Perpustakaan Digital Terintegrasi). Kami sangat menghargai privasi dan keamanan data pribadi seluruh civitas sekolah, termasuk siswa, santri, guru, staf, wali kelas, serta orang tua/wali murid. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi Anda saat menggunakan platform Absaka.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">2.</span> Informasi yang Kami Kumpulkan
                    </h2>
                    <p class="mb-3">Kami mengumpulkan informasi yang diperlukan untuk menyelenggarakan sistem presensi sekolah dan layanan literasi perpustakaan, antara lain:</p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600">
                        <li><strong>Data Identitas Akademik:</strong> Nomor Induk Siswa (NIS), Nomor Induk Pegawai (NIP), nama lengkap, kelas, foto profil, dan status keaktifan.</li>
                        <li><strong>Data Presensi & Kartu:</strong> Log waktu masuk/pulang harian, status kehadiran (Tepat Waktu, Terlambat, Izin, Sakit, Alpa), serta UID kartu RFID / token QR Code.</li>
                        <li><strong>Data Kontak & Notifikasi:</strong> Nomor telepon/WhatsApp guru dan orang tua/wali murid untuk pengiriman laporan presensi otomatis.</li>
                        <li><strong>Data Aktivitas Perpustakaan:</strong> Riwayat peminjaman buku, tenggat waktu pengembalian, status denda (bila ada), dan frekuensi kunjungan perpustakaan.</li>
                        <li><strong>Data Permohonan Izin:</strong> Alasan permohonan izin/sakit, rentang tanggal, serta dokumen bukti pendukung (seperti surat dokter).</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">3.</span> Penggunaan Data Google OAuth (Sign in with Google)
                    </h2>
                    <p class="mb-3">
                        Aplikasi Absaka menyediakan opsi autentikasi <em>"Sign in with Google"</em> untuk mempermudah akses login internal guru dan siswa. Ketika Anda memilih masuk melalui akun Google:
                    </p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600">
                        <li>Kami hanya mengakses informasi profil dasar publik yang Anda setujui, yaitu: <strong>Google ID, Alamat Email, Nama Lengkap, dan Foto Avatar Google</strong>.</li>
                        <li>Kami <strong>TIDAK PERNAH</strong> meminta akses ke Google Drive, Google Kontak, Gmail pribadi, maupun data sensitif lainnya di luar autentikasi dasar.</li>
                        <li>Sebelum akun Google Anda dapat digunakan pertama kali, sistem kami mewajibkan verifikasi Nomor Induk (NIS/NIP) yang valid di pangkalan data internal sekolah untuk memastikan Anda adalah anggota resmi civitas sekolah.</li>
                        <li>Penggunaan data yang diterima dari Google APIs akan sepenuhnya mematuhi <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" class="text-emerald-600 underline font-medium">Google API Services User Data Policy</a>, termasuk ketentuan <em>Limited Use</em>.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">4.</span> Tujuan Penggunaan Informasi
                    </h2>
                    <p class="mb-2">Informasi yang kami kumpulkan dipergunakan secara khusus untuk:</p>
                    <ol class="list-decimal pl-6 space-y-1.5 text-slate-600">
                        <li>Memverifikasi identitas pengguna saat memindai presensi di gerbang sekolah atau meminjam buku di perpustakaan.</li>
                        <li>Mengirimkan notifikasi laporan kehadiran harian dan pengumuman sekolah langsung ke WhatsApp wali murid.</li>
                        <li>Membantu guru piket dan wali kelas memantau ketertiban siswa serta menyetujui surat izin/sakit.</li>
                        <li>Menyajikan rekapitulasi data kehadiran dan ekspor laporan bulanan bagi pihak administrasi sekolah.</li>
                        <li>Menghitung metrik analitik seperti *Leaderboard Kedisiplinan* dan *Rating Minat Literasi*.</li>
                    </ol>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">5.</span> Perlindungan & Keamanan Data
                    </h2>
                    <p>
                        Kami menerapkan standar pengamanan teknis dan manajerial yang ketat untuk melindungi integritas data Anda. Seluruh kata sandi dienkripsi menggunakan algoritma *Bcrypt*, komunikasi data diamankan dengan enkripsi SSL/TLS, dan hak akses antar peran (*Role-Based Access Control*) dibatasi secara ketat sehingga pengguna hanya dapat mengakses data yang relevan dengan kewenangannya.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">6.</span> Berbagi Data kepada Pihak Ketiga
                    </h2>
                    <p>
                        Kami <strong>tidak pernah menjual, memperdagangkan, atau menyewakan</strong> data pribadi pengguna kepada pihak ketiga mana pun untuk tujuan komersial atau periklanan. Data hanya dibagikan kepada penyedia infrastruktur teknis pendukung (seperti WhatsApp Gateway resmi untuk pengiriman pesan pengumuman) semata-mata demi berjalannya operasional sistem sekolah.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">7.</span> Hak dan Kontrol Pengguna
                    </h2>
                    <p>
                        Pengguna memiliki hak untuk meminta peninjauan, koreksi data yang salah, atau pembaruan nomor kontak wali murid melalui staf administrator absensi sekolah. Pengguna yang ingin memutuskan tautan akun Google mereka dapat menghubungi pihak administrasi untuk dilakukan reset otentikasi.
                    </p>
                </section>

                <section class="bg-emerald-50/70 border border-emerald-200/70 rounded-xl p-5">
                    <h2 class="text-base font-bold text-emerald-900 mb-2">Hubungi Pengelola Privasi</h2>
                    <p class="text-sm text-emerald-800">
                        Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini atau pengelolaan data di aplikasi Absaka, silakan hubungi tim administrator internal sekolah melalui bagian administrasi atau kontak resmi sekolah.
                    </p>
                </section>

            </div>

            <!-- Footer Navigation inside Card -->
            <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Absaka. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('legal.terms') }}" class="hover:text-emerald-600 font-semibold transition">Ketentuan Layanan</a>
                    <span>•</span>
                    <a href="{{ url('/admin/login') }}" class="hover:text-emerald-600 font-semibold transition">Halaman Login</a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="py-6 text-center text-xs text-slate-400">
        Absaka - Sistem Presensi & Perpustakaan Digital Sekolah
    </footer>

</body>
</html>
