@extends('layouts.app')

@section('title', 'Review & Approval HRD - SP ' . $letter->nama_karyawan)

@section('content')
<div class="w-full space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('warning-letters.show', $letter->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Detail SP</span>
        </a>
    </div>

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white p-6 rounded-2xl shadow-sm border border-amber-600">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center font-bold text-2xl flex-shrink-0 shadow-inner">
                <i class="fa-solid fa-stamp text-amber-200"></i>
            </div>
            <div>
                <span class="text-[10px] font-black tracking-wider uppercase bg-black/20 px-2 py-0.5 rounded-full border border-white/20">
                    Otoritas Khusus HRD Management
                </span>
                <h1 class="text-lg font-black tracking-tight mt-1.5">Penetapan Rujukan Pasal & Persetujuan Surat Peringatan</h1>
                <p class="text-xs text-amber-100/90 mt-1 leading-relaxed">
                    Sebagai HRD, Anda berwenang memvalidasi landasan hukum (Pasal PP/PKB), menyesuaikan tingkat SP jika diperlukan, 
                    serta menyunting redaksi butir pelanggaran dan kronologi sebelum nomor surat resmi diterbitkan secara otomatis.
                </p>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-2xs">
        <div class="flex items-center gap-2 mb-1 font-bold">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>Mohon lengkapi isian wajib berikut:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 pl-2 text-rose-700 font-normal">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main Review Form -->
    <form method="POST" action="{{ route('warning-letters.approve-hrd', $letter->id) }}" class="space-y-6" id="reviewHrdForm">
        @csrf

        <!-- 1. RINGKASAN DATA PENGAJUAN AWAL -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-user-check text-primary text-base"></i>
                    <span>Informasi Karyawan & Pengusul</span>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
                    Diajukan: {{ $letter->created_at ? $letter->created_at->format('d/m/Y H:i') : '-' }} oleh {{ $letter->creator ? $letter->creator->name : 'Sistem' }}
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">Nama Karyawan</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $letter->nama_karyawan }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">NIK & NIP</span>
                    <span class="font-mono font-bold text-slate-800">{{ $letter->nik }} / {{ $letter->nip ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">Jabatan & Area</span>
                    <span class="font-semibold text-slate-800">{{ $letter->jabatan ?: '-' }} ({{ $letter->area ?: '-' }})</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">Entitas & Prinsiple</span>
                    <span class="font-semibold text-slate-800">
                        <strong class="text-primary font-black">{{ $letter->entity }}</strong> - {{ $letter->prinsiple ?: 'Internal ESA' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. PENYESUAIAN JENIS SP OLEH HRD -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-stamp text-amber-600 text-base"></i>
                    <span>Penetapan Tingkat Surat Peringatan Final</span>
                </div>
                <div class="text-[11px] text-slate-500">
                    Usulan Awal: <strong class="text-slate-800 uppercase">{{ $letter->tingkat_sp_diajukan }}</strong>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    Pilih Tingkat SP yang Disahkan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <!-- SP 1 -->
                    <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all has-checked:border-blue-500 has-checked:bg-blue-50/50 has-checked:ring-2 has-checked:ring-blue-500/20 hover:bg-slate-50 border-slate-200">
                        <input type="radio" name="tingkat_sp" value="sp1" class="sr-only" {{ old('tingkat_sp', $letter->tingkat_sp) === 'sp1' ? 'checked' : '' }} required>
                        <span class="text-xs font-extrabold text-blue-700">SP 1</span>
                        <span class="text-[10px] text-slate-500 text-center mt-0.5">Peringatan I (Satu)</span>
                    </label>

                    <!-- SP 2 -->
                    <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all has-checked:border-amber-500 has-checked:bg-amber-50/50 has-checked:ring-2 has-checked:ring-amber-500/20 hover:bg-slate-50 border-slate-200">
                        <input type="radio" name="tingkat_sp" value="sp2" class="sr-only" {{ old('tingkat_sp', $letter->tingkat_sp) === 'sp2' ? 'checked' : '' }}>
                        <span class="text-xs font-extrabold text-amber-700">SP 2</span>
                        <span class="text-[10px] text-slate-500 text-center mt-0.5">Peringatan II (Dua)</span>
                    </label>

                    <!-- SP 3 -->
                    <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all has-checked:border-rose-500 has-checked:bg-rose-50/50 has-checked:ring-2 has-checked:ring-rose-500/20 hover:bg-slate-50 border-slate-200">
                        <input type="radio" name="tingkat_sp" value="sp3" class="sr-only" {{ old('tingkat_sp', $letter->tingkat_sp) === 'sp3' ? 'checked' : '' }}>
                        <span class="text-xs font-extrabold text-rose-700">SP 3</span>
                        <span class="text-[10px] text-slate-500 text-center mt-0.5">Peringatan III (Terakhir)</span>
                    </label>
                </div>
                <p class="text-[10px] text-slate-400 mt-1.5">
                    Jika tingkat SP diubah dari usulan awal pemohon, sistem akan mencatat riwayat perubahan tingkat secara otomatis pada audit trail.
                </p>
            </div>
        </div>

        <!-- 3. INPUT RUJUKAN PASAL PP / PKB (WAJIB HRD) -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-amber-300 ring-2 ring-amber-500/10 space-y-4">
            <div class="flex items-center justify-between border-b border-amber-100 pb-3">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                    <i class="fa-solid fa-scale-balanced text-amber-600 text-base"></i>
                    <span>Rujukan Dasar Hukum / Pasal Peraturan Perusahaan (PP) / PKB</span>
                    <span class="text-rose-500 font-black">*</span>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 uppercase">
                    Wajib Diisi HRD
                </span>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Tuliskan pasal, ayat, dan redaksi ketentuan Peraturan Perusahaan atau Perjanjian Kerja Bersama (PKB) yang dilanggar sebagai landasan yuridis penerbitan surat ini.
            </p>

            <div>
                <textarea name="pasal_pelanggaran" rows="4" required
                          placeholder="Contoh:&#10;Pasal 45 Ayat (2) Huruf b Peraturan Perusahaan PT Arina Multikarya: &quot;Setiap pekerja dilarang meninggalkan area kerja operasional pada jam kerja tanpa izin tertulis dari atasan langsung.&quot;"
                          class="w-full px-3.5 py-3 rounded-xl border border-amber-300 bg-amber-50/30 text-xs text-slate-800 font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all leading-relaxed">{{ old('pasal_pelanggaran', $letter->pasal_pelanggaran) }}</textarea>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                    <span>Format bebas: Dapat memuat rujukan beberapa pasal sekaligus.</span>
                    <span class="text-amber-700 font-semibold">Tampil pada lembar resmi cetak PDF</span>
                </div>
            </div>
        </div>

        <!-- 4. EDIT BUTIR-BUTIR PELANGGARAN, TANGGAL & KRONOLOGI OLEH HRD -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-4" x-data="hrdViolationsEditor()">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-pen-to-square text-rose-500 text-base"></i>
                    <span>Koreksi / Penyelarasan Butir Pelanggaran & Kronologi</span>
                </div>
                <button type="button" @click="addViolation()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Tambah Butir Pelanggaran</span>
                </button>
            </div>

            <p class="text-xs text-slate-500">
                HRD dapat menyunting redaksi ringkasan pelanggaran, membetulkan tanggal, atau menyempurnakan kronologi agar lugas dan tepat secara hukum ketenagakerjaan.
            </p>

            <!-- Items -->
            <div class="space-y-4 pt-1">
                <template x-for="(item, index) in items" :key="item.key">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3 relative">
                        <!-- Hidden ID if existing -->
                        <input type="hidden" :name="'violations[' + index + '][id]'" :value="item.id || ''">

                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-[10px] font-black" x-text="index + 1"></span>
                                <span>Butir Pelanggaran ke-<span x-text="index + 1"></span></span>
                            </span>
                            <button type="button" @click="removeViolation(index)" x-show="items.length > 1"
                                    class="text-slate-400 hover:text-rose-600 p-1 text-xs transition-colors" title="Hapus butir ini">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Tanggal Pelanggaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="date" :name="'violations[' + index + '][tanggal_pelanggaran]'" x-model="item.tanggal" required
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Uraian Pokok Pelanggaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" :name="'violations[' + index + '][pelanggaran]'" x-model="item.pelanggaran" required
                                       placeholder="Uraian ringkas pelanggaran..."
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Kronologi Lengkap & Dampak Kejadian
                            </label>
                            <textarea :name="'violations[' + index + '][kronologi]'" x-model="item.kronologi" rows="2"
                                      placeholder="Uraian kronologi kejadian detail..."
                                      class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 5. TINDAKAN PERBAIKAN & CATATAN APPROVAL -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                <i class="fa-solid fa-clipboard-check text-slate-600 text-base"></i>
                <span>Tindakan Perbaikan & Catatan Keputusan HRD</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Tindakan Perbaikan / Sanksi Lanjutan
                </label>
                <textarea name="tindakan_perbaikan" rows="2"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">{{ old('tindakan_perbaikan', $letter->tindakan_perbaikan) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Catatan Approval HRD (Terekam dalam Log Alur)
                </label>
                <input type="text" name="catatan_approval" 
                       value="{{ old('catatan_approval', 'Surat Peringatan telah ditelaah secara yuridis dan disetujui untuk diterbitkan.') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            </div>

            <!-- Ketetapan Tanggal Rilis & Masa Berlaku 6 Bulan -->
            <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/70 flex items-center justify-between text-xs text-emerald-950">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-calendar-check text-emerald-600 text-base flex-shrink-0"></i>
                    <div>
                        <div class="font-bold text-emerald-900">Tanggal Resmi Surat &amp; Masa Berlaku:</div>
                        <div class="text-[11px] text-emerald-800 mt-0.5">
                            Tanggal surat resmi akan ditetapkan pada hari ini (<strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong>) saat disetujui, dan masa berlaku 6 bulan otomatis aktif s/d <strong>{{ \Carbon\Carbon::now()->addMonths(6)->translatedFormat('d F Y') }}</strong>.
                        </div>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold text-[10px] shadow-2xs flex-shrink-0">
                    Otomatis Rilis Hari Ini
                </span>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="flex items-center justify-between gap-3 pt-2">
            <!-- Reject Button (Trigger Modal) -->
            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                    class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>Tolak Pengajuan</span>
            </button>

            <!-- Approve & Generate Button -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('warning-letters.show', $letter->id) }}" 
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-stamp text-sm"></i>
                    <span>Setujui & Terbitkan Nomor Surat Resmi</span>
                </button>
            </div>
        </div>
    </form>

</div>

<!-- MODAL TOLAK PENGAJUAN -->
<div id="rejectModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2 font-bold text-rose-700 text-sm">
                <i class="fa-solid fa-triangle-exclamation text-base"></i>
                <span>Tolak Pengajuan Surat Peringatan</span>
            </div>
            <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <p class="text-xs text-slate-500">
            Pengajuan akan berstatus Ditolak dan pemohon akan menerima pemberitahuan beserta alasan penolakan berikut.
        </p>

        <form method="POST" action="{{ route('warning-letters.reject', $letter->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan *</label>
                <textarea name="alasan_penolakan" rows="3" required minlength="5"
                          placeholder="Jelaskan alasan pengajuan ditolak (misal: bukti tidak mencukupi, pelanggaran belum memenuhi syarat SP, dll)..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs">
                    Konfirmasi Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function hrdViolationsEditor() {
    return {
        items: [
            @foreach($letter->violations as $v)
            {
                key: {{ $v->id }},
                id: {{ $v->id }},
                tanggal: '{{ $v->tanggal_pelanggaran ? $v->tanggal_pelanggaran->format('Y-m-d') : '' }}',
                pelanggaran: @json($v->pelanggaran),
                kronologi: @json($v->kronologi ?? '')
            },
            @endforeach
        ],

        addViolation() {
            this.items.push({
                key: Date.now() + Math.random(),
                id: null,
                tanggal: '{{ date('Y-m-d') }}',
                pelanggaran: '',
                kronologi: ''
            });
        },

        removeViolation(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        }
    };
}
</script>
@endpush
@endsection
