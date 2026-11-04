{{-- PWA Install & Push Notification Component --}}
<div id="pwa-install-wrapper" class="fi-pwa-wrapper" style="
    margin-top: auto;
    position: sticky;
    bottom: 0;
    z-index: 20;
    padding: 0.75rem 1rem 1rem 1rem;
    border-top: 1px solid rgba(16, 185, 129, 0.15);
    background: inherit;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
">
    <!-- 1. TOMBOL AKTIFKAN NOTIFIKASI (HILANG JIKA NOTIFIKASI SUDAH AKTIF) -->
    <button
        type="button"
        id="web-notif-btn"
        onclick="requestNotificationPermission()"
        style="
            width: 100%;
            display: none; /* dinilai via JS */
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            padding: 0.65rem 0.85rem;
            margin-bottom: 0.5rem;
            border-radius: 0.625rem;
            border: 1px solid rgba(59, 130, 246, 0.35);
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.14) 0%, rgba(37, 99, 235, 0.10) 100%);
            color: #2563eb;
            font-size: 0.8125rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px -2px rgba(59, 130, 246, 0.2);
            transition: all 0.25s ease;
        "
        class="dark:!text-blue-400 dark:!border-blue-500/40"
        onmouseover="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.25) 0%, rgba(37, 99, 235, 0.20) 100%)'; this.style.borderColor='#3b82f6';"
        onmouseout="this.style.background='linear-gradient(135deg, rgba(59, 130, 246, 0.14) 0%, rgba(37, 99, 235, 0.10) 100%)'; this.style.borderColor='rgba(59, 130, 246, 0.35)';"
    >
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <!-- Icon Lonceng / Notifikasi -->
            <svg style="width: 1.15rem; height: 1.15rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span id="web-notif-label">Aktifkan Notifikasi</span>
        </div>

        <span style="
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.15rem 0.45rem;
            border-radius: 9999px;
            background-color: #2563eb;
            color: #ffffff;
            font-size: 0.625rem;
            font-weight: 800;
            letter-spacing: 0.05em;
        ">
            ALERT
        </span>
    </button>

    <!-- 2. TOMBOL INSTALL APLIKASI (HILANG JIKA SUDAH TERINSTAL) -->
    <button
        type="button"
        id="pwa-install-btn"
        onclick="handlePwaInstallClick()"
        style="
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.6rem;
            padding: 0.7rem 0.9rem;
            border-radius: 0.625rem;
            border: 1px solid rgba(16, 185, 129, 0.35);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.16) 0%, rgba(5, 150, 105, 0.12) 100%);
            color: #10b981;
            font-size: 0.8125rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px -2px rgba(16, 185, 129, 0.2);
            transition: all 0.25s ease;
        "
        onmouseover="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.28) 0%, rgba(5, 150, 105, 0.22) 100%)'; this.style.borderColor='#10b981'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 14px -2px rgba(16, 185, 129, 0.4)';"
        onmouseout="this.style.background='linear-gradient(135deg, rgba(16, 185, 129, 0.16) 0%, rgba(5, 150, 105, 0.12) 100%)'; this.style.borderColor='rgba(16, 185, 129, 0.35)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px -2px rgba(16, 185, 129, 0.2)';"
    >
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <!-- Icon Download/Install Device -->
            <svg style="width: 1.15rem; height: 1.15rem; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span id="pwa-install-label">Install Aplikasi Ini</span>
        </div>

        <span style="
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.15rem 0.45rem;
            border-radius: 9999px;
            background-color: #10b981;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.05em;
        ">
            <span style="width: 0.35rem; height: 0.35rem; border-radius: 9999px; background-color: #ffffff; display: inline-block;"></span>
            PWA
        </span>
    </button>
</div>

<!-- MODAL PANDUAN INSTALASI (IOS & DESKTOP HELP) -->
<div id="pwa-guide-modal" style="
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background-color: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
">
    <div style="
        position: relative;
        width: 100%;
        max-width: 440px;
        border-radius: 1rem;
        background: #ffffff;
        color: #1f2937;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(16, 185, 129, 0.3);
        overflow: hidden;
    " class="dark:bg-slate-900 dark:text-slate-100">

        <!-- HEADER MODAL -->
        <div style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid rgba(16, 185, 129, 0.15);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.08) 100%);
        ">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <span style="font-size: 1.35rem;" id="pwa-modal-device-icon">📱</span>
                <div>
                    <h3 id="pwa-modal-title" style="margin: 0; font-size: 1rem; font-weight: 800; color: #10b981;">
                        Pasang Aplikasi di Layar Utama
                    </h3>
                    <p style="margin: 0; font-size: 0.75rem; opacity: 0.75;">Absaka - Absensi & Perpustakaan Digital</p>
                </div>
            </div>

            <button
                type="button"
                onclick="closePwaModal()"
                style="
                    background: transparent;
                    border: none;
                    cursor: pointer;
                    color: inherit;
                    opacity: 0.6;
                    padding: 0.25rem;
                    border-radius: 0.375rem;
                "
                onmouseover="this.style.opacity='1';"
                onmouseout="this.style.opacity='0.6';"
            >
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- ISI PANDUAN -->
        <div id="pwa-modal-content" style="padding: 1.25rem;">
            <!-- Konten dinamis disuntikkan oleh JS -->
        </div>

        <!-- FOOTER MODAL -->
        <div style="
            display: flex;
            justify-content: flex-end;
            padding: 0.85rem 1.25rem;
            border-top: 1px solid rgba(16, 185, 129, 0.12);
            background: rgba(0, 0, 0, 0.02);
        ">
            <button
                type="button"
                onclick="closePwaModal()"
                style="
                    padding: 0.55rem 1.25rem;
                    border-radius: 0.5rem;
                    background: #10b981;
                    color: #ffffff;
                    border: none;
                    font-size: 0.8125rem;
                    font-weight: 700;
                    cursor: pointer;
                    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
                "
            >
                Mengerti
            </button>
        </div>
    </div>
</div>

<!-- LOGIKA JAVASCRIPT PWA & PUSH NOTIFICATION -->
<script>
    let deferredPwaPrompt = null;

    // 1. Registrasi Service Worker PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register("{{ asset('sw.js') }}")
                .then(reg => {
                    console.log('PWA ServiceWorker registered with scope:', reg.scope);
                })
                .catch(err => {
                    console.warn('PWA ServiceWorker registration error:', err);
                });
        });
    }

    // 2. Tangkap event prompt instalasi
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPwaPrompt = e;
        updateSidebarButtonsUi();
    });

    // 3. Tangkap event saat aplikasi telah berhasil diinstal
    window.addEventListener('appinstalled', () => {
        deferredPwaPrompt = null;
        updateSidebarButtonsUi();
    });

    // 4. Periksa apakah aplikasi sedang berjalan dalam mode standalone (sudah terinstal)
    function isRunningStandalone() {
        return (window.matchMedia('(display-mode: standalone)').matches) ||
               (window.navigator.standalone === true) ||
               (document.referrer.includes('android-app://'));
    }

    // 5. Deteksi platform iOS (Safari)
    function isIOSDevice() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    // 6. Update tampilan tombol Install & tombol Notifikasi
    function updateSidebarButtonsUi() {
        const wrapper = document.getElementById('pwa-install-wrapper');
        const installBtn = document.getElementById('pwa-install-btn');
        const notifBtn = document.getElementById('web-notif-btn');

        if (!wrapper) return;

        let hasVisibleButton = false;

        // A. Cek Tombol Notifikasi (Hilang jika notifikasi sudah aktif / granted)
        if (notifBtn) {
            if ('Notification' in window && Notification.permission !== 'granted') {
                notifBtn.style.display = 'flex';
                hasVisibleButton = true;
            } else {
                notifBtn.style.display = 'none';
            }
        }

        // B. Cek Tombol Install (Hilang total jika aplikasi sudah terinstall / standalone)
        if (installBtn) {
            if (isRunningStandalone()) {
                installBtn.style.display = 'none';
            } else {
                installBtn.style.display = 'flex';
                hasVisibleButton = true;

                const label = document.getElementById('pwa-install-label');
                if (label && isIOSDevice()) {
                    label.innerText = 'Tambahkan ke Layar Utama';
                }
            }
        }

        // Jika kedua tombol sudah hilang, sembunyikan seluruh wrapper
        wrapper.style.display = hasVisibleButton ? 'block' : 'none';
    }

    // Inisialisasi status tombol saat DOM siap
    document.addEventListener('DOMContentLoaded', updateSidebarButtonsUi);
    document.addEventListener('livewire:navigated', updateSidebarButtonsUi);

    // 7. Helper terpadu untuk memicu notifikasi native (mendukung Mobile ServiceWorker & Desktop)
    function fireNativeNotification(title, body, url) {
        const options = {
            body: body || '',
            icon: '{{ asset("icons/icon-192x192.png") }}',
            badge: '{{ asset("icons/icon-192x192.png") }}',
            vibrate: [200, 100, 200],
            data: { url: url || '{{ url("/admin") }}' }
        };

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(reg => {
                reg.showNotification(title, options);
            }).catch(() => {
                try {
                    const notif = new Notification(title, options);
                    notif.onclick = function() {
                        window.focus();
                        if (url) window.location.href = url;
                    };
                } catch (e) {
                    console.debug('Notification fallback failed:', e);
                }
            });
        } else if ('Notification' in window) {
            try {
                const notif = new Notification(title, options);
                notif.onclick = function() {
                    window.focus();
                    if (url) window.location.href = url;
                };
            } catch (e) {
                console.debug('Notification fallback failed:', e);
            }
        }
    }

    // 8. Handler saat tombol Aktifkan Notifikasi diklik
    function requestNotificationPermission() {
        if (!('Notification' in window)) {
            alert('Peramban web ini tidak mendukung fitur Web Notification.');
            return;
        }

        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                updateSidebarButtonsUi();
                fireNativeNotification(
                    '🔔 Notifikasi Absaka Aktif',
                    'Fitur notifikasi real-time berhasil diaktifkan pada perangkat ini.',
                    '{{ url("/admin") }}'
                );
                pollNotifications();
            } else if (permission === 'denied') {
                alert('Izin notifikasi diblokir di browser. Buka setelan browser situs ini untuk mengaktifkannya.');
            }
        });
    }

    // 9. Polling pemeriksaan notifikasi berkala dari server
    function pollNotifications() {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;

        fetch('{{ route("api.notifications.check") }}')
            .then(res => res.json())
            .then(data => {
                if (data && data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(item => {
                        fireNativeNotification(item.title, item.body, item.url);
                    });
                }
            })
            .catch(err => console.debug('Notif poll error:', err));
    }

    // Jalankan polling setiap 30 detik & 2 detik awal
    setInterval(pollNotifications, 30000);
    setTimeout(pollNotifications, 2000);

    // 10. Handler saat tombol install diklik
    function handlePwaInstallClick() {
        if (isIOSDevice()) {
            showIosInstallGuide();
            return;
        }

        if (deferredPwaPrompt) {
            deferredPwaPrompt.prompt();
            deferredPwaPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User mengonfirmasi pemasangan PWA');
                }
                deferredPwaPrompt = null;
                updateSidebarButtonsUi();
            });
            return;
        }

        showGenericInstallGuide();
    }

    function showIosInstallGuide() {
        const modal = document.getElementById('pwa-guide-modal');
        const title = document.getElementById('pwa-modal-title');
        const icon = document.getElementById('pwa-modal-device-icon');
        const content = document.getElementById('pwa-modal-content');

        if (!modal || !content) return;

        icon.innerText = '🍎';
        title.innerText = 'Pasang di Layar Utama iPhone / iPad';

        content.innerHTML = `
            <div style="font-size: 0.875rem; line-height: 1.6; display: flex; flex-direction: column; gap: 0.85rem;">
                <p style="margin: 0; opacity: 0.9;">
                    Untuk memasang aplikasi ini di iPhone/iPad Anda agar dapat dibuka seperti aplikasi native:
                </p>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08);">
                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">1</div>
                    <div>
                        Ketuk tombol <strong>Bagikan (Share)</strong> di bilah menu bawah browser Safari.
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08);">
                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">2</div>
                    <div>
                        Gulir ke bawah dan pilih <strong>"Tambahkan ke Layar Utama" (Add to Home Screen)</strong>.
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08);">
                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">3</div>
                    <div>
                        Ketuk <strong>"Tambah" (Add)</strong> di pojok kanan atas. Ikon aplikasi akan muncul di beranda iOS Anda!
                    </div>
                </div>
            </div>
        `;

        modal.style.display = 'flex';
    }

    function showGenericInstallGuide() {
        const modal = document.getElementById('pwa-guide-modal');
        const title = document.getElementById('pwa-modal-title');
        const icon = document.getElementById('pwa-modal-device-icon');
        const content = document.getElementById('pwa-modal-content');

        if (!modal || !content) return;

        icon.innerText = '💻';
        title.innerText = 'Pasang di Desktop / Windows';

        content.innerHTML = `
            <div style="font-size: 0.875rem; line-height: 1.6; display: flex; flex-direction: column; gap: 0.85rem;">
                <p style="margin: 0; opacity: 0.9;">
                    Aplikasi ini dapat diinstal langsung ke desktop Windows Anda menggunakan browser Google Chrome atau Microsoft Edge:
                </p>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08);">
                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">1</div>
                    <div>
                        Perhatikan di <strong>Address Bar (bilah alamat atas)</strong> sebelah kanan, klik ikon <strong>Install / Komputer</strong> 📥.
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08);">
                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">2</div>
                    <div>
                        Atau klik menu <strong>(Titik Tiga ⋮)</strong> di pojok kanan atas browser > pilih <strong>"Instal Absensi Digital..."</strong>.
                    </div>
                </div>

                <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.65rem; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.08);">
                    <div style="width: 1.75rem; height: 1.75rem; border-radius: 9999px; background: #10b981; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem; flex-shrink: 0;">3</div>
                    <div>
                        Klik <strong>"Instal"</strong>. Aplikasi akan terbuka di jendela mandiri dan pintasan muncul di Desktop & Start Menu Windows!
                    </div>
                </div>
            </div>
        `;

        modal.style.display = 'flex';
    }

    function closePwaModal() {
        const modal = document.getElementById('pwa-guide-modal');
        if (modal) modal.style.display = 'none';
    }

    window.addEventListener('click', (e) => {
        const modal = document.getElementById('pwa-guide-modal');
        if (modal && e.target === modal) {
            closePwaModal();
        }
    });
</script>
