<x-filament-panels::page>
    <!-- TAB TOMBOL PILIHAN MODE (RFID vs KAMERA) -->
    <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 1rem;" class="no-print">
        <button id="btn-rfid" onclick="switchMode('rfid')" style="
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: bold;
            font-size: 0.875rem;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background-color: #1e3a8a;
            color: white;
            transition: all 0.2s;
        ">
            <!-- Icon Card/RFID -->
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
            Mode Kartu / RFID
        </button>

        <button id="btn-camera" onclick="switchMode('camera')" style="
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: bold;
            font-size: 0.875rem;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            color: #374151;
            transition: all 0.2s;
        ">
            <!-- Icon Kamera -->
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Mode Kamera QR
        </button>
    </div>

    <!-- AREA KONTEN UTAMA (Ditampilkan memusat & terfokus) -->
    <div style="max-width: 600px; margin: 0 auto; width: 100%;">

        <!-- SECTION 1: RFID SCANNER (Aktif Pertama Kali) -->
        <div id="section-rfid" style="display: block;">
            <x-filament::section>
                <x-slot name="heading">
                    Input Scanner (USB / RFID)
                </x-slot>

                <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; text-align: center;">
                    Dekatkan Kartu RFID atau arahkan Barcode ke alat scanner Anda.
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
        </div>

        <!-- SECTION 2: KAMERA SCANNER (Awalnya Tersembunyi) -->
        <div id="section-camera" style="display: none;">
            <x-filament::section>
                <x-slot name="heading">
                    Scan Menggunakan Kamera HP / Laptop
                </x-slot>

                <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
                    <!-- Box pembungkus kamera dengan wire:ignore -->
                    <div id="reader" wire:ignore style="
                        width: 100%;
                        max-width: 350px;
                        border-radius: 0.75rem;
                        overflow: hidden;
                        border: 1px solid #d1d5db;
                        background-color: #f3f4f6;
                    "></div>
                </div>
            </x-filament::section>
        </div>

    </div>

    <!-- Script Javascript Toggle Mode & Integrasi Kamera QR -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('scannerInput');

            // Variabel Status Mode Aktif (Default: 'rfid')
            let activeMode = 'rfid';
            let html5QrcodeScanner = null;

            // Fokus otomatis pertama kali dimuat
            setTimeout(() => { input.focus(); }, 150);

            // JAGA AUTOFOCUS HANYA JIKA MODE RFID AKTIF!
            document.addEventListener('click', () => {
                if (activeMode === 'rfid') {
                    input.focus();
                }
            });

            // LOGIKA SAKTI TOGGLE SWITCH MODE
            window.switchMode = function(mode) {
                activeMode = mode;

                const btnRfid = document.getElementById('btn-rfid');
                const btnCamera = document.getElementById('btn-camera');
                const secRfid = document.getElementById('section-rfid');
                const secCamera = document.getElementById('section-camera');

                if (mode === 'rfid') {
                    // Tampilkan RFID, Sembunyikan Kamera
                    secRfid.style.display = 'block';
                    secCamera.style.display = 'none';

                    // Update Style Tombol
                    btnRfid.style.backgroundColor = '#1e3a8a';
                    btnRfid.style.color = '#ffffff';
                    btnCamera.style.backgroundColor = '#ffffff';
                    btnCamera.style.color = '#374151';

                    // Fokuskan kursor kembali ke input RFID
                    setTimeout(() => { input.focus(); }, 100);

                    // MATIKAN KAMERA TOTAL (Untuk privasi & hemat baterai ustadz)
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear().then(() => {
                            html5QrcodeScanner = null;
                        }).catch(err => console.error(err));
                    }
                } else {
                    // Tampilkan Kamera, Sembunyikan RFID
                    secRfid.style.display = 'none';
                    secCamera.style.display = 'block';

                    // Update Style Tombol
                    btnCamera.style.backgroundColor = '#1e3a8a';
                    btnCamera.style.color = '#ffffff';
                    btnRfid.style.backgroundColor = '#ffffff';
                    btnRfid.style.color = '#374151';

                    // Jalankan Inisialisasi Kamera
                    startCamera();
                }
            }

            // FUNGSI MENYALAKAN KAMERA
            function startCamera() {
                if (html5QrcodeScanner) return; // Mencegah dobel inisialisasi

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    {
                        fps: 10,
                        qrbox: { width: 220, height: 220 },
                        aspectRatio: 1.0,
                        // Menampilkan tombol pergantian kamera depan/belakang bawaan secara native
                        showTorchButtonIfSupported: true
                    },
                    /* verbose= */ false
                );

                let isCooldown = false;

                function onScanSuccess(decodedText, decodedResult) {
                    if (isCooldown) return;
                    isCooldown = true;

                    input.value = decodedText;

                    // Kirim data ke Livewire backend
                    @this.set('scannedCode', decodedText);
                    @this.call('processScan');

                    // Jeda 3 detik agar tidak spam scan
                    setTimeout(() => {
                        isCooldown = false;
                    }, 3000);
                }

                html5QrcodeScanner.render(onScanSuccess);
            }

            // LISTENER SUARA BEAKEND
            window.addEventListener('play-sound', event => {
                const type = event.detail.type;
                playBeep(type);
            });

            // FUNGSI SINTESIS SUARA BEEP
            function playBeep(type) {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;

                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'success') {
                    // Ding-ong Ceria naik (Ti-ring!)
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(784, ctx.currentTime);
                    gain.gain.setValueAtTime(0.06, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.08);

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
                    // Buzzer Kasar rendah (Bzaaa!)
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(140, ctx.currentTime);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.45);
                }

                else if (type === 'info') {
                    // Tet-tet datar cepat (Peringatan)
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587, ctx.currentTime);
                    gain.gain.setValueAtTime(0.08, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);

                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.08);

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
        });
    </script>
</x-filament-panels::page>
