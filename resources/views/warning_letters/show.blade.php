@extends('layouts.app')

@section('title', 'Detail Surat Peringatan ' . ($letter->nomor_surat ?: '#' . $letter->id))

@section('content')
<div class="w-full space-y-6">

    <!-- Top Navigation & Action Buttons -->
    <div class="flex items-center justify-between">
        <a href="{{ route('warning-letters.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar SP</span>
        </a>

        <!-- Quick Actions -->
        <div class="flex items-center gap-2">
            @if($letter->status === 'approved')
                <a href="{{ route('warning-letters.print-pdf', $letter->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs hover:shadow transition-all">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span>Cetak PDF Resmi</span>
                </a>
            @endif

            @if($letter->status === 'review_head' && $canApproveHead)
                <button type="button" onclick="document.getElementById('approveHeadModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs hover:shadow transition-all cursor-pointer">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Review & Setujui (Pimpinan)</span>
                </button>
            @endif

            @if($letter->status === 'review_hrd' && $canApproveHrd)
                <a href="{{ route('warning-letters.review-hrd', $letter->id) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs hover:shadow transition-all">
                    <i class="fa-solid fa-stamp"></i>
                    <span>Review & Terbitkan Nomor (HRD)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Success / Info / Error -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('info'))
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-2xs">
        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- TAHAP 1: BANNER APPROVAL PIMPINAN & OVERRIDE HRD -->
    @if($letter->status === 'review_head' && ($canApproveHead || $isHrdOrAdmin))
    <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-primary p-6 rounded-2xl text-white shadow-md space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-inner">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white border border-white/30">
                        Verifikasi Tahap 1 &bull; Pimpinan / Atasan Pembuat SP
                    </span>
                    <h2 class="text-base font-extrabold mt-1">Persetujuan Usulan Surat Peringatan (SP)</h2>
                    <p class="text-xs text-white/90 mt-0.5 leading-relaxed">
                        @if($canApproveHead)
                            Anda teridentifikasi sebagai Pimpinan/Atasan berwenang. Silakan tinjau rincian butir pelanggaran, berikan catatan arahan, lalu setujui untuk meneruskan berkas ke HRD.
                        @else
                            Surat ini sedang menunggu review Pimpinan. Sebagai Administrator / Tim HRD, Anda berwenang meninjau, menyetujui, menolak, atau membatalkan usulan ini.
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                        class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark mr-1"></i>Tolak Usulan
                </button>
                @if($isHrdOrAdmin)
                <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" 
                        class="px-4 py-2 rounded-xl bg-slate-900/60 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                    <i class="fa-solid fa-ban mr-1"></i>Batalkan SP
                </button>
                <a href="{{ route('warning-letters.review-hrd', $letter->id) }}" 
                   class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-stamp"></i>
                    <span>Review HRD Langsung</span>
                </a>
                @endif
                @if($canApproveHead)
                <button type="button" onclick="document.getElementById('approveHeadModal').classList.remove('hidden')" 
                        class="px-5 py-2.5 rounded-xl bg-white text-primary hover:bg-slate-50 font-black text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Setujui ke Bagian HRD</span>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- TAHAP 2: BANNER APPROVAL HRD (Jika sedang menunggu review HRD dan user adalah approver HRD) -->
    @if($letter->status === 'review_hrd' && ($canApproveHrd || $isHrdOrAdmin))
    <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-orange-500 p-6 rounded-2xl text-white shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-inner">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/20 text-white border border-white/30">
                    Verifikasi Tahap 2 &bull; HRD Management
                </span>
                <h2 class="text-base font-extrabold mt-1">Usulan Menunggu Penelaahan Rujukan Pasal & Penerbitan Nomor</h2>
                <p class="text-xs text-white/90 mt-0.5 leading-relaxed">
                    Tentukan rujukan pasal PP/PKB, sesuaikan redaksi atau tingkat SP jika diperlukan, lalu setujui untuk menerbitkan nomor surat resmi.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                    class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark mr-1"></i>Tolak SP
            </button>
            @if($isHrdOrAdmin)
            <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" 
                    class="px-4 py-2 rounded-xl bg-slate-900/60 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                <i class="fa-solid fa-ban mr-1"></i>Batalkan SP
            </button>
            @endif
            <a href="{{ route('warning-letters.review-hrd', $letter->id) }}" 
               class="px-5 py-2.5 rounded-xl bg-white text-amber-800 hover:bg-slate-50 font-black text-xs shadow-md transition-all flex items-center gap-2 flex-shrink-0">
                <i class="fa-solid fa-stamp"></i>
                <span>Review & Terbitkan Nomor SP</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Header Status Card -->
    <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="px-2.5 py-0.5 rounded-full font-black text-xs uppercase tracking-wide
                        {{ $letter->tingkat_sp === 'sp3' ? 'bg-rose-100 text-rose-800 border border-rose-300' : ($letter->tingkat_sp === 'sp2' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-blue-100 text-blue-800 border border-blue-300') }}">
                        {{ $letter->tingkat_sp_label }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $letter->status_badge['bg'] }} {{ $letter->status_badge['text'] }} {{ $letter->status_badge['border'] }}">
                        <i class="fa-solid {{ $letter->status_badge['icon'] }} text-[10px] mr-1"></i>
                        {{ $letter->status_badge['label'] }}
                    </span>
                    @if($letter->pimpinan_pembuat)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        Atasan Pembuat: <strong>{{ $letter->pimpinan_pembuat }}</strong>
                    </span>
                    @endif
                </div>
                <h1 class="text-xl font-extrabold text-slate-900 font-mono tracking-tight">
                    {{ $letter->nomor_surat ?: 'Usulan Pengajuan SP #' . $letter->id }}
                </h1>
            </div>

            <!-- Date & Expiry Block + HRD Cancel Action -->
            <div class="flex items-center gap-3">
                @if($isHrdOrAdmin && $letter->status !== 'cancelled')
                <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors flex items-center gap-1.5 cursor-pointer shadow-2xs"
                        title="Batalkan Surat Peringatan kapanpun (Khusus HRD)">
                    <i class="fa-solid fa-ban text-xs"></i>
                    <span>Batalkan SP</span>
                </button>
                @endif
                <div class="sm:text-right bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        {{ $letter->status === 'approved' ? 'Tanggal Rilis & Masa Berlaku' : 'Jadwal Rilis & Masa Berlaku' }}
                    </div>
                    @if($letter->status === 'approved')
                        <div class="text-xs font-bold text-slate-800 mt-0.5">
                            Rilis HRD: {{ $letter->tanggal_surat ? $letter->tanggal_surat->translatedFormat('d F Y') : '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Berlaku s/d: <strong class="text-rose-600">{{ $letter->tanggal_expired ? $letter->tanggal_expired->translatedFormat('d F Y') : '-' }}</strong> (6 Bulan)
                        </div>
                    @else
                        <div class="text-xs font-bold text-amber-700 mt-0.5 flex items-center justify-end gap-1">
                            <i class="fa-solid fa-clock-rotate-left text-[10px]"></i> Menunggu Rilis HRD
                        </div>
                        <div class="text-[10px] text-slate-400 mt-0.5">
                            Masa berlaku 6 bulan aktif sejak tanggal disetujui HRD
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Indikator Berkas Fisik Bertandatangan (MERAH / HIJAU) -->
        @if($letter->status === 'approved')
        <div class="p-4 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3
            {{ $letter->has_signed_doc ? 'bg-emerald-50/80 border-emerald-300 text-emerald-900' : 'bg-rose-50/80 border-rose-300 text-rose-900' }}">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shadow-2xs flex-shrink-0
                    {{ $letter->has_signed_doc ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white animate-bounce' }}">
                    <i class="fa-solid {{ $letter->has_signed_doc ? 'fa-file-circle-check' : 'fa-triangle-exclamation' }}"></i>
                </div>
                <div>
                    <div class="text-xs font-bold flex items-center gap-2">
                        <span>Status Berkas Fisik Bertandatangan 3 Pihak:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase
                            {{ $letter->has_signed_doc ? 'bg-emerald-200 text-emerald-900' : 'bg-rose-200 text-rose-900' }}">
                            {{ $letter->has_signed_doc ? 'LENGKAP (HIJAU)' : 'BELUM DIUNGGAH (MERAH)' }}
                        </span>
                    </div>
                    <p class="text-[11px] opacity-90 mt-0.5">
                        @if($letter->has_signed_doc)
                            Scan surat fisik yang telah ditandatangani oleh Karyawan, Atasan Langsung, dan HRD telah tersimpan aman pada sistem.
                        @else
                            Surat Peringatan resmi telah diterbitkan. Silakan cetak PDF dokumen, lakukan penandatanganan fisik 3 pihak, lalu unggah kembali scan berkas di bawah.
                        @endif
                    </p>
                </div>
            </div>

            <!-- Upload / Download Button -->
            <div class="flex items-center gap-2 flex-shrink-0">
                @if($letter->has_signed_doc)
                    <a href="{{ route('warning-letters.download-signed', $letter->id) }}" target="_blank"
                       class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-eye"></i>
                        <span>Lihat Berkas Scan</span>
                    </a>
                @endif
                <button type="button" onclick="document.getElementById('uploadTtdModal').classList.remove('hidden')"
                        class="px-3.5 py-1.5 rounded-xl font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer
                            {{ $letter->has_signed_doc ? 'bg-white text-emerald-800 border border-emerald-300 hover:bg-emerald-100' : 'bg-rose-600 hover:bg-rose-700 text-white' }}">
                    <i class="fa-solid fa-upload"></i>
                    <span>{{ $letter->has_signed_doc ? 'Unggah Ulang Scan' : 'Unggah Scan TTD Sekarang' }}</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    <!-- Grid Detail Data (Full Width 3-Columns Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Kolom): Profil Karyawan, Rujukan Pasal & Butir Pelanggaran -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. DATA KARYAWAN -->
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-id-card text-primary text-base"></i>
                    <span>Identitas Karyawan Bersangkutan</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Nama Lengkap</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $letter->nama_karyawan }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">NIK & NIP</span>
                        <span class="font-mono font-bold text-slate-800">{{ $letter->nik }} / {{ $letter->nip ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Jabatan</span>
                        <span class="font-semibold text-slate-800">{{ $letter->jabatan ?: '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Entitas Perusahaan</span>
                        <span class="px-2 py-0.5 rounded font-black text-[10px] bg-slate-800 text-white inline-block mt-0.5">
                            {{ $letter->entity }} - {{ $letter->entity_full_name }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Area Penempatan</span>
                        <span class="font-semibold text-slate-800">{{ $letter->area ?: '-' }} ({{ $letter->singkatan_area }})</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Mitra / Prinsiple</span>
                        <span class="font-semibold text-slate-800">{{ $letter->prinsiple ?: 'Internal ESA' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. RUJUKAN PASAL PP / PKB (DIISI OLEH HRD SAAT APPROVAL) -->
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                        <i class="fa-solid fa-scale-balanced text-amber-600 text-base"></i>
                        <span>Landasan Hukum / Rujukan Pasal PP & PKB</span>
                    </div>
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                        Wewenang HRD Management
                    </span>
                </div>

                @if(!empty($letter->pasal_pelanggaran))
                    <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200 text-xs leading-relaxed text-slate-800 whitespace-pre-line font-medium">
                        {{ $letter->pasal_pelanggaran }}
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-300 text-center text-xs text-slate-400">
                        <i class="fa-solid fa-clock text-amber-500 text-sm mb-1 block"></i>
                        <span>Rujukan pasal belum diinputkan. Bagian HRD akan menentukan dan menginputkan pasal Peraturan Perusahaan / PKB pada tahap persetujuan (approval).</span>
                    </div>
                @endif
            </div>

            <!-- 3. RINCIAN BUTIR-BUTIR PELANGGARAN & KRONOLOGI -->
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                        <i class="fa-solid fa-list-check text-rose-500 text-base"></i>
                        <span>Uraian Butir Pelanggaran & Kronologi Kejadian</span>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                        {{ $letter->violations->count() }} Butir Pelanggaran
                    </span>
                </div>

                <div class="space-y-4">
                    @forelse($letter->violations as $idx => $violation)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-slate-800 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] font-black">
                                    {{ $idx + 1 }}
                                </span>
                                <span>{{ $violation->pelanggaran }}</span>
                            </span>
                            <span class="text-[10px] font-mono font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                                <i class="fa-regular fa-calendar text-[9px] mr-1"></i>
                                {{ $violation->tanggal_pelanggaran ? $violation->tanggal_pelanggaran->format('d M Y') : '-' }}
                            </span>
                        </div>
                        @if(!empty($violation->kronologi))
                        <div class="text-xs text-slate-600 bg-white p-3 rounded-lg border border-slate-200/80 leading-relaxed whitespace-pre-line mt-2">
                            <span class="font-bold text-slate-700 block text-[11px] mb-1">Kronologi Kejadian:</span>
                            {{ $violation->kronologi }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-slate-400">Tidak ada butir pelanggaran yang dicatat.</div>
                    @endforelse
                </div>

                <!-- Tindakan Perbaikan / Komitmen -->
                @if(!empty($letter->tindakan_perbaikan))
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-700 block mb-1">Komitmen & Tindakan Perbaikan yang Diharapkan:</span>
                    <div class="p-3 rounded-xl bg-slate-50 text-xs text-slate-700 italic border border-slate-200">
                        "{{ $letter->tindakan_perbaikan }}"
                    </div>
                </div>
                @endif
            </div>

            <!-- 4. LAMPIRAN BERKAS BAP & FOTO BUKTI -->
            @if(!empty($letter->file_pendukung) && is_array($letter->file_pendukung) && count($letter->file_pendukung) > 0)
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-3">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-paperclip text-slate-600 text-base"></i>
                    <span>Berkas Lampiran Pendukung (BAP / Foto Bukti)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($letter->file_pendukung as $file)
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fa-solid fa-file-lines text-slate-400 text-base flex-shrink-0"></i>
                            <div class="min-w-0 truncate">
                                <div class="text-xs font-bold text-slate-800 truncate" title="{{ $file['name'] ?? 'Berkas Lampiran' }}">{{ $file['name'] ?? 'Berkas Lampiran' }}</div>
                                <div class="text-[10px] text-slate-400">{{ isset($file['size']) ? number_format($file['size'] / 1024, 1) . ' KB' : '' }}</div>
                            </div>
                        </div>
                        @if(isset($file['path']))
                        <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                           class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-[10px] transition-colors flex-shrink-0">
                            Unduh
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-white p-4 rounded-2xl shadow-xs border border-slate-200 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-paperclip text-slate-400"></i>
                    <span class="font-medium text-slate-700">Berkas Lampiran Pendukung:</span>
                    <span class="text-slate-400 italic">Tidak ada lampiran berkas fisik (Pengajuan tanpa lampiran)</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                    Opsional
                </span>
            </div>
            @endif

        </div>

        <!-- Kolom Kanan (1 Kolom): Tracking Workflow & Riwayat Approval -->
        <div class="space-y-6">

            <!-- Tombol Cetak Dokumen PDF Resmi (Jika Sudah Disetujui) -->
            @if($letter->status === 'approved')
            <div class="bg-gradient-to-tr from-rose-50 to-amber-50 p-5 rounded-2xl border border-rose-200 text-center space-y-3 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center mx-auto shadow-md">
                    <i class="fa-solid fa-file-pdf text-xl"></i>
                </div>
                <div>
                    <div class="text-xs font-extrabold text-slate-900">Dokumen PDF Resmi Siap Cetak</div>
                    <p class="text-[10px] text-slate-500 mt-0.5">
                        Lengkap dengan Kop Surat resmi {{ $letter->entity_full_name }}, Rujukan Pasal &amp; QR Code Verifikasi.
                    </p>
                </div>
                <div class="space-y-2 pt-1">
                    <a href="{{ route('warning-letters.print-pdf', $letter->id) }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs hover:shadow transition-all">
                        <i class="fa-solid fa-print"></i>
                        <span>Buka / Cetak PDF Dokumen</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- Kartu Info Pihak Terkait (Persetujuan Bertingkat) -->
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-users text-slate-600 text-base"></i>
                    <span>Pihak &amp; Alur Persetujuan Bertingkat</span>
                </div>

                <div class="space-y-3.5 text-xs">
                    <!-- 1. Pemohon -->
                    <div class="flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">
                            1
                        </div>
                        <div class="flex-1">
                            <span class="text-[10px] text-slate-400 block font-semibold">Diajukan Oleh (Pemohon)</span>
                            <span class="font-bold text-slate-800">{{ $letter->creator ? $letter->creator->name : 'Sistem' }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ $letter->created_at ? $letter->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>

                    <!-- 2. Pimpinan Pembuat -->
                    <div class="flex items-start gap-2.5 pt-2 border-t border-slate-100">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5
                            {{ $letter->head_approved_at ? 'bg-emerald-100 text-emerald-800' : ($letter->status === 'review_head' ? 'bg-blue-100 text-blue-800 ring-2 ring-blue-300' : 'bg-slate-100 text-slate-500') }}">
                            2
                        </div>
                        <div class="flex-1">
                            <span class="text-[10px] text-slate-400 block font-semibold">Pimpinan / Atasan Pemohon (Tahap 1)</span>
                            @if($letter->headApprover)
                                <span class="font-bold text-emerald-700 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                    <span>{{ $letter->headApprover->name }}</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block">{{ $letter->head_approved_at ? $letter->head_approved_at->format('d/m/Y H:i') : '-' }}</span>
                                @if(!empty($letter->head_notes))
                                    <p class="text-[11px] text-slate-600 italic bg-slate-50 p-2 rounded-lg mt-1 border border-slate-200/80">"{{ $letter->head_notes }}"</p>
                                @endif
                            @elseif($letter->status === 'review_head')
                                <span class="font-bold text-blue-700 flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-blue-500 text-[10px]"></i>
                                    <span>Menunggu Review Pimpinan</span>
                                </span>
                                <span class="text-[10px] text-slate-500 block">Atasan Terdata: {{ $letter->pimpinan_pembuat ?: 'Atasan Langsung' }}</span>
                            @else
                                <span class="text-slate-500 italic">{{ $letter->pimpinan_pembuat ?: 'Atasan Langsung' }} (Selesai/Dilewati)</span>
                            @endif
                        </div>
                    </div>

                    <!-- 3. HRD Management -->
                    <div class="flex items-start gap-2.5 pt-2 border-t border-slate-100">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5
                            {{ $letter->approved_at ? 'bg-emerald-100 text-emerald-800' : ($letter->status === 'review_hrd' ? 'bg-amber-100 text-amber-800 ring-2 ring-amber-300' : 'bg-slate-100 text-slate-500') }}">
                            3
                        </div>
                        <div class="flex-1">
                            <span class="text-[10px] text-slate-400 block font-semibold">Approval &amp; Penomoran HRD (Tahap 2)</span>
                            @if($letter->approver)
                                <span class="font-bold text-emerald-700 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                    <span>{{ $letter->approver->name }}</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block">{{ $letter->approved_at ? $letter->approved_at->format('d/m/Y H:i') : '-' }}</span>
                            @elseif($letter->status === 'review_hrd')
                                <span class="font-bold text-amber-700 flex items-center gap-1">
                                    <i class="fa-solid fa-clock text-amber-500 text-[10px]"></i>
                                    <span>Menunggu Verifikasi HRD</span>
                                </span>
                            @else
                                <span class="text-slate-400 italic">Menunggu Tahap 1 Selesai</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Log Approval -->
            <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-clock-rotate-left text-slate-600 text-base"></i>
                    <span>Riwayat Alur Persetujuan</span>
                </div>

                <div class="space-y-4 relative before:absolute before:inset-0 before:left-3 before:w-0.5 before:bg-slate-200">
                    @forelse($letter->approvals as $log)
                    <div class="relative flex items-start gap-3 pl-1">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-[9px] font-black z-10 flex-shrink-0
                            {{ $log->action === 'approve' ? 'bg-emerald-600' : ($log->action === 'reject' ? 'bg-rose-600' : 'bg-blue-600') }}">
                            <i class="fa-solid {{ $log->action === 'approve' ? 'fa-check' : ($log->action === 'reject' ? 'fa-xmark' : 'fa-arrow-right') }}"></i>
                        </div>
                        <div class="min-w-0 flex-1 bg-slate-50 p-2.5 rounded-xl border border-slate-200/80 text-xs">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-bold text-slate-800 truncate">{{ $log->user ? $log->user->name : 'Sistem' }}</span>
                                <span class="text-[9px] text-slate-400 flex-shrink-0">{{ $log->created_at ? $log->created_at->format('d/m H:i') : '' }}</span>
                            </div>
                            <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
                                Stage: {{ strtoupper(str_replace('_', ' ', $log->stage)) }}
                            </div>
                            @if($log->perubahan_tingkat)
                            <div class="text-[10px] text-amber-700 font-bold mt-1">
                                Penyesuaian Tingkat: {{ $log->perubahan_tingkat }}
                            </div>
                            @endif
                            @if(!empty($log->catatan))
                            <p class="text-[11px] text-slate-600 mt-1 italic">
                                "{{ $log->catatan }}"
                            </p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-xs text-slate-400 text-center py-2">Belum ada catatan log alur.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<!-- MODAL APPROVAL PIMPINAN (POPUP) -->
@if($letter->status === 'review_head' && $canApproveHead)
<div id="approveHeadModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2 font-bold text-slate-900 text-sm">
                <i class="fa-solid fa-user-check text-blue-600 text-base"></i>
                <span>Persetujuan Tahap 1 &bull; Pimpinan</span>
            </div>
            <button type="button" onclick="document.getElementById('approveHeadModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            Apakah Anda yakin menyetujui usulan Surat Peringatan ini? Setelah disetujui, berkas akan diteruskan ke Tim HRD Management untuk verifikasi rujukan pasal dan penerbitan nomor surat resmi.
        </p>

        <form method="POST" action="{{ route('warning-letters.approve-head', $letter->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pimpinan (Opsional)</label>
                <textarea name="notes" rows="3" placeholder="Tuliskan catatan arahan, hasil pembinaan, atau rekomendasi untuk HRD..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('approveHeadModal').classList.add('hidden')" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Setujui ke Tahap HRD</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- MODAL PENOLAKAN SP (POPUP) -->
<div id="rejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2 font-bold text-rose-700 text-sm">
                <i class="fa-solid fa-ban text-rose-600 text-base"></i>
                <span>Tolak Usulan Surat Peringatan</span>
            </div>
            <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            Mohon cantumkan alasan penolakan secara jelas agar dapat dipahami dan diperbaiki oleh pemohon.
        </p>

        <form method="POST" action="{{ route('warning-letters.reject', $letter->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="alasan_penolakan" rows="3" required placeholder="Contoh: Bukti ketidakhadiran belum lengkap atau sudah diselesaikan via mediasi internal..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Tolak Usulan SP</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL UNGGAH SCAN TTD FISIK (POPUP) -->
<div id="uploadTtdModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2 font-bold text-slate-900 text-sm">
                <i class="fa-solid fa-file-signature text-rose-600 text-base"></i>
                <span>Unggah Scan Berkas Bertandatangan</span>
            </div>
            <button type="button" onclick="document.getElementById('uploadTtdModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            Pastikan berkas scan yang diunggah telah memuat tanda tangan fisik lengkap 3 pihak: <strong>Karyawan Bersangkutan</strong>, <strong>Atasan Langsung</strong>, dan <strong>HRD Management</strong>.
        </p>

        <form method="POST" action="{{ route('warning-letters.upload-signed', $letter->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Berkas Scan (PDF / Gambar) *</label>
                <input type="file" name="file_ttd" required accept=".pdf,.jpg,.jpeg,.png"
                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all border border-slate-200 rounded-xl cursor-pointer">
                <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, JPG, PNG (Maks 15MB).</span>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('uploadTtdModal').classList.add('hidden')" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs cursor-pointer">
                    Simpan & Update Status Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PEMBATALAN SP (POPUP KHUSUS HRD) -->
@if($isHrdOrAdmin && $letter->status !== 'cancelled')
<div id="cancelModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2 font-bold text-rose-800 text-sm">
                <i class="fa-solid fa-ban text-rose-600 text-base"></i>
                <span>Batalkan Surat Peringatan (HRD)</span>
            </div>
            <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            Sebagai HRD / Administrator, Anda dapat membatalkan Surat Peringatan ini kapanpun. Status dokumen akan berubah menjadi <strong>Dibatalkan</strong> dan tercatat resmi pada riwayat log audit.
        </p>

        <form method="POST" action="{{ route('warning-letters.cancel', $letter->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan Resmi <span class="text-rose-500">*</span></label>
                <textarea name="alasan_pembatalan" rows="3" required placeholder="Contoh: Telah dilakukan rekonsiliasi internal / investigasi lanjutan menunjukkan kesalahpahaman..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 cursor-pointer">
                    Kembali
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i>
                    <span>Ya, Batalkan Surat Peringatan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
