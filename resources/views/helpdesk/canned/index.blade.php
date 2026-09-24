@extends('layouts.app')

@section('title', 'Template Balasan Cepat Helpdesk')

@section('content')
<div class="space-y-6" x-data="{ modalAddCanned: false }">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('helpdesk.index') }}" class="hover:text-primary transition-colors">Helpdesk</a>
                <span>/</span>
                <span class="text-slate-800">Template Balasan Cepat</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Template Balasan Cepat (Canned Responses)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Simpan teks respon standar untuk mempercepat penanganan keluhan oleh agen divisi.</p>
        </div>
        <button @click="modalAddCanned = true" type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary-600 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Template Baru</span>
        </button>
    </div>

    <!-- LIST CANNED RESPONSES -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cannedResponses as $canned)
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition-all">
            <div class="space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-800">{{ $canned->title }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                        {{ $canned->division->name ?? 'Semua Divisi' }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line max-h-36 overflow-y-auto">
                    {{ $canned->content }}
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-[10px] text-slate-400">Oleh: {{ $canned->creator->name ?? 'Admin' }}</span>
                <form method="POST" action="{{ route('helpdesk.canned.destroy', $canned->id) }}" onsubmit="return confirm('Hapus template ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold p-1">
                        <i class="fa-regular fa-trash-can mr-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400">
            <i class="fa-regular fa-comments text-4xl mb-2 text-slate-300"></i>
            <p class="font-bold text-sm text-slate-600">Belum ada template balasan cepat.</p>
            <p class="text-xs text-slate-400 mt-1">Buat template baru untuk membantu agen menjawab pertanyaan rutin.</p>
        </div>
        @endforelse
    </div>

    <!-- MODAL TAMBAH CANNED RESPONSE -->
    <div x-show="modalAddCanned" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-100" @click.away="modalAddCanned = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Tambah Template Balasan</h3>
                <button @click="modalAddCanned = false" type="button" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('helpdesk.canned.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Judul / Label Template <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Konfirmasi Permohonan Diterima" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Khusus untuk Divisi (Opsional)</label>
                    <select name="division_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="">Semua Divisi (Global)</option>
                        @foreach($divisions as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Isi Teks Balasan <span class="text-rose-500">*</span></label>
                    <textarea name="content" rows="5" required placeholder="Halo, permohonan Anda telah kami terima dan saat ini sedang dalam proses investigasi oleh tim kami..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl leading-relaxed"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button @click="modalAddCanned = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-primary-600 text-white font-bold shadow-md shadow-primary/20">Simpan Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
