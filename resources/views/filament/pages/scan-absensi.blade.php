<x-filament-panels::page>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

        <!-- KOLOM KIRI: Bagian Input USB Scanner -->
        <x-filament::section>
            <x-slot name="heading">
                Input Scanner (USB / RFID)
            </x-slot>

            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
                Pastikan kursor aktif di dalam kotak input di bawah ini sebelum menempelkan kartu atau men-scan barcode.
            </p>

            <form wire:submit.prevent="processScan" style="margin-bottom: 1.5rem;">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        id="scannerInput"
                        wire:model="scannedCode"
                        placeholder="Tempel kartu RFID atau scan barcode..."
                        style="text-align: center; font-size: 1.125rem; padding: 0.75rem;"
                        autofocus
                        autocomplete="off"
                    />
                </x-filament::input.wrapper>
            </form>

            @if($message)
                <div style="
                    padding: 1rem;
                    border-radius: 0.5rem;
                    text-align: center;
                    color: white;
                    font-weight: bold;
                    font-size: 1rem;
                    background-color: {{ $status === 'success' ? '#10b981' : ($status === 'danger' ? '#ef4444' : '#3b82f6') }};
                ">
                    {{ $message }}
                </div>
            @endif
        </x-filament::section>

        <!-- KOLOM KANAN: Scanner Kamera Bawaan Device -->
        <x-filament::section>
            <x-slot name="heading">
                Scan Menggunakan Kamera HP / Laptop
            </x-slot>

            <div style="display: flex; justify-content: center; align-items: center; width: 100%;">
                <!-- TAMBAHKAN wire:ignore DI SINI agar Livewire tidak merusak stream kamera -->
                <div id="reader" wire:ignore style="
                    width: 100%;
                    max-width: 350px;
                    border-radius: 0.75rem;
                    overflow: hidden;
                    border: 1px solid #d1d5db;
                "></div>
            </div>
        </x-filament::section>

    </div>

    <!-- Script Javascript untuk Autofocus USB & Integrasi Kamera QR -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('scannerInput');

            // Jaga agar input USB tetap fokus secara otomatis
            document.addEventListener('click', () => {
                input.focus();
            });

            window.addEventListener('play-sound', event => {
                const type = event.detail.type;
                playBeep(type);
            });



            // FUNGSI SINTESIS SUARA INSTRUMEN (Versi Upgrade Melodi)
            function playBeep(type) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;

                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'success') {
                    // NADA PERTAMA: G5 (784Hz) - Lembut & Cepat (Sol)
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(784, ctx.currentTime);
                    gain.gain.setValueAtTime(0.06, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.08);

                    // NADA KEDUA: C6 (1046Hz) - Berbunyi setelah 80ms (Do Tinggi)
                    // Membentuk melodi ceria naik "Ti-ring!" yang sangat khas sukses
                    setTimeout(() => {
                        const ctx2 = new AudioContext();
                        const osc2 = ctx2.createOscillator();
                        const gain2 = ctx2.createGain();
                        osc2.connect(gain2);
                        gain2.connect(ctx2.destination);

                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(1046, ctx2.currentTime);
                        gain2.gain.setValueAtTime(0.06, ctx2.currentTime);
                        gain2.gain.exponentialRampToValueAtTime(0.001, ctx2.currentTime + 0.2);

                        osc2.start(ctx2.currentTime);
                        osc2.stop(ctx2.currentTime + 0.2);
                    }, 80);
                }

                else if (type === 'danger') {
                    // Bunyi "BUZZER" gagal bernada rendah, kasar, dan tegas
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(140, ctx.currentTime);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.45);
                }

                else if (type === 'info') {
                    // Bunyi ganda cepat "TET-TET" datar (Peringatan / Sudah Absen)
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587, ctx.currentTime); // Nada D5
                    gain.gain.setValueAtTime(0.08, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.08);

                    // Beep kedua setelah jeda singkat 120 milidetik
                    setTimeout(() => {
                        const ctx2 = new AudioContext();
                        const osc2 = ctx2.createOscillator();
                        const gain2 = ctx2.createGain();
                        osc2.connect(gain2);
                        gain2.connect(ctx2.destination);

                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(587, ctx2.currentTime);
                        gain2.gain.setValueAtTime(0.08, ctx2.currentTime);
                        gain2.gain.exponentialRampToValueAtTime(0.001, ctx2.currentTime + 0.08);

                        osc2.start(ctx2.currentTime);
                        osc2.stop(ctx2.currentTime + 0.08);
                    }, 120);
                }
            }

            // Setup Camera Scanner menggunakan html5-qrcode
            const html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                {
                    fps: 10,
                    qrbox: { width: 220, height: 220 },
                    aspectRatio: 1.0
                },
                /* verbose= */ false
            );

            // Variable flag untuk mendeteksi masa jeda (cooldown) scan
            let isCooldown = false;

            function onScanSuccess(decodedText, decodedResult) {
                // Jika sistem sedang dalam masa jeda 3 detik, abaikan scan kamera
                if (isCooldown) {
                    return;
                }

                // Aktifkan masa jeda agar tidak terjadi scan beruntun dari kartu yang sama
                isCooldown = true;

                input.value = decodedText;

                // Kirim data ke Livewire backend
                @this.set('scannedCode', decodedText);
                @this.call('processScan');

                // Reset status jeda setelah 3 detik agar kamera siap menerima kartu berikutnya
                setTimeout(() => {
                    isCooldown = false;
                }, 3000);
            }

            html5QrcodeScanner.render(onScanSuccess);
        });
    </script>
</x-filament-panels::page>
