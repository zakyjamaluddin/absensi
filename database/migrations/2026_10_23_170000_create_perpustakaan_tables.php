<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Buku
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku')->unique()->index(); // Barcode ISBN / Kode Unik Perpustakaan
            $table->string('judul');
            $table->string('penulis')->nullable();
            $table->string('penerbit')->nullable();
            $table->string('tahun_terbit', 10)->nullable();
            $table->string('kategori')->nullable(); // Misal: Pelajaran, Agama, Fiksi, Sains, Sejarah
            $table->integer('stok')->default(1);
            $table->integer('tersedia')->default(1);
            $table->string('rak')->nullable(); // Lokasi rak buku
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Pengaturan Perpustakaan
        Schema::create('perpustakaan_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('lama_pinjam_default')->default(7); // Maksimal pengembalian default (hari)
            $table->integer('poin_kunjungan')->default(5);      // Poin literasi per kunjungan
            $table->integer('poin_peminjaman')->default(15);    // Poin literasi per buku dipinjam
            $table->timestamps();
        });

        // 3. Tabel Kunjungan Perpustakaan (Presensi Masuk Perpustakaan via RFID)
        Schema::create('kunjungan_perpustakaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->time('waktu_masuk');
            $table->string('keperluan')->default('Membaca / Belajar');
            $table->timestamps();
        });

        // 4. Tabel Peminjaman Buku
        Schema::create('peminjaman_bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman')->unique()->index();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('buku_id')->constrained('bukus')->cascadeOnDelete();
            $table->date('tanggal_pinjam')->index();
            $table->date('tanggal_jatuh_tempo')->index(); // Maksimal pengembalian
            $table->date('tanggal_kembali')->nullable()->index();
            $table->enum('status', ['Dipinjam', 'Dikembalikan', 'Terlambat'])->default('Dipinjam');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_bukus');
        Schema::dropIfExists('kunjungan_perpustakaans');
        Schema::dropIfExists('perpustakaan_settings');
        Schema::dropIfExists('bukus');
    }
};
