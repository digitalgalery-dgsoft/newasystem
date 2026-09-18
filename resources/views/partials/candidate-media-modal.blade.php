<!-- CANDIDATE MEDIA PREVIEW MODAL (FOTO PROFIL & LAMPIRAN CV) -->
<div id="candidateMediaModalRoot" 
     x-data="candidateMediaModalComponent()"
     x-init="initModal()"
     x-show="isOpen"
     x-cloak
     @keydown.escape.window="close()"
     class="fixed inset-0 z-[99999] flex items-center justify-center p-2 sm:p-4 md:p-6 transition-all duration-200"
     style="display: none;">
     
    <!-- Backdrop Overlay with Blur -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
         @click="close()"></div>

    <!-- Modal Dialog Window -->
    <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-700/30 w-full max-w-5xl flex flex-col overflow-hidden max-h-[94vh] z-10 animate-in fade-in zoom-in-95 duration-200"
         @click.stop>
        
        <!-- Header Bar -->
        <div class="flex items-center justify-between px-4 sm:px-6 py-3.5 bg-slate-900 text-white border-b border-slate-800 flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base font-bold shadow-xs flex-shrink-0"
                     :class="mediaType === 'image' ? 'bg-primary-500/20 text-primary-300 border border-primary-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'">
                    <i :class="mediaType === 'image' ? 'fa-solid fa-image' : 'fa-solid fa-file-pdf'"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-bold text-white truncate" x-text="mediaTitle">Preview Dokumen</h3>
                    <p class="text-[11px] text-slate-400 truncate" x-text="mediaType === 'image' ? 'Pratinjau Foto Resmi Pelamar' : 'Pratinjau Berkas Lampiran CV'"></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                <!-- Open in New Tab -->
                <a :href="mediaUrl" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all shadow-xs"
                   title="Buka di Tab Baru">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px] text-slate-400"></i>
                    <span class="hidden sm:inline">Buka Tab Baru</span>
                </a>

                <!-- Download File -->
                <a :href="mediaUrl" download
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-primary-600 hover:bg-primary-500 text-white transition-all shadow-xs"
                   title="Unduh Berkas">
                    <i class="fa-solid fa-download text-[11px]"></i>
                    <span class="hidden sm:inline">Unduh</span>
                </a>

                <!-- Close Button -->
                <button type="button" @click="close()" 
                        class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-400 flex items-center justify-center transition-colors text-sm ml-1"
                        title="Tutup (Esc)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Body Content Area -->
        <div class="flex-1 overflow-auto bg-slate-900/90 flex items-center justify-center min-h-[50vh] max-h-[calc(94vh-125px)] p-2 sm:p-4">
            
            <!-- 1. IMAGE PREVIEW -->
            <template x-if="mediaType === 'image'">
                <div class="flex items-center justify-center w-full h-full p-2">
                    <img :src="mediaUrl" 
                         :alt="mediaTitle"
                         class="max-h-[76vh] max-w-full rounded-xl shadow-2xl object-contain border border-slate-700/50 bg-slate-950">
                </div>
            </template>

            <!-- 2. PDF / DOCUMENT PREVIEW -->
            <template x-if="mediaType === 'pdf'">
                <div class="w-full h-full min-h-[72vh] flex flex-col bg-slate-800 rounded-xl overflow-hidden border border-slate-700">
                    <iframe :src="mediaUrl" 
                            class="w-full flex-1 min-h-[70vh] bg-white"
                            frameborder="0">
                    </iframe>
                </div>
            </template>
        </div>

        <!-- Footer / Status Bar -->
        <div class="px-4 sm:px-6 py-2.5 bg-white border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2 flex-shrink-0">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-primary-500 text-xs"></i>
                <span>Gunakan tombol <b>Buka Tab Baru</b> jika ingin mencetak berkas atau jika peramban membatasi pratinjau dokumen.</span>
            </div>
            <button type="button" @click="close()" class="px-4 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                Tutup Preview
            </button>
        </div>
    </div>
</div>

<script>
    function candidateMediaModalComponent() {
        return {
            isOpen: false,
            mediaType: 'image',
            mediaUrl: '',
            mediaTitle: '',
            initModal() {
                window.openCandidateMedia = (type, url, title) => {
                    this.mediaType = type || 'image';
                    this.mediaUrl = url || '';
                    this.mediaTitle = title || 'Pratinjau Dokumen';
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                };
                window.closeCandidateMedia = () => {
                    this.close();
                };
            },
            close() {
                this.isOpen = false;
                this.mediaUrl = '';
                document.body.style.overflow = '';
            }
        };
    }
</script>
