@extends('layouts.app')

@section('title', 'Master Template Laporan Kendala')

@section('content')
<div class="space-y-6" x-data="{
    openCreate: false,
    openEdit: false,
    editData: {},
    setEdit(tpl) {
        this.editData = JSON.parse(JSON.stringify(tpl));
        this.openEdit = true;
    }
}">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                <span>/</span>
                <span class="text-slate-800">Master Template Laporan</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Template Laporan Kendala</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola jenis template kendala dan format isian baku untuk memudahkan pengajuan tiket antar divisi.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('helpdesk.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-xs transition-all">
                <i class="fa-solid fa-arrow-left text-slate-400"></i>
                <span>Kembali</span>
            </a>
            <button @click="openCreate = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Tambah Template Baru</span>
            </button>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <form method="GET" action="{{ route('helpdesk.templates.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama template, subjek, atau isi teks..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition-all">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                @if(request('search'))
                <a href="{{ route('helpdesk.templates.index', request()->except('search')) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>

            <select name="division_id" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:outline-none focus:border-primary focus:bg-white">
                <option value="">Semua Divisi</option>
                <option value="general" {{ request('division_id') === 'general' ? 'selected' : '' }}>Umum (Tanpa Divisi Tertentu)</option>
                @foreach($divisions as $d)
                <option value="{{ $d->id }}" {{ request('division_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </form>

        <span class="text-xs font-bold text-slate-500">
            Total: <strong class="text-slate-800">{{ $templates->count() }}</strong> Template
        </span>
    </div>

    <!-- TABEL TEMPLATE -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-extrabold text-[11px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Template</th>
                        <th class="py-3.5 px-4">Divisi & Subjek Default</th>
                        <th class="py-3.5 px-4">Format Isian Deskripsi</th>
                        <th class="py-3.5 px-4">Format Dokumen Lampiran</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($templates as $index => $t)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                            {{ $index + 1 }}
                        </td>
                        <td class="py-3.5 px-4 align-top">
                            <strong class="text-slate-800 text-xs font-bold block">{{ $t->title }}</strong>
                            <span class="text-[10px] text-slate-400">Urutan: #{{ $t->order_num }}</span>
                        </td>
                        <td class="py-3.5 px-4 align-top space-y-1">
                            @if($t->division)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border text-[10px] font-bold {{ $t->division->color_badge }}">
                                <i class="{{ $t->division->icon }} text-[9px]"></i>
                                <span>{{ $t->division->name }}</span>
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">
                                <i class="fa-solid fa-asterisk text-[9px]"></i>
                                <span>Umum / Bebas</span>
                            </span>
                            @endif
                            <p class="text-slate-700 font-medium text-[11px] leading-tight mt-1">{{ $t->subject ?: '-' }}</p>
                        </td>
                        <td class="py-3.5 px-4 align-top max-w-xs">
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/70 font-mono text-[11px] text-slate-600 whitespace-pre-line line-clamp-3 leading-relaxed">
                                {{ $t->message }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 align-top">
                            @if($t->attachment)
                            <div class="space-y-1">
                                <a href="{{ Storage::url($t->attachment) }}" download 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 text-[11px] font-bold transition-all shadow-2xs">
                                    <i class="fa-solid fa-file-arrow-down text-blue-600"></i>
                                    <span class="truncate max-w-[130px]">{{ basename($t->attachment) }}</span>
                                </a>
                                <form method="POST" action="{{ route('helpdesk.templates.attachment.remove', $t->id) }}" onsubmit="return confirm('Hapus file lampiran format dari template ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] text-rose-500 hover:underline block">Hapus File</button>
                                </form>
                            </div>
                            @else
                            <span class="text-slate-400 italic text-[11px]">Tidak Ada</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 align-top text-center">
                            @if($t->is_active)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">Aktif</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 align-top text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="setEdit({{ json_encode($t) }})" 
                                        class="p-1.5 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-xs font-bold transition-all" title="Edit Template">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <form method="POST" action="{{ route('helpdesk.templates.destroy', $t->id) }}" onsubmit="return confirm('Hapus template {{ $t->title }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 text-xs font-bold transition-all" title="Hapus Template">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <i class="fa-regular fa-clipboard text-4xl mb-2 text-slate-300"></i>
                            <p class="font-bold text-sm text-slate-600">Belum ada template laporan kendala.</p>
                            <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Template Baru" di atas untuk membuat template kendala baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL TAMBAH TEMPLATE BARU -->
    <div x-show="openCreate" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="openCreate = false" class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-2xl w-full p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-circle-plus"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Tambah Template Laporan Kendala</h3>
                </div>
                <button type="button" @click="openCreate = false" class="text-slate-400 hover:text-slate-600 text-base">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('helpdesk.templates.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama / Jenis Template <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Permohonan Reset Password Akun"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Divisi Terkait (Opsional)</label>
                        <select name="division_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white text-xs">
                            <option value="">-- Umum / Semua Divisi --</option>
                            @foreach($divisions as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Subjek Default (Auto-Fill)</label>
                    <input type="text" name="subject" placeholder="Contoh: [Permohonan IT] Reset Akun Email"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white text-xs">
                    <p class="text-[10px] text-slate-400 mt-0.5">Judul ini akan otomatis terisi saat pengaju memilih template ini.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Format Isian Deskripsi Kendala <span class="text-rose-500">*</span></label>
                    <textarea name="message" rows="6" required placeholder="Contoh:&#10;[DETAIL KENDALA]&#10;Nama:&#10;NIK:&#10;Kendala:&#10;Langkah yang dicoba:"
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white font-mono text-xs leading-relaxed"></textarea>
                    <p class="text-[10px] text-slate-400 mt-0.5">Format formulir pertanyaan/uraian kendala yang harus dilengkapi oleh pengaju tiket.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Unggah Berkas Format / Dokumen Acuan (Opsional)</label>
                    <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-[10px] text-slate-400 mt-0.5">Misal: form pengajuan format Excel, Word, atau PDF yang wajib diunduh & diisi pengaju (Maks. 10MB).</p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="openCreate = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold shadow-md shadow-primary/20">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT TEMPLATE -->
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="openEdit = false" class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-2xl w-full p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Edit Template Laporan Kendala</h3>
                </div>
                <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-slate-600 text-base">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'{{ url('/helpdesk/templates') }}/' + (editData ? editData.id : '')" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Nama / Jenis Template <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required x-model="editData.title"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Divisi Terkait</label>
                        <select name="division_id" x-model="editData.division_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white text-xs">
                            <option value="">-- Umum / Semua Divisi --</option>
                            @foreach($divisions as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Subjek Default</label>
                    <input type="text" name="subject" x-model="editData.subject"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Format Isian Deskripsi Kendala <span class="text-rose-500">*</span></label>
                    <textarea name="message" rows="6" required x-model="editData.message"
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:border-primary focus:bg-white font-mono text-xs leading-relaxed"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Ganti Berkas Format Lampiran</label>
                    <template x-if="editData && editData.attachment">
                        <p class="text-[11px] text-blue-600 font-bold mb-1 flex items-center gap-1">
                            <i class="fa-solid fa-file"></i>
                            <span>File saat ini: <span x-text="editData.attachment ? editData.attachment.split('/').pop() : ''"></span></span>
                        </p>
                    </template>
                    <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-[10px] text-slate-400 mt-0.5">Biarkan kosong jika tidak ingin mengubah berkas lampiran.</p>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="editData.is_active" class="rounded text-primary focus:ring-primary">
                        <span class="font-bold text-slate-700">Template Aktif</span>
                    </label>

                    <div class="flex items-center gap-2.5">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold shadow-md shadow-primary/20">
                            Perbarui Template
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
