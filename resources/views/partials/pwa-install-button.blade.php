<!-- ========================================================================= -->
<!-- PWA WINDOWS & DESKTOP APP INSTALL BANNER / PROMPT (ASystem ESA Groups)    -->
<!-- ========================================================================= -->
<div id="asystem-pwa-install-banner" 
     class="fixed bottom-5 right-5 z-[99990] max-w-sm w-[calc(100%-2.5rem)] bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-blue-200/80 dark:border-blue-900/60 rounded-2xl p-4 shadow-2xl shadow-blue-900/20 transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none"
     style="display: none;">
    
    <div class="flex items-start gap-3.5">
        <!-- App Icon -->
        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-700 via-primary to-sky-500 p-0.5 shadow-md shadow-blue-500/30 flex-shrink-0 flex items-center justify-center">
            <img src="/icons/icon-192x192.png" alt="ASystem Logo" class="w-full h-full rounded-[10px] object-cover" onerror="this.src='/icons/icon.svg';">
        </div>

        <!-- Info & Copy -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">
                    <i class="fa-brands fa-windows text-[10px]"></i> Windows App
                </span>
                <button type="button" 
                        onclick="dismissPwaInstallBanner()" 
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-all text-xs" 
                        title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <h4 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight mt-1">
                Pasang ASystem di Windows
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mt-0.5 line-clamp-2">
                Akses langsung dari Desktop & Taskbar tanpa perlu membuka browser lagi.
            </p>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 mt-3 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" 
                        onclick="triggerPwaInstall()" 
                        class="flex-1 py-1.5 px-3 rounded-xl bg-gradient-to-r from-blue-700 via-primary to-blue-600 hover:from-blue-600 hover:to-blue-500 text-white font-bold text-xs shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span>Pasang Sekarang</span>
                </button>
                <button type="button" 
                        onclick="dismissPwaInstallBanner()" 
                        class="py-1.5 px-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                    Nanti Saja
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        var deferredPrompt = null;
        var banner = document.getElementById('asystem-pwa-install-banner');

        // Cek apakah aplikasi sudah berjalan dalam mode standalone (sudah terinstall sebagai PWA)
        var isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                           window.navigator.standalone === true || 
                           document.referrer.includes('android-app://');

        if (isStandalone) {
            console.log('[PWA] Aplikasi ASystem berjalan dalam mode Standalone Window.');
            return;
        }

        // Tangkap event sebelum instalasi browser
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            window.asystemDeferredPrompt = e;

            // Tampilkan tombol di topbar jika ada
            var topbarBtn = document.getElementById('pwa-topbar-install-btn');
            if (topbarBtn && !isStandalone) {
                topbarBtn.classList.remove('hidden');
                topbarBtn.classList.add('inline-flex');
            }

            // Cek apakah user baru saja menutup banner dalam 3 hari terakhir
            var dismissedUntil = localStorage.getItem('asystem_pwa_dismissed_until');
            if (dismissedUntil && new Date().getTime() < parseInt(dismissedUntil, 10)) {
                return;
            }

            // Tampilkan banner dengan animasi halus setelah 2 detik
            setTimeout(function() {
                if (banner && !isStandalone) {
                    banner.style.display = 'block';
                    requestAnimationFrame(function() {
                        banner.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
                        banner.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    });
                }
            }, 2000);
        });

        // Handler klik tombol Pasang
        window.triggerPwaInstall = function() {
            var promptEvent = deferredPrompt || window.asystemDeferredPrompt;
            if (!promptEvent) {
                // Fallback instruksi jika browser tidak mendukung direct prompt
                alert("Untuk memasang ASystem di Windows:\n1. Klik ikon install (komputer/tanda panah) di ujung kanan bilah alamat browser Anda (Chrome/Edge).\n2. Atau buka menu browser (titik 3) > Aplikasi > 'Install ASystem'.");
                return;
            }

            promptEvent.prompt();
            promptEvent.userChoice.then(function(choiceResult) {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] Pengguna menyetujui instalasi ASystem.');
                    hideBanner();
                    var topbarBtn = document.getElementById('pwa-topbar-install-btn');
                    if (topbarBtn) {
                        topbarBtn.classList.remove('inline-flex');
                        topbarBtn.classList.add('hidden');
                    }
                } else {
                    console.log('[PWA] Pengguna membatalkan instalasi.');
                }
                deferredPrompt = null;
                window.asystemDeferredPrompt = null;
            });
        };

        // Handler dismiss banner
        window.dismissPwaInstallBanner = function() {
            hideBanner();
            // Tunda prompt selama 3 hari
            var threeDaysLater = new Date().getTime() + (3 * 24 * 60 * 60 * 1000);
            localStorage.setItem('asystem_pwa_dismissed_until', threeDaysLater.toString());
        };

        function hideBanner() {
            if (banner) {
                banner.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                banner.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
                setTimeout(function() {
                    banner.style.display = 'none';
                }, 300);
            }
        }

        // Listener saat aplikasi berhasil diinstall
        window.addEventListener('appinstalled', function(evt) {
            console.log('[PWA] ASystem berhasil dipasang sebagai aplikasi desktop!');
            hideBanner();
            var topbarBtn = document.getElementById('pwa-topbar-install-btn');
            if (topbarBtn) {
                topbarBtn.classList.remove('inline-flex');
                topbarBtn.classList.add('hidden');
            }
            localStorage.setItem('asystem_pwa_installed', 'true');
        });

        // Global manual trigger (bisa dipanggil dari menu navbar/profil)
        window.installAsystemPwa = window.triggerPwaInstall;
    })();
</script>
