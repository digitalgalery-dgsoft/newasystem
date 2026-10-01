@extends('layouts.public')

@section('title', 'Cek Status Pengajuan Veklaring - ESA Groups')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/70">
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold border border-primary/20">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
                <span>Pelacakan Surat Keterangan Kerja Online</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Cek Status Pengajuan Veklaring</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                Masukkan Nomor Induk Kependudukan (NIK) atau 8-digit Kode Validasi Permohonan Anda.
            </p>
        </div>

        <!-- Search Box -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
            <form action="{{ route('paklaring.public.check') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                        <i class="fa-solid fa-id-card text-sm"></i>
                    </span>
                    <input type="text" 
                           name="q" 
                           value="{{ $search ?? '' }}"
                           required
                           placeholder="Ketik NIK KTP (16 digit) atau Kode Validasi (cth: 8PKR7A2B)"
                           class="w-full pl-11 pr-4 py-3 rounded-2xl border border-slate-200 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-primary hover:bg-primary-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-primary/20 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Cari Pengajuan</span>
                </button>
            </form>
        </div>

        <!-- Hasil Pencarian -->
        @if(!empty($search))
            @if($paklarings->isEmpty())
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xs text-center space-y-3">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl border border-rose-100">
                        <i class="fa-solid fa-file-circle-xmark"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-800">Data Tidak Ditemukan</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Tidak ditemukan riwayat permohonan veklaring dengan kata kunci <strong>"{{ $search }}"</strong>. Pastikan NIK atau kode validasi sudah benar.
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('paklaring.public.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-xs">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>Buat Pengajuan Baru</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($paklarings as $item)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                        <!-- Header Pengajuan -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-primary bg-primary-50 px-2.5 py-0.5 rounded-lg border border-primary-200">
                                        {{ $item->kode_validasi }}
                                    </span>
                                    <span class="text-xs text-slate-400">&bull; {{ $item->created_at?->format('d M Y, H:i') }} WIB</span>
                                </div>
                                <h2 class="text-lg font-black text-slate-800 mt-1">{{ $item->nama_lengkap }}</h2>
                                <p class="text-xs text-slate-500">{{ $item->jabatan }} &bull; {{ $item->prinsiple }} &bull; {{ $item->area }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $item->status_badge['bg'] }} flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->status_badge['dot'] }}"></span>
                                    <span>{{ $item->status_badge['label'] }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Visual Step Progression Timeline -->
                        @php
                            $stepOrder = $item->current_step_order ?: match ($item->status_bagian) {
                                'Area' => 1,
                                'HRD' => 2,
                                'DB' => 3,
                                'BPJS' => 4,
                                'Selesai' => 5,
                                default => 1,
                            };
                            if ($item->status === 'Selesai') $stepOrder = 5;
                        @endphp

                        <div class="space-y-3">
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tahapan Verifikasi Berjalan:</div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <!-- Step 1: Area -->
                                <div class="p-3 rounded-2xl border text-center space-y-1 transition-all {{ $stepOrder > 1 ? 'bg-emerald-50/80 border-emerald-200 text-emerald-800' : ($stepOrder === 1 && $item->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 text-primary ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                                    <div class="text-base">
                                        @if($stepOrder > 1)
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        @elseif($stepOrder === 1 && $item->status !== 'Tolak')
                                             <i class="fa-solid fa-spinner fa-spin text-primary"></i>
                                        @else
                                            <i class="fa-solid fa-circle text-slate-300"></i>
                                        @endif
                                    </div>
                                    <div class="text-xs font-bold">1. Review Area</div>
                                    <div class="text-[10px] {{ $stepOrder > 1 ? 'text-emerald-600' : ($stepOrder === 1 ? 'text-primary' : 'text-slate-400') }}">
                                        {{ $stepOrder > 1 ? 'Disetujui' : ($stepOrder === 1 ? 'Sedang Diproses' : 'Review Area') }}
                                    </div>
                                </div>

                                <!-- Step 2: HRD -->
                                <div class="p-3 rounded-2xl border text-center space-y-1 transition-all {{ $stepOrder > 2 ? 'bg-emerald-50/80 border-emerald-200 text-emerald-800' : ($stepOrder === 2 && $item->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 text-primary ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                                    <div class="text-base">
                                        @if($stepOrder > 2)
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        @elseif($stepOrder === 2 && $item->status !== 'Tolak')
                                            <i class="fa-solid fa-spinner fa-spin text-primary"></i>
                                        @else
                                            <i class="fa-solid fa-circle text-slate-300"></i>
                                        @endif
                                    </div>
                                    <div class="text-xs font-bold">2. Review HRD</div>
                                    <div class="text-[10px] {{ $stepOrder > 2 ? 'text-emerald-600' : ($stepOrder === 2 ? 'text-primary' : 'text-slate-400') }}">
                                        {{ $stepOrder > 2 ? 'Disetujui' : ($stepOrder === 2 ? 'Sedang Diproses' : 'Review HRD') }}
                                    </div>
                                </div>

                                <!-- Step 3: DB -->
                                <div class="p-3 rounded-2xl border text-center space-y-1 transition-all {{ $stepOrder > 3 ? 'bg-emerald-50/80 border-emerald-200 text-emerald-800' : ($stepOrder === 3 && $item->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 text-primary ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                                    <div class="text-base">
                                        @if($stepOrder > 3)
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        @elseif($stepOrder === 3 && $item->status !== 'Tolak')
                                            <i class="fa-solid fa-spinner fa-spin text-primary"></i>
                                        @else
                                            <i class="fa-solid fa-circle text-slate-300"></i>
                                        @endif
                                    </div>
                                    <div class="text-xs font-bold">3. Review DB</div>
                                    <div class="text-[10px] {{ $stepOrder > 3 ? 'text-emerald-600' : ($stepOrder === 3 ? 'text-primary' : 'text-slate-400') }}">
                                        {{ $stepOrder > 3 ? 'Disetujui' : ($stepOrder === 3 ? 'Sedang Diproses' : 'Review DB') }}
                                    </div>
                                </div>

                                <!-- Step 4: BPJS & Selesai -->
                                <div class="p-3 rounded-2xl border text-center space-y-1 transition-all {{ $stepOrder >= 5 ? 'bg-emerald-50/80 border-emerald-200 text-emerald-800' : ($stepOrder === 4 && $item->status !== 'Tolak' ? 'bg-blue-50 border-blue-300 text-primary ring-2 ring-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-400') }}">
                                    <div class="text-base">
                                        @if($stepOrder >= 5)
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        @elseif($stepOrder === 4 && $item->status !== 'Tolak')
                                            <i class="fa-solid fa-spinner fa-spin text-primary"></i>
                                        @else
                                            <i class="fa-solid fa-circle text-slate-300"></i>
                                        @endif
                                    </div>
                                    <div class="text-xs font-bold">4. Review BPJS</div>
                                    <div class="text-[10px] {{ $stepOrder >= 5 ? 'text-emerald-600' : ($stepOrder === 4 ? 'Sedang Diproses' : 'Review BPJS') }}">
                                        {{ $stepOrder >= 5 ? 'Selesai Terbit' : ($stepOrder === 4 ? 'Sedang Diproses' : 'Review BPJS') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nomor Surat Resmi & Tombol Unduh Surat Jika Sudah Terbit / Rilis -->
                        @if(!empty($item->nomor_ref) || $item->status === 'Selesai')
                        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-emerald-50 via-teal-50/50 to-emerald-50/80 border border-emerald-200 text-emerald-900 shadow-xs space-y-3.5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                                        <i class="fa-solid fa-stamp text-emerald-600"></i>
                                        <span>Nomor Surat Referensi Kerja Resmi:</span>
                                    </div>
                                    <div class="text-sm sm:text-base font-black font-mono text-emerald-950 tracking-wide">
                                        {{ $item->nomor_ref ?: 'MENUNGGU NOMOR SURAT' }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full bg-emerald-600 text-white text-xs font-black shadow-xs flex items-center gap-1.5 w-fit">
                                        <i class="fa-solid fa-circle-check text-xs"></i>
                                        <span>Resmi Terbit &amp; Sah</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Aksi Unduh Surat & Validasi Digital (Touch-Friendly Responsive di Ponsel) -->
                            <div class="pt-3 border-t border-emerald-200/80 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                                <a href="{{ route('paklaring.public.download', $item->kode_validasi) }}" 
                                   target="_blank"
                                   class="flex-1 min-h-[44px] px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 text-center select-none cursor-pointer">
                                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                                    <span>Unduh Surat Veklaring (PDF)</span>
                                </a>

                                <a href="{{ route('paklaring.public.verify', $item->kode_validasi) }}" 
                                   target="_blank"
                                   class="min-h-[44px] px-4 py-2.5 rounded-xl bg-white hover:bg-emerald-50/50 border border-emerald-300 text-emerald-800 text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 text-center select-none cursor-pointer shadow-2xs">
                                    <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                                    <span>Verifikasi Keaslian QR</span>
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Riwayat Catatan Approval Terakhir -->
                        @if($item->approvals->isNotEmpty())
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas Terakhir:</div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                                @php $lastAppr = $item->approvals->first(); @endphp
                                <div class="flex items-center justify-between text-slate-700">
                                    <span class="font-bold">{{ $lastAppr->step_name }} ({{ $lastAppr->user_name }})</span>
                                    <span class="text-[10px] text-slate-400">{{ $lastAppr->created_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-slate-600 italic">"{{ $lastAppr->notes }}"</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        @endif

    </div>
</div>
@endsection
