@extends('layouts.app')

@section('title', 'Alur Approver Dinamis - ESA Groups')

@section('content')
@php
    // Siapkan daftar opsi Area untuk Searchable Dropdown
    $areaOptionsList = [
        ['value' => 'ALL', 'label' => 'Semua Area (Nasional)', 'desc' => 'Berlaku untuk seluruh area / nasional'],
        ['value' => 'JAKARTA', 'label' => 'Khusus Area Jakarta', 'desc' => 'Kandidat formasi wilayah DKI Jakarta'],
        ['value' => 'OUTSIDE_JAKARTA', 'label' => 'Selain Area Jakarta (Luar Jakarta / Daerah)', 'desc' => 'Kandidat penempatan kantor cabang / luar kota'],
    ];
    foreach($areas as $ar) {
        $areaOptionsList[] = ['value' => $ar, 'label' => $ar, 'desc' => 'Area Penempatan ' . $ar];
    }

    // Siapkan daftar opsi Prinsiple / Entitas untuk Searchable Dropdown
    $principleOptionsList = [
        ['value' => 'ALL', 'label' => 'Semua Prinsiple / Entitas', 'desc' => 'Berlaku untuk seluruh entitas inhouse & prinsiple'],
    ];
    foreach($entities as $code => $entName) {
        $principleOptionsList[] = ['value' => $code, 'label' => $code . ' - ' . $entName, 'desc' => 'Entitas Resmi Inhouse ' . $code];
    }
    if (!empty($principles)) {
        foreach($principles as $pName) {
            $principleOptionsList[] = ['value' => $pName, 'label' => $pName, 'desc' => 'Master Prinsiple Rekanan'];
        }
    }
@endphp

<div class="space-y-6" x-data="workflowManager()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                    Modul Alur Approval
                </span>
                <span class="text-xs text-slate-400 font-medium">&bull; Versi Dinamis Multi-Approver</span>
            </div>
            <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-diagram-project text-primary text-lg"></i>
                <span>Konfigurasi Alur Approver: {{ $workflow->name }}</span>
            </h1>
            <p class="text-xs text-slate-500">
                Atur tahapan persetujuan kandidat inhouse secara fleksibel, kondisi Area &amp; Prinsiple dinamis, pengecualian Direksi, dan pemilihan multiple approver.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" @click="openCreateModal()" class="px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold transition-all shadow-md shadow-primary/20 flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Step Approval</span>
            </button>
        </div>
    </div>

    <!-- Feedback Notifikasi -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    <!-- Visual Pipeline Stepper -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-black text-slate-800">Pipeline Tahapan Approval</h2>
                <p class="text-[11px] text-slate-500">Urutan tahapan yang akan dilalui oleh berkas kandidat inhouse secara sekuensial.</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                Total {{ $workflow->steps->count() }} Step
            </span>
        </div>

        <!-- Daftar Step Cards -->
        <div class="space-y-3.5">
            @forelse($workflow->steps as $index => $step)
            <div class="p-4 rounded-2xl border border-slate-200/80 bg-white hover:border-primary/40 transition-all shadow-xs relative">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Kiri: Nomor Step & Informasi -->
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                            {{ $step->step_order }}
                        </div>

                        <div class="space-y-1.5 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-sm text-slate-800">{{ $step->step_name }}</h3>

                                <!-- Badge Tipe Approver -->
                                @if($step->approver_type === 'head')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                        <i class="fa-solid fa-user-tie text-[9px]"></i> Head / Pimpinan Langsung
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        <i class="fa-solid fa-users text-[9px]"></i> Akun User Spesifik
                                    </span>
                                @endif

                                <!-- Badge Skip Direksi -->
                                @if($step->skip_if_direksi)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1" title="Jika pimpinan rekruter adalah Direksi, step ini otomatis dilewati">
                                        <i class="fa-solid fa-shield-halved text-[9px]"></i> Auto-Skip Direksi
                                    </span>
                                @endif
                            </div>

                            @if($step->description)
                                <p class="text-xs text-slate-500 leading-relaxed">{{ $step->description }}</p>
                            @endif

                            <!-- Detail Approver / Pemetaan Dinamis -->
                            @if($step->approver_type === 'user')
                                @if(!empty($step->approval_rules) && is_array($step->approval_rules))
                                    <div class="pt-2 space-y-1.5">
                                        <div class="text-[11px] font-bold text-slate-500 flex items-center gap-1.5">
                                            <i class="fa-solid fa-network-wired text-primary text-xs"></i>
                                            <span>Pemetaan Approver Dinamis ({{ count($step->approval_rules) }} Aturan):</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($step->approval_rules as $r)
                                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs flex flex-col gap-1.5 shadow-2xs">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span class="inline-flex items-center gap-1 font-bold text-slate-700 bg-white px-2 py-0.5 rounded-lg border border-slate-200 text-[10.5px]">
                                                            <i class="fa-solid fa-location-dot text-indigo-500 text-[9.5px]"></i>
                                                            {{ $r['area'] === 'ALL' ? 'Semua Area' : ($r['area'] === 'OUTSIDE_JAKARTA' ? 'Luar Jakarta' : $r['area']) }}
                                                        </span>
                                                        <span class="inline-flex items-center gap-1 font-bold text-slate-700 bg-white px-2 py-0.5 rounded-lg border border-slate-200 text-[10.5px]">
                                                            <i class="fa-solid fa-building text-amber-500 text-[9.5px]"></i>
                                                            {{ $r['prinsiple'] === 'ALL' ? 'Semua Prinsiple' : $r['prinsiple'] }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-1 flex-wrap pt-0.5">
                                                        <span class="text-[10px] text-slate-400 font-medium">Approver:</span>
                                                        @if(!empty($r['users']))
                                                            @foreach($r['users'] as $u)
                                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10.5px] font-semibold bg-primary/10 text-primary border border-primary/20">
                                                                    <i class="fa-solid fa-user-check text-[9px]"></i>
                                                                    <span>{{ is_array($u) ? $u['name'] : $u }}</span>
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-[10.5px] text-rose-500 font-semibold italic">Belum ada akun dipilih</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Fallback Approver Terpilih (Legacy) -->
                                    <div class="pt-1 flex flex-wrap items-center gap-1.5">
                                        <span class="text-[11px] font-bold text-slate-400">Approver Terpilih:</span>
                                        @forelse($step->stepUsers as $su)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                <i class="fa-solid fa-user-check text-[9px] text-emerald-600"></i>
                                                <span>{{ $su->user_name }}</span>
                                                @if($su->user?->job_title)
                                                    <span class="text-[9.5px] text-slate-400 font-normal">({{ $su->user->job_title }})</span>
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-[11px] text-rose-500 font-bold italic">Belum ada akun approver yang dipilih!</span>
                                        @endforelse
                                    </div>
                                @endif
                            @else
                                <div class="pt-1 flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
                                    <i class="fa-solid fa-circle-info text-indigo-500 text-xs"></i>
                                    <span>Approver akan ditentukan otomatis sesuai data Pimpinan dari Rekrutor kandidat di Master Karyawan.</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Kanan: Tombol Reorder, Edit, dan Hapus -->
                    <div class="flex items-center gap-1.5 shrink-0 self-end lg:self-center">
                        @if($index > 0)
                        <button type="button" @click="moveStep({{ $index }}, -1)" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-all text-xs cursor-pointer" title="Pindahkan Ke Atas">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                        @endif

                        @if($index < $workflow->steps->count() - 1)
                        <button type="button" @click="moveStep({{ $index }}, 1)" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-all text-xs cursor-pointer" title="Pindahkan Ke Bawah">
                            <i class="fa-solid fa-arrow-down"></i>
                        </button>
                        @endif

                        <button type="button" @click="openEditModal({{ json_encode($step) }})" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-primary text-xs font-bold transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </button>

                        <form action="{{ route('master.approval-workflow.step.destroy', $step->id) }}" method="POST" onsubmit="return confirm('Hapus step approval [{{ $step->step_name }}]? Alur yang sedang berjalan akan menyesuaikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-slate-400 space-y-3">
                <i class="fa-solid fa-diagram-project text-4xl text-slate-300"></i>
                <p class="text-sm font-medium">Belum ada tahapan approval yang dibuat.</p>
                <button type="button" @click="openCreateModal()" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md cursor-pointer">
                    Tambah Step Sekarang
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL FORM: TAMBAH / EDIT STEP APPROVAL -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div @click.away="showModal = false" class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid" :class="isEdit ? 'fa-pen-to-square' : 'fa-plus'"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800" x-text="isEdit ? 'Edit Step Approval' : 'Tambah Step Approval Baru'"></h3>
                        <p class="text-[11px] text-slate-500">Konfigurasi hak akses approver, kondisi area &amp; entitas dinamis (searchable)</p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="formAction" method="POST" class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="workflow_id" value="{{ $workflow->id }}">
                <input type="hidden" name="rules_json" :value="JSON.stringify(formData.rules)">

                <!-- 1. Pilihan Tipe Approver: Head vs Akun User -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Tipe Approver</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="p-3.5 rounded-xl border-2 cursor-pointer transition-all flex items-start gap-3"
                            :class="formData.approver_type === 'head' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-950 shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="approver_type" value="head" x-model="formData.approver_type" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <div class="font-bold text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-tie text-indigo-600"></i> Head / Pimpinan
                                </div>
                                <div class="text-[10.5px] text-slate-500 leading-snug mt-1">
                                    Otomatis diarahkan ke Head/Pimpinan rekruter sesuai data di Master Karyawan.
                                </div>
                            </div>
                        </label>

                        <label class="p-3.5 rounded-xl border-2 cursor-pointer transition-all flex items-start gap-3"
                            :class="formData.approver_type === 'user' ? 'border-primary bg-primary/5 text-slate-900 shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="approver_type" value="user" x-model="formData.approver_type" class="mt-0.5 text-primary focus:ring-primary">
                            <div>
                                <div class="font-bold text-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-users text-primary"></i> Akun User Tertentu
                                </div>
                                <div class="text-[10.5px] text-slate-500 leading-snug mt-1">
                                    Pilih user dengan aturan kondisi Area &amp; Prinsiple dinamis (mendukung multiple user).
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Nama Step & Urutan -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Step Approval <span class="text-rose-500">*</span></label>
                        <input type="text" name="step_name" x-model="formData.step_name" required placeholder="Contoh: Persetujuan Head Approver / Review HRD" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Step <span class="text-rose-500">*</span></label>
                        <input type="number" name="step_order" x-model="formData.step_order" min="1" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none">
                    </div>
                </div>

                <!-- 3. Aturan Khusus jika Tipe Head: Pengecualian Direksi -->
                <div x-show="formData.approver_type === 'head'" class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="skip_if_direksi" value="1" x-model="formData.skip_if_direksi" class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-bold text-slate-800">Lewati (Skip) step ini jika Pimpinan Rekruter adalah Direksi / Direktur / BOD</span>
                    </label>
                    <p class="text-[10.5px] text-slate-500 pl-5 leading-relaxed">
                        Jika dicentang, apabila kandidat ditangani oleh rekruter yang berpimpinan langsung Direksi, tahapan ini akan otomatis dilewati dan langsung lanjut ke step berikutnya.
                    </p>
                </div>

                <!-- 4. ATURAN PEMETAAN DINAMIS: AREA, PRINSIPLE & USER (Khusus Tipe Akun User) -->
                <div x-show="formData.approver_type === 'user'" class="space-y-3 border-t border-slate-100 pt-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold text-slate-800">
                                Aturan Pemetaan Area, Prinsiple &amp; Approver (Dinamis)
                            </label>
                            <p class="text-[11px] text-slate-500">
                                Tambahkan kombinasi Area dan Prinsiple beserta PIC User yang ditugaskan. Seluruh pilihan kini <strong>Searchable</strong>.
                            </p>
                        </div>
                        <span class="text-[10.5px] text-primary font-bold px-2 py-0.5 rounded-lg bg-primary/10" x-text="formData.rules.length + ' Aturan'"></span>
                    </div>

                    <!-- Repeater Baris Aturan -->
                    <div class="space-y-3.5">
                        <template x-for="(rule, rIdx) in formData.rules" :key="rule.id">
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-3 relative shadow-2xs">
                                
                                <!-- Header Baris Aturan -->
                                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-slate-900 text-white font-bold text-[11px] flex items-center justify-center" x-text="rIdx + 1"></span>
                                        <span class="text-xs font-bold text-slate-700">Kombinasi Kondisi #<span x-text="rIdx + 1"></span></span>
                                    </div>
                                    <button type="button" @click="removeRule(rIdx)" x-show="formData.rules.length > 1" class="text-slate-400 hover:text-rose-600 transition-colors p-1 cursor-pointer" title="Hapus Aturan Ini">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>

                                <!-- Grid Pilihan Area & Prinsiple (Keduanya Searchable) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    
                                    <!-- 1. Pilihan Area (Searchable Dropdown) -->
                                    <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false; search = ''">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1 flex items-center justify-between">
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-location-dot text-indigo-500"></i>
                                                <span>Area Penempatan</span>
                                            </span>
                                            <span class="text-[9.5px] text-slate-400 font-normal">Searchable</span>
                                        </label>
                                        
                                        <!-- Trigger Button -->
                                        <button type="button" 
                                                @click="open = !open; if(open) $nextTick(() => $refs.areaSearchInput.focus())" 
                                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white hover:border-primary/50 text-left flex items-center justify-between gap-2 shadow-2xs cursor-pointer transition-all">
                                            <span class="font-bold text-slate-800 truncate" x-text="getAreaLabel(rule.area)"></span>
                                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }"></i>
                                        </button>

                                        <!-- Dropdown Menu Popover -->
                                        <div x-show="open" 
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl p-2 space-y-1.5 max-h-64 overflow-hidden flex flex-col">
                                            
                                            <!-- Search Input -->
                                            <div class="relative shrink-0">
                                                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                                <input type="text" 
                                                       x-ref="areaSearchInput"
                                                       x-model="search" 
                                                       placeholder="Ketik untuk mencari area..." 
                                                       class="w-full pl-8 pr-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            </div>

                                            <!-- Options List -->
                                            <div class="overflow-y-auto divide-y divide-slate-50 flex-1 max-h-48">
                                                <template x-for="opt in filterAreaOptions(search)" :key="opt.value">
                                                    <button type="button" 
                                                            @click="rule.area = opt.value; open = false; search = ''" 
                                                            class="w-full p-2 rounded-lg text-left hover:bg-slate-50 flex items-center justify-between text-xs cursor-pointer transition-colors"
                                                            :class="rule.area === opt.value ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700'">
                                                        <div class="min-w-0">
                                                            <div class="truncate font-semibold" x-text="opt.label"></div>
                                                            <div class="text-[9.5px] text-slate-400 truncate" x-text="opt.desc"></div>
                                                        </div>
                                                        <i class="fa-solid fa-check text-xs text-primary" x-show="rule.area === opt.value"></i>
                                                    </button>
                                                </template>
                                                <div x-show="filterAreaOptions(search).length === 0" class="p-3 text-center text-xs text-slate-400 italic">
                                                    Area tidak ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Pilihan Prinsiple / Entitas (Searchable Dropdown) -->
                                    <div class="relative" x-data="{ open: false, search: '' }" @click.outside="open = false; search = ''">
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1 flex items-center justify-between">
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-building text-amber-500"></i>
                                                <span>Prinsiple / Entitas</span>
                                            </span>
                                            <span class="text-[9.5px] text-slate-400 font-normal">Searchable</span>
                                        </label>
                                        
                                        <!-- Trigger Button -->
                                        <button type="button" 
                                                @click="open = !open; if(open) $nextTick(() => $refs.prinSearchInput.focus())" 
                                                class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white hover:border-primary/50 text-left flex items-center justify-between gap-2 shadow-2xs cursor-pointer transition-all">
                                            <span class="font-bold text-slate-800 truncate" x-text="getPrinLabel(rule.prinsiple)"></span>
                                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }"></i>
                                        </button>

                                        <!-- Dropdown Menu Popover -->
                                        <div x-show="open" 
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl p-2 space-y-1.5 max-h-64 overflow-hidden flex flex-col">
                                            
                                            <!-- Search Input -->
                                            <div class="relative shrink-0">
                                                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                                                <input type="text" 
                                                       x-ref="prinSearchInput"
                                                       x-model="search" 
                                                       placeholder="Ketik kode entitas (AMK, AKP...) atau nama..." 
                                                       class="w-full pl-8 pr-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            </div>

                                            <!-- Options List -->
                                            <div class="overflow-y-auto divide-y divide-slate-50 flex-1 max-h-48">
                                                <template x-for="opt in filterPrinOptions(search)" :key="opt.value">
                                                    <button type="button" 
                                                            @click="rule.prinsiple = opt.value; open = false; search = ''" 
                                                            class="w-full p-2 rounded-lg text-left hover:bg-slate-50 flex items-center justify-between text-xs cursor-pointer transition-colors"
                                                            :class="rule.prinsiple === opt.value ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700'">
                                                        <div class="min-w-0">
                                                            <div class="truncate font-semibold" x-text="opt.label"></div>
                                                            <div class="text-[9.5px] text-slate-400 truncate" x-text="opt.desc"></div>
                                                        </div>
                                                        <i class="fa-solid fa-check text-xs text-primary" x-show="rule.prinsiple === opt.value"></i>
                                                    </button>
                                                </template>
                                                <div x-show="filterPrinOptions(search).length === 0" class="p-3 text-center text-xs text-slate-400 italic">
                                                    Prinsiple / Entitas tidak ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Pilihan Akun Approver (Searchable Multi-Select) -->
                                <div class="space-y-1.5 pt-1" x-data="{ open: false, search: '' }" @click.outside="open = false; search = ''">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-check text-primary text-[11px]"></i>
                                            <span>Pilih Akun Approver <span class="text-primary font-normal">(Bisa memilih lebih dari 1 user)</span></span>
                                        </label>
                                        <span class="text-[10px] text-slate-500 font-semibold" x-text="rule.user_ids.length + ' User Terpilih'"></span>
                                    </div>

                                    <!-- Tag list user yang terpilih -->
                                    <div class="flex flex-wrap gap-1.5 min-h-[36px] p-2 bg-white rounded-xl border border-slate-200">
                                        <template x-for="uid in rule.user_ids" :key="uid">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-primary/10 text-primary border border-primary/20 shadow-2xs">
                                                <i class="fa-solid fa-user-check text-[9px] text-primary"></i>
                                                <span x-text="getUserName(uid)"></span>
                                                <button type="button" @click="toggleUserInRule(rIdx, uid)" class="hover:text-rose-600 cursor-pointer ml-0.5">
                                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                                </button>
                                            </span>
                                        </template>
                                        <span x-show="rule.user_ids.length === 0" class="text-[11px] text-slate-400 italic py-0.5">
                                            Belum ada user dipilih. Klik tombol di bawah untuk mencari &amp; menambah approver...
                                        </span>
                                    </div>

                                    <!-- Trigger Button & Popover Searchable Users -->
                                    <div class="relative">
                                        <button type="button" 
                                                @click="open = !open; if(open) $nextTick(() => $refs.userSearchInput.focus())" 
                                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 bg-white hover:border-primary/50 text-slate-600 hover:text-slate-900 text-left flex items-center justify-between gap-2 shadow-2xs cursor-pointer transition-all">
                                            <span class="font-medium text-slate-600 flex items-center gap-1.5">
                                                <i class="fa-solid fa-user-plus text-primary text-xs"></i>
                                                <span>+ Tambah User Approver untuk Kondisi Ini...</span>
                                            </span>
                                            <div class="flex items-center gap-1 text-slate-400">
                                                <span class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded font-bold">Searchable</span>
                                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-150" :class="{ 'rotate-180': open }"></i>
                                            </div>
                                        </button>

                                        <!-- Dropdown Popover Searchable Users -->
                                        <div x-show="open" 
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="absolute z-50 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl p-2 space-y-2 max-h-72 overflow-hidden flex flex-col">
                                            
                                            <!-- Search Box -->
                                            <div class="relative shrink-0">
                                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                                <input type="text" 
                                                       x-ref="userSearchInput"
                                                       x-model="search" 
                                                       placeholder="Ketik nama, email, jabatan, atau area karyawan..." 
                                                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                                            </div>

                                            <!-- List of Filtered Users -->
                                            <div class="overflow-y-auto divide-y divide-slate-100 flex-1 max-h-56">
                                                <template x-for="u in filterUsersList(search)" :key="u.id">
                                                    <button type="button" 
                                                            @click="toggleUserInRule(rIdx, u.id)" 
                                                            class="w-full p-2.5 rounded-xl text-left hover:bg-slate-50 flex items-center justify-between gap-3 text-xs cursor-pointer transition-colors"
                                                            :class="rule.user_ids.includes(u.id) ? 'bg-primary/5 text-primary' : 'text-slate-800'">
                                                        <div class="min-w-0">
                                                            <div class="font-bold flex items-center gap-1.5 truncate">
                                                                <span x-text="u.name"></span>
                                                                <span x-show="rule.user_ids.includes(u.id)" class="text-[9.5px] px-1.5 py-0.2 rounded bg-primary text-white font-bold">Terpilih</span>
                                                            </div>
                                                            <div class="text-[10.5px] text-slate-400 truncate">
                                                                <span x-text="u.email"></span> &bull; 
                                                                <span x-text="u.job_title || u.role"></span>
                                                                <span x-show="u.area" x-text="' (' + u.area + ')'"></span>
                                                            </div>
                                                        </div>
                                                        <div class="shrink-0">
                                                            <span x-show="rule.user_ids.includes(u.id)" class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">
                                                                <i class="fa-solid fa-check"></i>
                                                            </span>
                                                            <span x-show="!rule.user_ids.includes(u.id)" class="w-5 h-5 rounded-full border border-slate-300 text-slate-400 flex items-center justify-center text-[10px] hover:border-primary hover:text-primary">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </span>
                                                        </div>
                                                    </button>
                                                </template>
                                                <div x-show="filterUsersList(search).length === 0" class="p-4 text-center text-xs text-slate-400 italic">
                                                    Karyawan / user tidak ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Tombol Tambah Aturan Baru -->
                    <button type="button" @click="addRule()" class="w-full py-2.5 rounded-xl border-2 border-dashed border-primary/30 hover:border-primary bg-primary/5 hover:bg-primary/10 text-primary text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>+ Tambah Aturan Area &amp; Prinsiple Baru</span>
                    </button>
                </div>

                <!-- 5. Deskripsi / Catatan Tambahan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Deskripsi Tambahan</label>
                    <textarea name="description" x-model="formData.description" rows="2" placeholder="Catatan internal peruntukan step ini..." class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none"></textarea>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold transition-all shadow-md shadow-primary/20 flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Step'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function workflowManager() {
    return {
        showModal: false,
        isEdit: false,
        formAction: '',
        areaOptions: @json($areaOptionsList),
        principleOptions: @json($principleOptionsList),
        usersList: @json($availableUsers),
        formData: {
            step_name: '',
            step_order: {{ $workflow->steps->count() + 1 }},
            approver_type: 'user',
            skip_if_direksi: false,
            description: '',
            rules: [],
        },

        getUserName(userId) {
            const uid = parseInt(userId);
            const u = this.usersList.find(x => x.id === uid);
            return u ? u.name : 'User #' + uid;
        },

        getAreaLabel(areaValue) {
            const found = this.areaOptions.find(o => o.value === areaValue);
            return found ? found.label : (areaValue === 'ALL' ? 'Semua Area (Nasional)' : areaValue);
        },

        filterAreaOptions(query) {
            if (!query || !query.trim()) return this.areaOptions;
            const q = query.toLowerCase().trim();
            return this.areaOptions.filter(o => o.label.toLowerCase().includes(q) || o.value.toLowerCase().includes(q));
        },

        getPrinLabel(prinValue) {
            const found = this.principleOptions.find(o => o.value === prinValue);
            return found ? found.label : (prinValue === 'ALL' ? 'Semua Prinsiple / Entitas' : prinValue);
        },

        filterPrinOptions(query) {
            if (!query || !query.trim()) return this.principleOptions;
            const q = query.toLowerCase().trim();
            return this.principleOptions.filter(o => o.label.toLowerCase().includes(q) || o.value.toLowerCase().includes(q));
        },

        filterUsersList(query) {
            if (!query || !query.trim()) return this.usersList.slice(0, 100);
            const q = query.toLowerCase().trim();
            return this.usersList.filter(u => {
                const text = (u.name + ' ' + u.email + ' ' + (u.job_title || '') + ' ' + (u.area || '')).toLowerCase();
                return text.includes(q);
            }).slice(0, 100);
        },

        addRule() {
            this.formData.rules.push({
                id: 'rule_' + Date.now() + '_' + Math.random().toString(36).substring(2, 5),
                area: 'ALL',
                prinsiple: 'ALL',
                user_ids: [],
            });
        },

        removeRule(index) {
            if (this.formData.rules.length > 1) {
                this.formData.rules.splice(index, 1);
            }
        },

        addUserToRule(rIdx, userId) {
            if (!userId) return;
            const uid = parseInt(userId);
            if (!this.formData.rules[rIdx].user_ids.includes(uid)) {
                this.formData.rules[rIdx].user_ids.push(uid);
            }
        },

        toggleUserInRule(rIdx, userId) {
            const uid = parseInt(userId);
            const idx = this.formData.rules[rIdx].user_ids.indexOf(uid);
            if (idx === -1) {
                this.formData.rules[rIdx].user_ids.push(uid);
            } else {
                this.formData.rules[rIdx].user_ids.splice(idx, 1);
            }
        },

        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route("master.approval-workflow.step.store") }}';
            this.formData = {
                step_name: '',
                step_order: {{ $workflow->steps->count() + 1 }},
                approver_type: 'user',
                skip_if_direksi: false,
                description: '',
                rules: [
                    {
                        id: 'rule_' + Date.now(),
                        area: 'ALL',
                        prinsiple: 'ALL',
                        user_ids: [],
                    }
                ],
            };
            this.showModal = true;
        },

        openEditModal(step) {
            this.isEdit = true;
            this.formAction = `/master/approval-workflow/step/${step.id}`;

            let loadedRules = [];
            if (step.approval_rules && Array.isArray(step.approval_rules) && step.approval_rules.length > 0) {
                loadedRules = step.approval_rules.map(r => ({
                    id: r.id || ('rule_' + Math.random().toString(36).substring(2, 6)),
                    area: r.area || 'ALL',
                    prinsiple: r.prinsiple || 'ALL',
                    user_ids: r.user_ids ? r.user_ids.map(Number) : (r.users ? r.users.map(u => Number(u.id)) : []),
                }));
            } else if (step.step_users && step.step_users.length > 0) {
                // Fallback dari step_users legacy
                loadedRules = [
                    {
                        id: 'rule_' + Date.now(),
                        area: step.area_scope || 'ALL',
                        prinsiple: step.entity_scope || 'ALL',
                        user_ids: step.step_users.map(u => Number(u.user_id)).filter(Boolean),
                    }
                ];
            } else {
                loadedRules = [
                    {
                        id: 'rule_' + Date.now(),
                        area: step.area_scope || 'ALL',
                        prinsiple: step.entity_scope || 'ALL',
                        user_ids: [],
                    }
                ];
            }

            this.formData = {
                step_name: step.step_name,
                step_order: step.step_order,
                approver_type: step.approver_type,
                skip_if_direksi: !!step.skip_if_direksi,
                description: step.description || '',
                rules: loadedRules,
            };
            this.showModal = true;
        },

        async moveStep(currentIndex, direction) {
            const steps = @json($workflow->steps->pluck('id'));
            const targetIndex = currentIndex + direction;
            if (targetIndex < 0 || targetIndex >= steps.length) return;

            // Swap
            const temp = steps[currentIndex];
            steps[currentIndex] = steps[targetIndex];
            steps[targetIndex] = temp;

            try {
                const res = await fetch('{{ route("master.approval-workflow.step.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ step_ids: steps })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Gagal mengubah urutan: ' + (data.message || 'Error'));
                }
            } catch (err) {
                alert('Terjadi kesalahan jaringan.');
            }
        }
    };
}
</script>
@endsection
