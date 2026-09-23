@extends('layouts.app')

@section('title', 'Manajemen Alur Approver Dinamis - ASystem')

@section('page_title', 'ALUR APPROVER DINAMIS')

@section('content')
<div class="space-y-6" x-data="workflowManager()">

    <!-- HEADER & ACTIONS -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-primary/10 text-primary border border-primary/20">
                    MASTER APPROVAL
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Modul: {{ strtoupper(str_replace('_', ' ', $workflow->module)) }}</span>
            </div>
            <h1 class="text-xl font-black tracking-tight text-slate-800">
                Alur &amp; Role Akses Approver Dinamis
            </h1>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Kelola tahapan approval, penugasan approver (Head / Akun User), aturan pengecualian Direksi, dan kondisi formasi (Area Jakarta vs Luar Jakarta serta Entitas Inhouse).
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('interviewinhouse.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-arrow-left text-slate-400"></i>
                <span>Lihat Kandidat Inhouse</span>
            </a>
            <button type="button" @click="openCreateModal()" class="px-4 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold transition-all shadow-md shadow-primary/20 flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Step Baru</span>
            </button>
        </div>
    </div>

    <!-- NOTIFIKASI SUKSES / ERROR -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span class="font-bold">{!! session('success') !!}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i class="fa-solid fa-xmark"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
            <span class="font-bold">{!! session('error') !!}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900"><i class="fa-solid fa-xmark"></i></button>
    </div>
    @endif

    <!-- PIPELINE / TIMELINE STEP LIST -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-diagram-project text-primary text-sm"></i>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Daftar Urutan Tahapan Approval (Pipeline)</h2>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                Total: {{ $workflow->steps->count() }} Tahapan
            </span>
        </div>

        <div class="p-6 space-y-4">
            @forelse($workflow->steps as $index => $step)
            <div class="relative group bg-white border border-slate-200/90 rounded-2xl p-5 hover:border-primary/50 hover:shadow-md transition-all">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    
                    <!-- Kiri: Nomor Step & Info Utama -->
                    <div class="flex items-start gap-4">
                        <!-- Step Badge -->
                        <div class="w-10 h-10 rounded-2xl {{ $step->approver_type === 'head' ? 'bg-indigo-600 text-white shadow-indigo-500/20' : 'bg-primary text-white shadow-primary/20' }} shadow-md flex items-center justify-center font-black text-sm shrink-0">
                            {{ $step->step_order }}
                        </div>

                        <!-- Step Detail -->
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-black text-slate-800 tracking-tight">
                                    {{ $step->step_name }}
                                </h3>

                                <!-- Badge Tipe Approver -->
                                @if($step->approver_type === 'head')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center gap-1">
                                        <i class="fa-solid fa-user-tie text-[9px]"></i> Head / Pimpinan (Master Karyawan)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200 flex items-center gap-1">
                                        <i class="fa-solid fa-users text-[9px]"></i> Akun User ({{ $step->stepUsers->count() }} Terpilih)
                                    </span>
                                @endif

                                <!-- Badge Scope Area -->
                                @if($step->area_scope === 'ALL')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Semua Area
                                    </span>
                                @elseif($step->area_scope === 'JAKARTA')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Khusus Jakarta
                                    </span>
                                @elseif($step->area_scope === 'OUTSIDE_JAKARTA')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Selain Jakarta (Luar Jakarta)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        Area: {{ $step->area_scope }}
                                    </span>
                                @endif

                                <!-- Badge Scope Entitas -->
                                @if($step->entity_scope === 'ALL')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Semua Entitas
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        Entitas: {{ $step->entity_scope }}
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

                            <!-- Daftar Approver Terpilih (Jika Tipe Akun) -->
                            @if($step->approver_type === 'user')
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
                        <button type="button" @click="moveStep({{ $index }}, -1)" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-all text-xs" title="Pindahkan Ke Atas">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                        @endif

                        @if($index < $workflow->steps->count() - 1)
                        <button type="button" @click="moveStep({{ $index }}, 1)" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-all text-xs" title="Pindahkan Ke Bawah">
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
                <button type="button" @click="openCreateModal()" class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md">
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
                        <p class="text-[11px] text-slate-500">Konfigurasi hak akses approver, kondisi area &amp; entitas</p>
                    </div>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="formAction" method="POST" class="p-6 space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="workflow_id" value="{{ $workflow->id }}">

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
                                    Pilih satu atau beberapa akun karyawan/user tertentu (mendukung multiple approver).
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 2. Nama Step & Urutan -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Step Approval <span class="text-rose-500">*</span></label>
                        <input type="text" name="step_name" x-model="formData.step_name" required placeholder="Contoh: Persetujuan Head Approver / Review HRD Jakarta" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Step <span class="text-rose-500">*</span></label>
                        <input type="number" name="step_order" x-model="formData.step_order" min="1" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none">
                    </div>
                </div>

                <!-- 3. Kondisi Area & Entitas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Area</label>
                        <select name="area_scope" x-model="formData.area_scope" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none cursor-pointer">
                            <option value="ALL">Semua Area (Nasional)</option>
                            <option value="JAKARTA">Khusus Area Jakarta</option>
                            <option value="OUTSIDE_JAKARTA">Selain Area Jakarta (Luar Jakarta / Daerah)</option>
                            @foreach($areas as $ar)
                                <option value="{{ $ar }}">{{ $ar }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Step hanya akan aktif jika formasi kandidat sesuai kondisi area ini.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Entitas Inhouse</label>
                        <select name="entity_scope" x-model="formData.entity_scope" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none cursor-pointer">
                            <option value="ALL">Semua Entitas Inhouse</option>
                            @foreach($entities as $code => $entName)
                                <option value="{{ $code }}">{{ $entName }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">Step hanya berlaku untuk kandidat dari entitas resmi terpilih.</p>
                    </div>
                </div>

                <!-- 4. Aturan Khusus: Pengecualian Direksi -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="skip_if_direksi" value="1" x-model="formData.skip_if_direksi" class="rounded text-primary focus:ring-primary">
                        <span class="text-xs font-bold text-slate-800">Lewati (Skip) step ini jika Pimpinan Rekruter adalah Direksi / Direktur / BOD</span>
                    </label>
                    <p class="text-[10.5px] text-slate-500 pl-5 leading-relaxed">
                        Jika dicentang, apabila kandidat ditangani oleh rekruter yang berpimpinan langsung Direksi, tahapan ini akan otomatis dilewati dan langsung lanjut ke step berikutnya.
                    </p>
                </div>

                <!-- 5. Pilihan Akun Approver (Khusus jika Tipe Akun User) -->
                <div x-show="formData.approver_type === 'user'" class="space-y-2 border-t border-slate-100 pt-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">
                            Pilih Akun Approver <span class="text-primary font-normal">(Bisa memilih lebih dari 1 user)</span>
                        </label>
                        <span class="text-[10px] text-slate-500 font-bold" x-text="selectedUserIds.length + ' User Terpilih'"></span>
                    </div>

                    <!-- Filter / Cari User -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="userSearch" placeholder="Ketik nama, email, atau jabatan user..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                    </div>

                    <!-- List User Checkboxes -->
                    <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-xl divide-y divide-slate-100 bg-slate-50/50 p-1">
                        @foreach($availableUsers as $userItem)
                        <label x-show="filterUser('{{ strtolower($userItem->name) }}', '{{ strtolower($userItem->email) }}', '{{ strtolower($userItem->job_title ?? '') }}')" class="p-2 rounded-lg hover:bg-white flex items-center justify-between gap-3 text-xs cursor-pointer transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <input type="checkbox" name="user_ids[]" value="{{ $userItem->id }}" :checked="selectedUserIds.includes({{ $userItem->id }})" @change="toggleUser({{ $userItem->id }})" class="rounded text-primary focus:ring-primary">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 truncate">{{ $userItem->name }}</div>
                                    <div class="text-[10.5px] text-slate-400 truncate">{{ $userItem->email }} &bull; {{ $userItem->job_title ?: 'Karyawan' }} {{ $userItem->area ? "({$userItem->area})" : '' }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 shrink-0 uppercase">{{ $userItem->role }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- 6. Deskripsi / Catatan Tambahan -->
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
        userSearch: '',
        selectedUserIds: [],
        formData: {
            step_name: '',
            step_order: {{ $workflow->steps->count() + 1 }},
            approver_type: 'user',
            area_scope: 'ALL',
            entity_scope: 'ALL',
            skip_if_direksi: false,
            description: '',
        },

        openCreateModal() {
            this.isEdit = false;
            this.formAction = '{{ route("master.approval-workflow.step.store") }}';
            this.selectedUserIds = [];
            this.userSearch = '';
            this.formData = {
                step_name: '',
                step_order: {{ $workflow->steps->count() + 1 }},
                approver_type: 'user',
                area_scope: 'ALL',
                entity_scope: 'ALL',
                skip_if_direksi: false,
                description: '',
            };
            this.showModal = true;
        },

        openEditModal(step) {
            this.isEdit = true;
            this.formAction = `/master/approval-workflow/step/${step.id}`;
            this.userSearch = '';
            this.selectedUserIds = step.step_users ? step.step_users.map(u => u.user_id).filter(Boolean) : [];
            this.formData = {
                step_name: step.step_name,
                step_order: step.step_order,
                approver_type: step.approver_type,
                area_scope: step.area_scope || 'ALL',
                entity_scope: step.entity_scope || 'ALL',
                skip_if_direksi: !!step.skip_if_direksi,
                description: step.description || '',
            };
            this.showModal = true;
        },

        toggleUser(userId) {
            const index = this.selectedUserIds.indexOf(userId);
            if (index === -1) {
                this.selectedUserIds.push(userId);
            } else {
                this.selectedUserIds.splice(index, 1);
            }
        },

        filterUser(name, email, job) {
            if (!this.userSearch) return true;
            const q = this.userSearch.toLowerCase();
            return name.includes(q) || email.includes(q) || job.includes(q);
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
