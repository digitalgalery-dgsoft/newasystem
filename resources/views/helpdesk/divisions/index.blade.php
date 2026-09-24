@extends('layouts.app')

@section('title', 'Master Divisi & Agen Helpdesk')

@section('content')
<div class="space-y-6" x-data="{ modalAddDiv: false, editDivData: null }">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                <span>/</span>
                <span class="text-slate-800">Master Divisi</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Divisi & Agen Helpdesk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola divisi layanan dan petakan karyawan inhouse aktif sebagai agen responder.</p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <form method="POST" action="{{ route('helpdesk.divisions.sync_inhouse') }}" onsubmit="return confirm('Jalankan pemindaian dan sinkronisasi otomatis divisi berdasarkan Master Karyawan Inhouse?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-bold transition-all shadow-xs">
                    <i class="fa-solid fa-arrows-rotate text-indigo-600"></i>
                    <span>⚡ Sinkronkan dari Karyawan Inhouse</span>
                </button>
            </form>
            <button @click="modalAddDiv = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Divisi Baru</span>
            </button>
        </div>
    </div>

    <!-- DAFTAR DIVISI KARTU -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($divisions as $div)
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-all">
            <div class="space-y-4">
                <!-- TOP HEADER -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl {{ $div->color_badge }}">
                            <i class="{{ $div->icon }}"></i>
                        </div>
                        <div>
                            <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">{{ $div->code }}</span>
                            <h3 class="text-base font-bold text-slate-900 mt-0.5">{{ $div->name }}</h3>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $div->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                        {{ $div->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">{{ $div->description ?: 'Layanan bantuan divisi.' }}</p>

                <!-- SLA & INFO TIKET -->
                <div class="grid grid-cols-2 gap-2 text-xs p-3 bg-slate-50 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Target SLA</span>
                        <strong class="text-slate-800">{{ $div->sla_hours }} Jam</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Total Tiket</span>
                        <strong class="text-primary">{{ $div->tickets_count }} Tiket</strong>
                    </div>
                </div>

                <!-- WA GROUP LINK -->
                @if($div->wa_group_link)
                <a href="{{ $div->wa_group_link }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-bold hover:underline">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Grup WhatsApp Divisi</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                </a>
                @endif

                <!-- DAFTAR AGEN INHOUSE -->
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            Agen Responder ({{ $div->agents->count() }})
                        </span>
                    </div>

                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                        @forelse($div->agents as $ag)
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center gap-2 truncate">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0">
                                    {{ strtoupper(substr($ag->name, 0, 1)) }}
                                </div>
                                <div class="truncate">
                                    <strong class="block text-slate-800 truncate text-[11px]">{{ $ag->name }}</strong>
                                    <span class="text-[10px] text-slate-400 truncate">{{ $ag->job_title ?? $ag->email }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('helpdesk.divisions.agents.remove', [$div->id, $ag->id]) }}" onsubmit="return confirm('Hapus agen {{ $ag->name }} dari divisi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-600 p-1" title="Hapus Agen">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="text-[11px] text-slate-400 italic">Belum ada agen yang ditugaskan.</p>
                        @endforelse
                    </div>

                    <!-- FORM TAMBAH AGEN (SEARCHABLE COMBOBOX) -->
                    <div x-data="agentPickerComponent({{ json_encode($div->agents->pluck('id')->values()->all()) }})" class="relative mt-3 pt-3 border-t border-slate-100">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                            + Tambah Agen Karyawan
                        </label>
                        <form method="POST" action="{{ route('helpdesk.divisions.agents.add', $div->id) }}" class="flex items-center gap-1.5">
                            @csrf
                            <input type="hidden" name="user_id" :value="selectedUserId" required>

                            <div class="relative flex-1" @click.away="open = false">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        x-model="searchQuery" 
                                        @focus="open = true" 
                                        @input="open = true; if(selectedUserId && searchQuery !== selectedUserName) selectedUserId = ''"
                                        @keydown.escape="open = false"
                                        :placeholder="selectedUserName ? selectedUserName : 'Cari nama / divisi karyawan...'"
                                        class="w-full pl-7 pr-7 py-1.5 bg-slate-50 border rounded-xl text-xs text-slate-700 focus:outline-none focus:bg-white transition-all font-medium placeholder:text-slate-400"
                                        :class="selectedUserId ? 'border-primary ring-1 ring-primary/20 bg-blue-50/30' : 'border-slate-200 focus:border-primary'"
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                    </div>
                                    <button 
                                        type="button" 
                                        x-show="selectedUserId || searchQuery" 
                                        @click="clearSelection()" 
                                        class="absolute inset-y-0 right-0 pr-2 flex items-center text-slate-400 hover:text-rose-500"
                                        title="Hapus pilihan"
                                    >
                                        <i class="fa-solid fa-circle-xmark text-xs"></i>
                                    </button>
                                </div>

                                <!-- DROPDOWN SUGGESTIONS LIST -->
                                <div 
                                    x-show="open" 
                                    x-cloak 
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute left-0 right-0 top-full mt-1 z-50 max-h-52 overflow-y-auto bg-white rounded-xl shadow-2xl border border-slate-200 p-1 text-xs divide-y divide-slate-50"
                                >
                                    <template x-for="item in filteredAgents" :key="item.id">
                                        <div 
                                            @click="selectAgent(item)"
                                            class="p-2 rounded-lg hover:bg-blue-50 hover:text-primary transition-colors cursor-pointer flex items-center justify-between gap-2 group"
                                        >
                                            <div class="truncate">
                                                <span class="font-bold block truncate text-slate-800 group-hover:text-primary" x-text="item.name"></span>
                                                <span class="text-[10px] text-slate-400 block truncate" x-text="item.sub"></span>
                                            </div>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 group-hover:bg-primary group-hover:text-white transition-colors flex-shrink-0">
                                                Pilih
                                            </span>
                                        </div>
                                    </template>
                                    <div x-show="filteredAgents.length === 0" class="p-3 text-center text-slate-400 text-[11px] italic">
                                        Tidak ditemukan karyawan yang cocok.
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="!selectedUserId"
                                :class="selectedUserId ? 'bg-primary hover:bg-primary-600 text-white cursor-pointer shadow-xs' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                class="px-3 py-1.5 rounded-xl font-bold text-xs transition-all flex items-center gap-1 flex-shrink-0"
                            >
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span>Tambah</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- FOOTER AKSI DIVISI -->
            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-end gap-2 text-xs">
                <button @click="editDivData = {{ json_encode($div) }}" type="button" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                    <i class="fa-regular fa-pen-to-square mr-1"></i>Edit
                </button>
                <form method="POST" action="{{ route('helpdesk.divisions.destroy', $div->id) }}" onsubmit="return confirm('Hapus atau nonaktifkan divisi {{ $div->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold transition-all">
                        <i class="fa-regular fa-trash-can mr-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400">
            <i class="fa-solid fa-sitemap text-4xl mb-2 text-slate-300"></i>
            <p class="font-bold text-sm text-slate-600">Belum ada divisi helpdesk yang dibuat.</p>
            <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Sinkronkan dari Karyawan Inhouse" untuk otomatis membuat divisi standar.</p>
        </div>
        @endforelse
    </div>

    <!-- MODAL TAMBAH DIVISI -->
    <div x-show="modalAddDiv" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="modalAddDiv = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Tambah Divisi Baru</h3>
                <button @click="modalAddDiv = false" type="button" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('helpdesk.divisions.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: IT Support / Operasional" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Unik <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" required placeholder="IT, OPS, GA" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl uppercase font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Target SLA (Jam)</label>
                        <input type="number" name="sla_hours" value="24" min="1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tema Warna</label>
                        <select name="color" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="blue">Blue (Biru ESA)</option>
                            <option value="indigo">Indigo</option>
                            <option value="emerald">Emerald (Hijau)</option>
                            <option value="amber">Amber (Kuning/Oranye)</option>
                            <option value="rose">Rose (Merah)</option>
                            <option value="purple">Purple (Ungu)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Ikon FontAwesome</label>
                        <input type="text" name="icon" value="fa-solid fa-headset" placeholder="fa-solid fa-headset" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Layanan</label>
                    <textarea name="description" rows="2" placeholder="Uraikan cakupan kendala yang ditangani oleh divisi ini..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Link Grup WhatsApp (Opsional)</label>
                    <input type="url" name="wa_group_link" placeholder="https://chat.whatsapp.com/..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="modalAddDiv = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold shadow-md shadow-primary/20">Simpan Divisi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT DIVISI -->
    <div x-show="editDivData" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="editDivData = null">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Edit Divisi</h3>
                <button @click="editDivData = null" type="button" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form :action="'{{ url('/helpdesk/divisions') }}/' + (editDivData ? editDivData.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" :value="editDivData?.name" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kode Unik <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" :value="editDivData?.code" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl uppercase font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Target SLA (Jam)</label>
                        <input type="number" name="sla_hours" :value="editDivData?.sla_hours" min="1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tema Warna</label>
                        <select name="color" :value="editDivData?.color" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="blue">Blue (Biru ESA)</option>
                            <option value="indigo">Indigo</option>
                            <option value="emerald">Emerald (Hijau)</option>
                            <option value="amber">Amber (Kuning/Oranye)</option>
                            <option value="rose">Rose (Merah)</option>
                            <option value="purple">Purple (Ungu)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Ikon FontAwesome</label>
                        <input type="text" name="icon" :value="editDivData?.icon" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Layanan</label>
                    <textarea name="description" rows="2" :value="editDivData?.description" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Link Grup WhatsApp</label>
                    <input type="url" name="wa_group_link" :value="editDivData?.wa_group_link" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editDivData?.is_active" class="rounded text-primary">
                    <label for="edit_is_active" class="font-bold text-slate-700">Status Aktif</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="editDivData = null" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold shadow-md shadow-primary/20">Perbarui Divisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const INHOUSE_AGENTS = {!! json_encode($inhouseAgentsList ?? []) !!};

    function agentPickerComponent(existingAgentIds = []) {
        return {
            open: false,
            searchQuery: '',
            selectedUserId: '',
            selectedUserName: '',
            existingAgentIds: existingAgentIds || [],
            get filteredAgents() {
                const available = INHOUSE_AGENTS.filter(a => !this.existingAgentIds.includes(a.id));
                if (!this.searchQuery) {
                    return available.slice(0, 30);
                }
                const q = this.searchQuery.toLowerCase().trim();
                return available.filter(a => 
                    a.name.toLowerCase().includes(q) || a.sub.toLowerCase().includes(q)
                ).slice(0, 30);
            },
            selectAgent(item) {
                this.selectedUserId = item.id;
                this.selectedUserName = item.name;
                this.searchQuery = item.name;
                this.open = false;
            },
            clearSelection() {
                this.selectedUserId = '';
                this.selectedUserName = '';
                this.searchQuery = '';
                this.open = false;
            }
        };
    }
</script>
@endsection
