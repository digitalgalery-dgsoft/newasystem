@extends('layouts.app')

@section('title', 'Form Pengajuan Surat Peringatan (SP)')

@section('content')
<div class="w-full space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('warning-letters.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar SP</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-500 to-amber-500 text-white flex items-center justify-center font-bold text-xl shadow-xs flex-shrink-0">
                <i class="fa-solid fa-file-circle-plus"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900 tracking-tight">Form Pengajuan Surat Peringatan</h1>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Pengajuan usulan Surat Peringatan (SP 1, SP 2, atau SP 3) untuk karyawan yang melakukan pelanggaran disiplin. 
                    <span class="text-amber-700 font-semibold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                        Catatan: Rujukan Pasal Peraturan Perusahaan/PKB akan ditentukan dan diinputkan oleh Bagian HRD saat tahap approval.
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Error Alert -->
    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-2xs">
        <div class="flex items-center gap-2 mb-1.5 font-bold">
            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
            <span>Terdapat beberapa isian yang belum lengkap atau keliru:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 pl-2 text-rose-700 font-normal">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main Form -->
    <form method="POST" action="{{ route('warning-letters.store') }}" enctype="multipart/form-data" class="space-y-6" id="spForm">
        @csrf

        <!-- 1. IDENTITAS KARYAWAN -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-5">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                <i class="fa-solid fa-id-card-clip text-primary text-base"></i>
                <span>1. Pilih Karyawan yang Diajukan</span>
            </div>

            <!-- Employee Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Cari & Pilih Karyawan <span class="text-rose-500">*</span>
                </label>
                <div class="relative" x-data="employeePicker()">
                    <div class="relative">
                        <i class="fa-solid fa-user-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               x-model="searchQuery" 
                               @input.debounce.300ms="searchEmployees()"
                               @focus="if(results.length > 0) isOpen = true"
                               placeholder="Ketik NIK, NIP, atau Nama Lengkap Karyawan..." 
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="employee_id" :value="selectedEmployee ? selectedEmployee.id : '{{ old('employee_id') }}'" required>

                    <!-- Search Results Dropdown -->
                    <div x-show="isOpen && results.length > 0" 
                         @click.outside="isOpen = false"
                         class="absolute left-0 right-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200 z-50 max-h-60 overflow-y-auto divide-y divide-slate-100"
                         style="display: none;">
                        <template x-for="emp in results" :key="emp.id">
                            <div @click="selectEmployee(emp)" class="p-3 hover:bg-slate-50 cursor-pointer flex items-center justify-between gap-3 transition-colors">
                                <div>
                                    <div class="text-xs font-bold text-slate-900" x-text="emp.nama"></div>
                                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                        NIK: <span class="font-bold text-slate-700" x-text="emp.nik"></span>
                                        <span class="mx-1">&bull;</span>
                                        <span x-text="emp.jabatan"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-0.5 rounded font-black text-[9px] bg-slate-100 text-slate-700" x-text="emp.entity"></span>
                                    <div class="text-[10px] text-slate-400 mt-0.5" x-text="emp.area"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Selected Employee Information Card -->
                    <template x-if="selectedEmployee">
                        <div class="mt-4 p-4 rounded-xl bg-slate-50/80 border border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Nama Lengkap</span>
                                <span class="font-bold text-slate-900 text-sm" x-text="selectedEmployee.nama"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">NIK & NIP</span>
                                <span class="font-mono font-bold text-slate-800" x-text="selectedEmployee.nik + ' / ' + (selectedEmployee.nip || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Jabatan & Area</span>
                                <span class="font-semibold text-slate-800" x-text="selectedEmployee.jabatan + ' (' + selectedEmployee.area + ')'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Entitas & Prinsiple</span>
                                <span class="font-semibold text-slate-800">
                                    <strong class="text-primary font-black" x-text="selectedEmployee.entity"></strong> - 
                                    <span x-text="selectedEmployee.prinsiple"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- 2. PARAMETER SURAT PERINGATAN -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-5">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3 text-slate-800 font-bold text-sm">
                <i class="fa-solid fa-stamp text-rose-500 text-base"></i>
                <span>2. Usulan Tingkat SP & Tanggal</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Tingkat SP Selection -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Usulan Tingkat Surat Peringatan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <!-- SP 1 -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all has-checked:border-blue-500 has-checked:bg-blue-50/50 has-checked:ring-2 has-checked:ring-blue-500/20 hover:bg-slate-50 border-slate-200">
                            <input type="radio" name="tingkat_sp" value="sp1" class="sr-only" {{ old('tingkat_sp', 'sp1') === 'sp1' ? 'checked' : '' }} required>
                            <span class="text-xs font-extrabold text-blue-700">SP 1</span>
                            <span class="text-[10px] text-slate-500 text-center mt-0.5">Peringatan I</span>
                        </label>

                        <!-- SP 2 -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all has-checked:border-amber-500 has-checked:bg-amber-50/50 has-checked:ring-2 has-checked:ring-amber-500/20 hover:bg-slate-50 border-slate-200">
                            <input type="radio" name="tingkat_sp" value="sp2" class="sr-only" {{ old('tingkat_sp') === 'sp2' ? 'checked' : '' }}>
                            <span class="text-xs font-extrabold text-amber-700">SP 2</span>
                            <span class="text-[10px] text-slate-500 text-center mt-0.5">Peringatan II</span>
                        </label>

                        <!-- SP 3 -->
                        <label class="relative flex flex-col items-center justify-center p-3 rounded-xl border cursor-pointer transition-all has-checked:border-rose-500 has-checked:bg-rose-50/50 has-checked:ring-2 has-checked:ring-rose-500/20 hover:bg-slate-50 border-slate-200">
                            <input type="radio" name="tingkat_sp" value="sp3" class="sr-only" {{ old('tingkat_sp') === 'sp3' ? 'checked' : '' }}>
                            <span class="text-xs font-extrabold text-rose-700">SP 3</span>
                            <span class="text-[10px] text-slate-500 text-center mt-0.5">Peringatan III (Terakhir)</span>
                        </label>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5">Jenis SP dapat disesuaikan/ditingkatkan oleh HRD saat penelaahan berkas.</p>
                </div>

                <!-- Tanggal Pengajuan Usulan SP -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Tanggal Pembuatan Pengajuan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                            <i class="fa-solid fa-calendar-day text-[9px]"></i>
                            <span>Hari Ini</span>
                        </span>
                    </label>
                    <input type="text" value="{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} ({{ date('Y-m-d') }})" readonly
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-bold text-xs outline-none cursor-not-allowed">
                    <p class="text-[10px] text-slate-400 mt-1.5">
                        <i class="fa-solid fa-circle-info text-blue-500 mr-0.5"></i>
                        Tanggal resmi Surat Peringatan &amp; masa berlaku 6 bulan akan dihitung otomatis sejak <strong>tanggal rilis / persetujuan akhir HRD</strong>.
                    </p>
                </div>
            </div>

            <!-- Tahap 1: Pimpinan / Atasan Pembuat SP (Otomatis dari Master Karyawan) -->
            <div class="pt-2 border-t border-slate-100">
                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-user-tie text-blue-600"></i>
                        <span>Pimpinan / Atasan Pemohon (Verifikator Tahap 1)</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                        <i class="fa-solid fa-lock text-[9px]"></i>
                        <span>Otomatis dari Master Karyawan</span>
                    </span>
                </label>

                @if(!empty($creatorPimpinan))
                    <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-2xs flex-shrink-0">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $creatorPimpinan }}</div>
                                <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                    {{ $creatorPimpinanJabatan ?: 'Pimpinan / Atasan Langsung' }} &bull; Verifikator Approval Tahap 1
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="pimpinan_pembuat" value="{{ $creatorPimpinan }}">
                        <span class="text-[10px] font-bold text-blue-700 bg-white px-2.5 py-1 rounded-lg border border-blue-200 shadow-2xs flex items-center gap-1 flex-shrink-0">
                            <i class="fa-solid fa-circle-check text-[9px] text-blue-600"></i> Terverifikasi Master
                        </span>
                    </div>
                @else
                    <div class="relative">
                        <select name="pimpinan_pembuat" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white font-medium text-slate-700">
                            <option value="">-- Pilih Pimpinan / Atasan dari Master Karyawan --</option>
                            @if(isset($masterLeaders))
                                @foreach($masterLeaders as $lead)
                                    <option value="{{ $lead->nama_karyawan }}" {{ old('pimpinan_pembuat') == $lead->nama_karyawan ? 'selected' : '' }}>
                                        {{ $lead->nama_karyawan }} ({{ $lead->jabatan }} - {{ $lead->area }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">
                        <i class="fa-solid fa-circle-info mr-0.5 text-blue-500"></i> Akun Anda belum terpetakan ke atasan langsung di master karyawan. Silakan pilih nama atasan langsung dari master karyawan di atas.
                    </p>
                @endif
                <p class="text-[10px] text-slate-400 mt-1.5">
                    Surat Peringatan yang diajukan akan otomatis diarahkan ke antrean verifikasi pimpinan di atas sebelum diteruskan ke bagian HRD.
                </p>
            </div>
        </div>

        <!-- 3. BUTIR-BUTIR PELANGGARAN & KRONOLOGI (MULTIPLE ITEMS) -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-4" x-data="violationsManager()">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-list-check text-rose-500 text-base"></i>
                    <span>3. Rincian Butir Pelanggaran & Kronologi</span>
                </div>
                <button type="button" @click="addViolation()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Tambah Butir Pelanggaran</span>
                </button>
            </div>

            <p class="text-xs text-slate-500">
                Tuliskan setiap tindakan pelanggaran secara spesifik beserta tanggal kejadian dan kronologi detail peristiwa.
            </p>

            <!-- Violations Items List -->
            <div class="space-y-4 pt-1">
                <template x-for="(item, index) in items" :key="item.key">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3 relative group">
                        
                        <!-- Item Header -->
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-2">
                            <span class="text-xs font-extrabold text-slate-800 flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-[10px] font-black" x-text="index + 1"></span>
                                <span>Butir Pelanggaran ke-<span x-text="index + 1"></span></span>
                            </span>
                            <button type="button" @click="removeViolation(index)" x-show="items.length > 1"
                                    class="text-slate-400 hover:text-rose-600 p-1 text-xs transition-colors" title="Hapus butir ini">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>

                        <!-- Grid: Tanggal & Uraian Pokok -->
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
                                <input type="text" :name="'violations[' + index + '][pelanggaran]'" x-model="item.pelanggaran" 
                                       placeholder="Contoh: Meninggalkan jam kerja operasional tanpa izin atasan" required
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white">
                            </div>
                        </div>

                        <!-- Kronologi Lengkap -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Kronologi Detail Peristiwa & Dampak Kejadian
                            </label>
                            <textarea :name="'violations[' + index + '][kronologi]'" x-model="item.kronologi" rows="2"
                                      placeholder="Jelaskan kronologi kejadian secara runtut, lokasi, waktu, saksi, dan dampak yang ditimbulkan terhadap operasional..."
                                      class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white"></textarea>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 4. BERKAS PENDUKUNG & TINDAKAN PERBAIKAN -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                    <i class="fa-solid fa-paperclip text-slate-600 text-base"></i>
                    <span>4. Lampiran Bukti Pendukung & Harapan Perbaikan</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200">
                    Opsional / Jika Ada
                </span>
            </div>

            <!-- Upload Bukti BAP / Foto -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                    <span>Upload Berkas Pendukung (BAP / Foto Bukti / Dokumen Kejadian)</span>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">Opsional (Boleh Dikosongkan)</span>
                </label>
                <input type="file" name="file_pendukung[]" multiple
                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all border border-slate-200 rounded-xl cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">
                    <i class="fa-solid fa-circle-info text-blue-500 mr-0.5"></i>
                    <strong>Opsional:</strong> Dapat memilih 1 atau lebih file jika ada berkas pendukung (Format: PDF, JPG, PNG, DOCX. Maksimal 10MB per file). Pengajuan usulan SP tetap sah dan dapat diproses tanpa lampiran berkas.
                </p>
            </div>

            <!-- Tindakan Perbaikan / Komitmen -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Tindakan Perbaikan yang Diharapkan (Opsional)
                </label>
                <textarea name="tindakan_perbaikan" rows="3"
                          placeholder="Contoh: Karyawan wajib mematuhi jam operasional dan tidak mengulangi pelanggaran serupa selama masa berlaku SP..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">{{ old('tindakan_perbaikan') }}</textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('warning-letters.index') }}" 
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs hover:shadow transition-all flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Kirim Pengajuan Usulan SP</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function employeePicker() {
    return {
        selectedEmployee: @if(isset($preselectedEmployee) && $preselectedEmployee)
            {
                id: {{ $preselectedEmployee->id }},
                nama: @json($preselectedEmployee->nama_karyawan),
                nik: @json($preselectedEmployee->nik),
                nip: @json($preselectedEmployee->nip ?: '-'),
                jabatan: @json($preselectedEmployee->jabatan ?: '-'),
                area: @json($preselectedEmployee->area ?: '-'),
                entity: @json(strtoupper($preselectedEmployee->entity ?: 'AMK')),
                prinsiple: @json($preselectedEmployee->prinsiple ?: 'Internal ESA')
            }
        @else null @endif,
        searchQuery: @if(isset($preselectedEmployee) && $preselectedEmployee)
            @json($preselectedEmployee->nama_karyawan . ' (' . $preselectedEmployee->nik . ')')
        @else '' @endif,
        results: [],
        isOpen: false,

        async searchEmployees() {
            if (this.searchQuery.length < 2) {
                this.results = [];
                this.isOpen = false;
                return;
            }
            try {
                const res = await fetch(`{{ route('warning-letters.search-employees') }}?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await res.json();
                this.results = data.results || [];
                this.isOpen = this.results.length > 0;
            } catch (e) {
                console.error('Error fetching employees:', e);
            }
        },

        selectEmployee(emp) {
            this.selectedEmployee = emp;
            this.searchQuery = `${emp.nama} (${emp.nik})`;
            this.isOpen = false;
        }
    };
}

function violationsManager() {
    return {
        items: [
            { key: Date.now(), tanggal: '{{ date('Y-m-d') }}', pelanggaran: '', kronologi: '' }
        ],

        addViolation() {
            this.items.push({
                key: Date.now() + Math.random(),
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
