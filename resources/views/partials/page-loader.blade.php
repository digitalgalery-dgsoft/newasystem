<!-- ========================================================================= -->
<!-- GLOBAL PAGE LOADER & PROGRESS BAR COMPONENT (ASystem ESA Groups)          -->
<!-- ========================================================================= -->

<!-- 1. Top Glowing Progress Bar -->
<div id="asystem-global-progress" class="fixed top-0 left-0 right-0 h-[3px] z-[999999] pointer-events-none transition-opacity duration-300 opacity-0" style="display: none;">
    <div id="asystem-progress-bar" class="h-full w-0 bg-gradient-to-r from-blue-700 via-primary-600 via-blue-500 to-sky-400 shadow-[0_0_12px_rgba(15,82,186,0.9),0_0_6px_rgba(56,189,248,0.9)] transition-all duration-200 ease-out relative">
        <!-- Glowing Leading Edge / Peg -->
        <div class="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-r from-transparent to-white/60"></div>
        <div class="absolute -right-1.5 -top-1 w-3 h-3 rounded-full bg-sky-300 shadow-[0_0_10px_#38bdf8,0_0_20px_#0F52BA] animate-ping opacity-75"></div>
    </div>
</div>

<!-- 2. Frosted Glass Loading Overlay (for Form Submissions & Long Requests) -->
<div id="asystem-loading-overlay" class="fixed inset-0 z-[999990] flex items-center justify-center bg-slate-900/40 backdrop-blur-[4px] transition-all duration-300 opacity-0 pointer-events-none" style="display: none;">
    <div class="bg-white/95 backdrop-blur-md border border-white/80 shadow-2xl shadow-blue-900/20 rounded-2xl p-6 sm:p-7 flex flex-col items-center gap-4 text-center min-w-[240px] max-w-xs transform scale-95 transition-all duration-300" id="asystem-loading-card">
        <!-- Modern Dual-Ring Glowing Spinner -->
        <div class="relative w-14 h-14 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-4 border-slate-100 border-t-primary border-r-blue-500 animate-spin"></div>
            <div class="absolute inset-1.5 rounded-full border-4 border-slate-100 border-b-sky-400 border-l-primary-500 animate-spin" style="animation-direction: reverse; animation-duration: 0.8s;"></div>
            <div class="w-5 h-5 rounded-lg bg-gradient-to-tr from-primary to-blue-500 flex items-center justify-center text-white text-[10px] font-black shadow-md shadow-primary/30">
                A
            </div>
        </div>
        
        <!-- Text & Message -->
        <div class="space-y-1">
            <h4 id="asystem-loading-title" class="text-sm font-bold text-slate-800 tracking-tight">Memproses Data...</h4>
            <p id="asystem-loading-sub" class="text-xs text-slate-500 font-medium leading-relaxed">Mohon tunggu sebentar, sistem sedang memuat</p>
        </div>

        <!-- Mini Progress Pulsing Indicator -->
        <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
            <div class="h-full bg-gradient-to-r from-primary via-blue-500 to-sky-400 rounded-full animate-asystem-shimmer" style="width: 100%;"></div>
        </div>
    </div>
</div>

<!-- 3. Styles for Smooth Loading Animations & Page Transitions -->
<style>
    /* Smooth Entrance for Page Containers */
    @keyframes asystemFadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .asystem-page-enter {
        animation: asystemFadeIn 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* Shimmer Animation */
    @keyframes asystemShimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .animate-asystem-shimmer {
        animation: asystemShimmer 1.4s ease-in-out infinite;
    }

    /* Spinner rotation utility */
    @keyframes asystemSpin {
        to { transform: rotate(360deg); }
    }
    .asystem-spin {
        animation: asystemSpin 0.75s linear infinite;
    }

    /* Skeleton Loading Placeholder Effect */
    .skeleton-placeholder {
        background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
        background-size: 200% 100%;
        animation: skeletonMove 1.5s infinite;
    }
    @keyframes skeletonMove {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>

<!-- 4. Global Controller Script -->
<script>
    (function () {
        const progressEl = document.getElementById('asystem-global-progress');
        const progressBar = document.getElementById('asystem-progress-bar');
        const overlay = document.getElementById('asystem-loading-overlay');
        const card = document.getElementById('asystem-loading-card');
        const titleEl = document.getElementById('asystem-loading-title');
        const subEl = document.getElementById('asystem-loading-sub');

        let progressTimer = null;
        let currentProgress = 0;

        /**
         * Mulai Progress Bar di bagian atas
         */
        function startProgress() {
            if (!progressEl || !progressBar) return;
            clearInterval(progressTimer);
            currentProgress = 10;
            progressEl.style.display = 'block';
            progressEl.style.opacity = '1';
            progressBar.style.width = '10%';

            progressTimer = setInterval(() => {
                if (currentProgress < 65) {
                    currentProgress += Math.random() * 12;
                } else if (currentProgress < 88) {
                    currentProgress += Math.random() * 4;
                } else if (currentProgress < 95) {
                    currentProgress += 0.5;
                }
                progressBar.style.width = Math.min(currentProgress, 95) + '%';
            }, 180);
        }

        /**
         * Selesaikan Progress Bar & sembunyikan
         */
        function finishProgress() {
            if (!progressEl || !progressBar) return;
            clearInterval(progressTimer);
            progressBar.style.width = '100%';
            setTimeout(() => {
                progressEl.style.opacity = '0';
                setTimeout(() => {
                    progressEl.style.display = 'none';
                    progressBar.style.width = '0%';
                    currentProgress = 0;
                }, 250);
            }, 150);
        }

        /**
         * Tampilkan Full Overlay Loader
         */
        function showOverlay(title = 'Memproses Data...', subtitle = 'Mohon tunggu sebentar, sistem sedang memuat') {
            if (!overlay) return;
            if (titleEl) titleEl.innerText = title;
            if (subEl) subEl.innerText = subtitle;
            overlay.style.display = 'flex';
            // Trigger reflow for CSS transition
            void overlay.offsetWidth;
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            if (card) {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }
            startProgress();
        }

        /**
         * Sembunyikan Full Overlay Loader
         */
        function hideOverlay() {
            if (!overlay) return;
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            if (card) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 300);
            finishProgress();
        }

        // Ekspos ke Global Window agar bisa dipanggil dari manapun
        window.showLoader = showOverlay;
        window.hideLoader = hideOverlay;
        window.startProgressBar = startProgress;
        window.finishProgressBar = finishProgress;

        // Auto-run pada halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', () => {
            finishProgress();

            // Tambahkan kelas fade-in ke container konten utama jika belum ada
            const mainContent = document.querySelector('main');
            if (mainContent && !mainContent.classList.contains('asystem-page-enter')) {
                mainContent.classList.add('asystem-page-enter');
            }
        });

        // Sembunyikan jika kembali lewat tombol Back browser (BFCache)
        window.addEventListener('pageshow', (event) => {
            hideOverlay();
            // Reset semua button state jika ada form yang di-submit
            document.querySelectorAll('button[data-submitting="true"]').forEach(btn => {
                btn.removeAttribute('data-submitting');
                btn.disabled = false;
                if (btn.dataset.originalHtml) {
                    btn.innerHTML = btn.dataset.originalHtml;
                }
            });
        });

        // Intercept semua klik link <a> untuk animasi progress bar otomatis
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;

            // Abaikan link khusus
            const href = link.getAttribute('href');
            if (!href || 
                href.startsWith('#') || 
                href.startsWith('javascript:') || 
                href.startsWith('mailto:') || 
                href.startsWith('tel:') || 
                link.getAttribute('target') === '_blank' || 
                link.hasAttribute('download') || 
                link.hasAttribute('data-no-loader') ||
                e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) {
                return;
            }

            // Pastikan domain sama
            try {
                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin) {
                    // Cek apakah hanya hash pada halaman yang sama
                    if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) {
                        return;
                    }
                    startProgress();
                }
            } catch (err) {
                // Ignore URL parsing errors
            }
        });

        // Intercept semua Form Submit untuk Loading State & Button Spinner
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form || form.hasAttribute('data-no-loader')) return;

            const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
            
            // Periksa apakah form untuk upload file atau pencarian
            const isUpload = form.getAttribute('enctype') === 'multipart/form-data';
            const isSearch = form.getAttribute('method')?.toLowerCase() === 'get';

            if (submitBtn && !submitBtn.disabled) {
                submitBtn.setAttribute('data-submitting', 'true');
                submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                
                // Tambahkan spinner halus pada tombol
                submitBtn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-current mr-2"></i><span>${submitBtn.innerText || 'Memproses...'}</span>`;
                submitBtn.classList.add('opacity-80', 'cursor-wait');
            }

            // Jika form POST / upload, tampilkan modal overlay
            if (!isSearch) {
                const customMsg = form.getAttribute('data-loading-text') || (isUpload ? 'Mengunggah Berkas...' : 'Menyimpan Data...');
                showOverlay(customMsg, 'Mohon tidak menutup jendela sebelum proses selesai.');
            } else {
                // Untuk filter / pencarian GET, gunakan top progress bar yang elegan
                startProgress();
            }
        });

        // Fallback: Pastikan progress bar selesai jika window unload dibatalkan
        window.addEventListener('beforeunload', () => {
            startProgress();
        });
    })();
</script>
