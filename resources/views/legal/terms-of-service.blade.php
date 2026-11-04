<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ketentuan Layanan - Absaka</title>
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
                <a href="{{ route('legal.privacy') }}" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition">Kebijakan Privasi</a>
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
                    Syarat & Ketentuan Penggunaan Resmi
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Ketentuan Layanan (Terms of Service)</h1>
                <p class="text-sm text-slate-500 mt-2">Terakhir diperbarui: 30 Oktober 2026 • Berlaku untuk seluruh pengguna aplikasi Absaka</p>
            </div>

            <!-- Content Body -->
            <div class="space-y-8 text-slate-700 text-sm sm:text-base leading-relaxed">
                
                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">1.</span> Penerimaan Ketentuan
                    </h2>
                    <p>
                        Dengan mengakses, mendaftar, atau menggunakan aplikasi <strong>Absaka</strong>, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat oleh Ketentuan Layanan ini. Jika Anda tidak menyetujui salah satu poin dalam ketentuan ini, Anda tidak diperkenankan menggunakan aplikasi Absaka.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">2.</span> Deskripsi Layanan
                    </h2>
                    <p class="mb-3">Absaka adalah platform digital internal sekolah yang menyediakan layanan:</p>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600">
                        <li>Pencatatan presensi siswa dan guru secara digital menggunakan kartu RFID atau pemindaian QR Code.</li>
                        <li>Pengiriman notifikasi otomatis kehadiran, kepulangan, dan status alpa ke WhatsApp orang tua/wali murid.</li>
                        <li>Pengelolaan perizinan dan sakit online dengan mekanisme persetujuan oleh Guru Piket harian.</li>
                        <li>Sistem informasi perpustakaan sekolah (katalog buku, sirkulasi peminjaman, log kunjungan, dan cetak label barcode).</li>
                        <li>Pemantauan peringkat kedisiplinan dan minat literasi siswa.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">3.</span> Akun Pengguna & Keamanan Kredensial
                    </h2>
                    <ul class="list-disc pl-6 space-y-2 text-slate-600">
                        <li>Setiap pengguna wajib memberikan informasi identitas yang benar, akurat, dan sesuai dengan data administrasi sekolah.</li>
                        <li>Pengguna bertanggung jawab penuh dalam menjaga kerahasiaan kata sandi atau akun Google yang terhubung ke sistem.</li>
                        <li>Dilarang memindahtangankan akun atau kartu fisik presensi (RFID / QR Code) kepada orang lain untuk tujuan penitipan absen (*titip absen*). Tindakan tersebut merupakan pelanggaran disiplin sekolah.</li>
                        <li>Pihak sekolah berhak menonaktifkan atau membatasi hak akses akun yang terindikasi melakukan penyalahgunaan.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">4.</span> Penggunaan Layanan Google OAuth
                    </h2>
                    <p>
                        Fitur login dengan Google disediakan semata-mata untuk mempermudah otentikasi identitas internal. Pendaftaran akun Google hanya akan berhasil jika pengguna dapat memverifikasi Nomor Induk (NIS/NIP) yang terdaftar secara sah di pangkalan data sekolah.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">5.</span> Tata Tertib Sirkulasi Perpustakaan
                    </h2>
                    <p class="mb-3">Bagi pengguna modul perpustakaan digital:</p>
                    <ul class="list-disc pl-6 space-y-1.5 text-slate-600">
                        <li>Peminjam buku wajib menjaga keutuhan dan kebersihan buku yang dipinjam.</li>
                        <li>Buku harus dikembalikan tepat waktu sesuai batas durasi peminjaman default yang berlaku.</li>
                        <li>Keterlambatan pengembalian atau kerusakan buku akan dikenakan sanksi/denda sesuai dengan aturan perpustakaan yang telah ditetapkan oleh pihak sekolah.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">6.</span> Hak Kekayaan Intelektual
                    </h2>
                    <p>
                        Seluruh logo, merek dagang, desain antarmuka, kode sumber, struktur basis data, dan dokumentasi aplikasi Absaka merupakan hak kekayaan intelektual resmi sekolah dan pengembang. Pengguna dilarang menyalin, merekayasa balik (*reverse engineering*), atau mendistribusikan sistem ini tanpa izin tertulis.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">7.</span> Batasan Tanggung Jawab
                    </h2>
                    <p>
                        Pihak sekolah dan pengembang berupaya maksimal menjaga keandalan sistem selama 24/7. Namun demikian, pengembang tidak bertanggung jawab atas keterlambatan pengiriman notifikasi yang disebabkan oleh gangguan jaringan internet seluler, kendala server WhatsApp pihak ketiga, atau pemadaman listrik di luar kendali sistem.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span class="text-emerald-600">8.</span> Perubahan Ketentuan
                    </h2>
                    <p>
                        Ketentuan Layanan ini dapat disesuaikan sewaktu-waktu seiring perkembangan regulasi dan kebutuhan operasional sekolah. Pembaruan akan selalu ditampilkan pada halaman ini dengan tanggal revisi terbaru.
                    </p>
                </section>

                <section class="bg-slate-100/70 border border-slate-200 rounded-xl p-5">
                    <h2 class="text-base font-bold text-slate-900 mb-2">Pertanyaan dan Masukan</h2>
                    <p class="text-sm text-slate-700">
                        Apabila Anda memiliki pertanyaan atau kendala seputar Ketentuan Layanan ini, silakan menghubungi administrator IT atau staf tata usaha sekolah.
                    </p>
                </section>

            </div>

            <!-- Footer Navigation inside Card -->
            <div class="mt-10 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Absaka. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('legal.privacy') }}" class="hover:text-emerald-600 font-semibold transition">Kebijakan Privasi</a>
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
