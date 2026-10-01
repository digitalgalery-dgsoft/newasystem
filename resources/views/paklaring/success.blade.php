@extends('layouts.public')

@section('title', 'Permohonan Veklaring Berhasil Dikirim - ESA Groups')

@section('content')
<div class="min-h-screen py-16 px-4 sm:px-6 lg:px-8 bg-slate-50/70 flex items-center justify-center">
    <div class="max-w-xl w-full space-y-6">

        <!-- Card Sukses -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50 text-center space-y-6 relative overflow-hidden">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl border-2 border-emerald-100 shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Permohonan Berhasil Masuk</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Pengajuan Veklaring Diterima</h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Permohonan Surat Keterangan / Referensi Kerja Anda telah tersimpan di sistem ESA Groups dan langsung diteruskan ke Tim Area untuk verifikasi awal.
                </p>
            </div>

            <!-- Kode Validasi Box -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-50/70 via-indigo-50/50 to-slate-50 border border-blue-200/70 space-y-2">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">ID Permohonan / Kode Validasi Anda:</div>
                <div class="flex items-center justify-center gap-3">
                    <span id="kodeValText" class="text-2xl sm:text-3xl font-black text-primary tracking-widest font-mono">{{ $paklaring->kode_validasi }}</span>
                    <button type="button" 
                            onclick="copyKode('{{ $paklaring->kode_validasi }}')" 
                            class="p-2 rounded-xl bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs shadow-xs transition-all cursor-pointer" 
                            title="Salin Kode Permohonan">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
                <p class="text-[11px] text-slate-400">Simpan kode ini atau NIK Anda untuk melacak perkembangan status surat referensi kerja.</p>
            </div>

            <!-- Ringkasan Data -->
            <div class="text-left p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2.5 text-xs text-slate-700">
                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-slate-400">Nama Pemohon:</span>
                    <span class="font-bold text-slate-800">{{ $paklaring->nama_lengkap }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-slate-400">Nomor KTP (NIK):</span>
                    <span class="font-bold font-mono text-slate-800">{{ $paklaring->nik }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-slate-400">Prinsiple &amp; Area:</span>
                    <span class="font-bold text-slate-800">{{ $paklaring->prinsiple }} &bull; {{ $paklaring->area }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-slate-400">Jabatan Terakhir:</span>
                    <span class="font-bold text-slate-800">{{ $paklaring->jabatan }}</span>
                </div>
                <div class="flex justify-between pt-1">
                    <span class="text-slate-400">Status Saat Ini:</span>
                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-200">
                        {{ $paklaring->status_bagian_badge['label'] }}
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                <a href="{{ route('paklaring.public.check', ['q' => $paklaring->kode_validasi]) }}" 
                   class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-primary hover:bg-primary-700 text-white text-xs font-bold transition-all shadow-md shadow-primary/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    <span>Pantau Status Pengajuan</span>
                </a>
                <a href="{{ route('home.index') }}" 
                   class="w-full sm:w-auto py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    <span>Beranda Utama</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
function copyKode(kode) {
    navigator.clipboard.writeText(kode).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Disalin!',
            text: `Kode permohonan ${kode} telah disalin ke clipboard.`,
            timer: 1500,
            showConfirmButton: false
        });
    });
}
</script>
@endsection
