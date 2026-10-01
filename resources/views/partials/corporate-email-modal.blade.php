@if(auth()->check() && auth()->user()->mustUpdateCorporateEmail() && !request()->routeIs('profile.*'))
    <!-- MODAL POP UP WAJIB UPDATE EMAIL CORPORATE (TIDAK BISA DITUTUP) -->
    <div id="corporateEmailModal" 
         class="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6 overflow-y-auto bg-slate-950/85 backdrop-blur-md"
         role="dialog" 
         aria-modal="true" 
         aria-labelledby="corporate-modal-title">
        
        <div class="relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-2xl p-6 sm:p-8 text-center overflow-hidden my-auto animate-asystem-modal">
            <!-- Decorative Glow Background -->
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-rose-500/15 rounded-full blur-3xl pointer-events-none -z-0"></div>
            <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-blue-500/15 rounded-full blur-3xl pointer-events-none -z-0"></div>

            <div class="relative z-10">
                <!-- Badge Penanda Wajib -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-800 mb-4 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>Khusus Karyawan Inhouse Wajib Email Corporate</span>
                </div>

                <!-- Animated Icon -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-tr from-rose-500 via-amber-500 to-indigo-600 flex items-center justify-center text-white shadow-xl shadow-rose-500/20 mx-auto relative mb-4 ring-8 ring-rose-50 dark:ring-rose-950/40">
                    <i class="fa-solid fa-envelope-circle-check text-2xl sm:text-3xl animate-bounce"></i>
                </div>

                <!-- Header Title -->
                <h3 id="corporate-modal-title" class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Pembaruan Email Corporate Wajib
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-md mx-auto">
                    Akun Anda terdaftar sebagai <strong class="text-slate-800 dark:text-slate-200">Karyawan Inhouse</strong>, namun saat ini masih menggunakan alamat email personal non-corporate.
                </p>

                <!-- Current Email Card -->
                <div class="mt-5 p-4 rounded-2xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/60 text-left space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-rose-800 dark:text-rose-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                            <span>Email Akun Saat Ini:</span>
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-200/80 dark:bg-rose-900 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-700">
                            Belum Corporate
                        </span>
                    </div>
                    <div class="font-mono text-xs sm:text-sm font-extrabold text-rose-700 dark:text-rose-300 bg-white dark:bg-slate-900 px-3 py-2 rounded-xl border border-rose-200 dark:border-rose-800 break-all select-all flex items-center gap-2 shadow-xs">
                        <i class="fa-regular fa-envelope text-rose-400"></i>
                        <span>{{ auth()->user()->email }}</span>
                    </div>
                    <p class="text-[11px] text-rose-900/80 dark:text-rose-300/80 leading-relaxed">
                        Sesuai ketentuan tata kelola ESA Groups, <strong>seluruh Karyawan Inhouse WAJIB menggunakan alamat email corporate resmi</strong> untuk login dan operasional sistem.
                    </p>
                </div>

                <!-- Allowed Domains List -->
                <div class="mt-4 text-left space-y-2">
                    <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center justify-between">
                        <span>Domain Corporate Resmi yang Diizinkan:</span>
                        <span class="text-[10px] text-primary font-extrabold">6 Domain Resmi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">1</span>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-100 truncate">{{ '@' }}arina.co.id</div>
                                <div class="text-[10px] text-slate-400 truncate">PT Arina Multikarya</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">2</span>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-100 truncate">{{ '@' }}alvakaryaperkasa.co.id</div>
                                <div class="text-[10px] text-slate-400 truncate">PT Alva Karya Perkasa</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-[10px]">3</span>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-100 truncate">{{ '@' }}anugrahterpercayakerja.co.id</div>
                                <div class="text-[10px] text-slate-400 truncate">PT Anugrah Terpercaya Kerja</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-[10px]">4</span>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-100 truncate">{{ '@' }}abadiberkatodelia.co.id</div>
                                <div class="text-[10px] text-slate-400 truncate">PT Arina Bintang Oetama</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-[10px]">5</span>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-100 truncate">{{ '@' }}anugrahtalentaberkarya.co.id</div>
                                <div class="text-[10px] text-slate-400 truncate">PT Anugrah Tri Berkah</div>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">6</span>
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-slate-800 dark:text-slate-100 truncate">{{ '@' }}asystem.co.id</div>
                                <div class="text-[10px] text-slate-400 truncate">ASystem Portal Corporate</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call To Action Buttons -->
                <div class="mt-6 space-y-2.5">
                    <a href="{{ route('profile.index') }}" 
                       class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-600 via-primary-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-sm sm:text-base shadow-xl shadow-primary/25 hover:shadow-primary/40 transition-all flex items-center justify-center gap-2.5 group">
                        <i class="fa-solid fa-user-pen text-base group-hover:scale-110 transition-transform"></i>
                        <span>Update Email Corporate Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1.5 transition-transform"></i>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="text-center pt-1">
                        @csrf
                        <button type="submit" 
                                class="text-xs font-semibold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors inline-flex items-center gap-1.5 py-1 px-3 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Keluar Akun (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Proteksi: Kunci Scroll & Cegah Tombol ESC / Penutupan Modal -->
    <script>
        (function() {
            // Kunci scrolling pada body
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');

            // Tangkal event Escape key agar modal tidak bisa ditutup
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            }, true);
        })();
    </script>
@endif
