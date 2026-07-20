<div class="flex flex-col items-center justify-center p-4">
    <!-- Area Kartu yang akan Dicetak -->
    <div id="print-area" class="border-2 border-gray-300 rounded-xl p-6 bg-white text-black shadow-md flex flex-col items-center justify-between" style="width: 85.6mm; height: 54mm; font-family: sans-serif; box-sizing: border-box; background-color: #ffffff;">
        <!-- Header Kartu -->
        <div class="text-center w-full" style="border-bottom: 2px solid #1e3a8a; padding-bottom: 4px; margin-bottom: 8px;">
            <h3 style="font-size: 11px; font-weight: bold; color: #1e3a8a; margin: 0; text-transform: uppercase;">Pondok Pesantren</h3>
            <p style="font-size: 8px; color: #6b7280; margin: 2px 0 0 0;">KARTU IDENTITAS ABSENSI</p>
        </div>

        <!-- Konten Tengah: QR Code & Detail -->
        <div style="display: flex; width: 100%; align-items: center; justify-content: space-around;">
            <!-- QR Code (Menggunakan HTML5 Canvas yang di-render oleh Javascript) -->
            <div style="padding: 4px; background: white; border: 1px solid #d1d5db; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                <canvas id="qr-canvas" style="width: 100px; height: 100px;"></canvas>
            </div>

            <!-- Identitas Diri -->
            <div style="text-align: left; font-size: 9px; line-height: 1.4; max-width: 140px;">
                <div style="font-weight: bold; color: #111827; margin-bottom: 4px; font-size: 10px; word-break: break-word;">
                    {{ $record->nama }}
                </div>
                <div style="color: #4b5563;">
                    ID: <strong>{{ $record instanceof \App\Models\Siswa ? $record->nis : $record->nip }}</strong>
                </div>
                @if($record instanceof \App\Models\Siswa)
                    <div style="color: #4b5563;">
                        Kelas: <strong>{{ $record->kelas->nama_kelas ?? '-' }}</strong>
                    </div>
                @else
                    <div style="color: #4b5563;">
                        Jabatan: <strong>Guru / Ustadz</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer Kartu -->
        <div class="text-center w-full" style="margin-top: 6px;">
            <p style="font-size: 7px; color: #9ca3af; margin: 0;">Harap bawa kartu ini setiap kali melakukan scan absen.</p>
        </div>
    </div>

    <!-- Tombol Cetak (Hanya tampil di layar web, tidak ikut tercetak) -->
    <div class="mt-6 flex justify-end w-full no-print">
        <button onclick="printCard()" style="background-color: #1e3a8a; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer;">
            Cetak Kartu
        </button>
    </div>

    <!-- CSS Print Media Query -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                border: none !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Script QR Code Generator Client-Side -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
    <script>
        // Generate QR Code di dalam Canvas HTML5
        var qr = new QRious({
            element: document.getElementById('qr-canvas'),
            value: '{{ $record instanceof \App\Models\Siswa ? $record->nis : $record->nip }}',
            size: 200, // Ukuran resolusi tinggi agar tajam saat dicetak
            level: 'H' // Tingkat koreksi kesalahan tinggi agar mudah ter-scan kamera
        });

        function printCard() {
            window.print();
        }
    </script>
</div>
