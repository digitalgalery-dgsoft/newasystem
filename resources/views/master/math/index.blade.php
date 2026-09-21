@extends('layouts.app')

@section('title', 'Master Soal Matematika - ASYSTEM Support System')

@section('content')
<div class="space-y-6">

    <!-- BREADCRUMB -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('fitur.index') }}" class="hover:text-primary transition-colors flex items-center gap-1.5">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </a>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span>Master Data</span>
        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
        <span class="font-bold text-slate-800">Master Soal Matematika</span>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-info text-blue-600 text-base"></i>
                <span>{{ session('info') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- PAGE HEADER -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-primary-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/20 flex-shrink-0">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Master Soal Matematika</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-200">CBT Recruitment</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kelola bank soal ujian matematika CBT & evaluasi hasil pengerjaan kandidat ESA Groups. Soal yang berstatus aktif akan langsung diujikan kepada kandidat.
                </p>
            </div>
        </div>

        <button type="button" 
                onclick="openCreateModal()"
                class="px-5 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 flex-shrink-0">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah Soal Baru</span>
        </button>
    </div>

    <!-- 4 STAT METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Soal -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg flex-shrink-0 border border-blue-100">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ $totalQuestions }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Total Bank Soal</div>
            </div>
        </div>

        <!-- 2. Soal Aktif di CBT -->
        <div class="bg-white rounded-2xl p-4 border border-emerald-200/80 bg-emerald-50/20 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-200">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-xl font-black text-emerald-700">{{ $activeQuestions }}</div>
                <div class="text-[11px] font-semibold text-emerald-600">Aktif di Portal CBT</div>
            </div>
        </div>

        <!-- 3. Pilihan Ganda -->
        <div class="bg-white rounded-2xl p-4 border border-indigo-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg flex-shrink-0 border border-indigo-100">
                <i class="fa-solid fa-circle-dot"></i>
            </div>
            <div>
                <div class="text-xl font-black text-indigo-700">{{ $multipleChoiceCount }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Pilihan Ganda</div>
            </div>
        </div>

        <!-- 4. Isian Singkat -->
        <div class="bg-white rounded-2xl p-4 border border-amber-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg flex-shrink-0 border border-amber-100">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <div class="text-xl font-black text-amber-700">{{ $fillInBlankCount }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Isian Angka / Teks</div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('master.math.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari teks soal atau kunci jawaban..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all">
                @if(!empty($search))
                    <a href="{{ route('master.math.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </a>
                @endif
            </div>

            <!-- Tipe Soal -->
            <div class="sm:col-span-3">
                <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Tipe Soal</option>
                    <option value="multiple_choice" {{ $type === 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                    <option value="fill_in_the_blank" {{ $type === 'fill_in_the_blank' ? 'selected' : '' }}>Isian Singkat</option>
                </select>
            </div>

            <!-- Status Aktif -->
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif di CBT</option>
                    <option value="0" {{ $status === '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>
        </form>
    </div>

    <!-- TABLE MASTER SOAL -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-3 w-28">Tipe Soal</th>
                        <th class="py-3.5 px-4 min-w-[280px]">Pertanyaan</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Pilihan Jawaban (A-E)</th>
                        <th class="py-3.5 px-4 w-32 text-center">Kunci Jawaban</th>
                        <th class="py-3.5 px-3 w-28 text-center">Status CBT</th>
                        <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($questions as $q)
                        <tr class="hover:bg-blue-50/30 transition-colors {{ !$q->is_active ? 'opacity-60 bg-slate-50/50' : '' }}">
                            <!-- # ID -->
                            <td class="py-4 px-4 text-center font-bold text-slate-400">
                                {{ $q->id }}
                            </td>

                            <!-- TIPE SOAL -->
                            <td class="py-4 px-3">
                                @if($q->question_type === 'multiple_choice')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-bold border border-indigo-200">
                                        <i class="fa-solid fa-circle-dot text-[8px]"></i>
                                        Pilihan Ganda
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold border border-amber-200">
                                        <i class="fa-solid fa-pen text-[8px]"></i>
                                        Isian Angka
                                    </span>
                                @endif
                            </td>

                            <!-- PERTANYAAN -->
                            <td class="py-4 px-4">
                                <div class="font-medium text-slate-900 leading-relaxed max-w-xl">
                                    {{ $q->question_text }}
                                </div>
                            </td>

                            <!-- PILIHAN JAWABAN -->
                            <td class="py-4 px-4">
                                @if($q->question_type === 'multiple_choice' && !empty($q->parsed_choices))
                                    <div class="space-y-1">
                                        @foreach($q->parsed_choices as $optKey => $optVal)
                                            <div class="flex items-center gap-1.5 text-[11px] {{ strtoupper(trim($q->correct_answer)) === strtoupper($optKey) ? 'font-bold text-emerald-700 bg-emerald-50/80 px-2 py-0.5 rounded-md border border-emerald-200' : 'text-slate-600' }}">
                                                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[9px] font-black flex-shrink-0 {{ strtoupper(trim($q->correct_answer)) === strtoupper($optKey) ? 'bg-emerald-600 text-white' : '' }}">
                                                    {{ $optKey }}
                                                </span>
                                                <span class="truncate">{{ $optVal }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">- Isian Langsung -</span>
                                @endif
                            </td>

                            <!-- KUNCI JAWABAN -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-100/80 text-emerald-800 font-mono font-black text-xs border border-emerald-200 shadow-sm">
                                    <i class="fa-solid fa-key text-[10px] text-emerald-600"></i>
                                    {{ $q->correct_answer }}
                                </span>
                            </td>

                            <!-- STATUS CBT TOGGLE -->
                            <td class="py-4 px-3 text-center">
                                <form method="POST" action="{{ route('master.math.toggle', $q->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                            title="Klik untuk mengubah status aktif/non-aktif di CBT"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold transition-all {{ $q->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border border-emerald-300' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-300' }}">
                                        <span class="w-2 h-2 rounded-full {{ $q->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                        <span>{{ $q->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- AKSI -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick='openEditModal(@json($q))'
                                            title="Edit Soal"
                                            class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center border border-blue-200">
                                        <i class="fa-solid fa-pencil text-[11px]"></i>
                                    </button>
                                    <form method="POST" action="{{ route('master.math.destroy', $q->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal #{{ $q->id }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Soal"
                                                class="w-7 h-7 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center border border-rose-200">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
                                    <i class="fa-solid fa-inbox"></i>
                                </div>
                                <p class="font-bold text-slate-600">Tidak ada soal matematika ditemukan.</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Sesuaikan filter atau tambahkan soal baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH SOAL BARU -->
<!-- ========================================================================= -->
<div id="createModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 my-8 transition-all">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg font-bold border border-blue-100">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Tambah Soal Matematika Baru</h3>
                    <p class="text-xs text-slate-500">Soal akan langsung dimasukkan ke bank soal CBT</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors flex items-center justify-center">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('master.math.store') }}" class="space-y-4">
            @csrf

            <!-- Tipe Soal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Soal <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-blue-500 transition-all font-semibold text-xs">
                        <input type="radio" name="question_type" value="multiple_choice" checked onchange="toggleChoiceFields('create', this.value)" class="text-primary focus:ring-primary">
                        <span>Pilihan Ganda (A-E)</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-blue-500 transition-all font-semibold text-xs">
                        <input type="radio" name="question_type" value="fill_in_the_blank" onchange="toggleChoiceFields('create', this.value)" class="text-primary focus:ring-primary">
                        <span>Isian Singkat (Angka / Teks)</span>
                    </label>
                </div>
            </div>

            <!-- Teks Pertanyaan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Pertanyaan <span class="text-rose-500">*</span></label>
                <textarea name="question_text" rows="3" required placeholder="Tuliskan soal cerita matematika atau perhitungan di sini..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all"></textarea>
            </div>

            <!-- Opsi Pilihan Ganda (A-E) -->
            <div id="createChoicesWrapper" class="space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <label class="block text-xs font-bold text-slate-700 mb-2">Opsi Pilihan Jawaban (A s/d E):</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">A</span>
                        <input type="text" name="choices[A]" placeholder="Opsi A" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">B</span>
                        <input type="text" name="choices[B]" placeholder="Opsi B" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">C</span>
                        <input type="text" name="choices[C]" placeholder="Opsi C" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">D</span>
                        <input type="text" name="choices[D]" placeholder="Opsi D" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2 sm:col-span-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">E</span>
                        <input type="text" name="choices[E]" placeholder="Opsi E (Opsional)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                </div>
            </div>

            <!-- Kunci Jawaban -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kunci Jawaban Benar <span class="text-rose-500">*</span></label>
                <input type="text" name="correct_answer" required placeholder="Contoh: A (untuk pilgan) atau 170000 (untuk isian)" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all">
                <p class="text-[11px] text-slate-400 mt-1">Untuk pilihan ganda: ketik hurufnya (A, B, C, D, atau E). Untuk isian singkat: ketik angka atau hasil hitung eksak (misal: 170000 atau 71.43%).</p>
            </div>

            <!-- Status Aktif -->
            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                    <span class="text-xs font-bold text-slate-700">Aktifkan soal ini di Portal CBT kandidat</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Soal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT SOAL -->
<!-- ========================================================================= -->
<div id="editModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 my-8 transition-all">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-lg font-bold border border-indigo-100">
                    <i class="fa-solid fa-pencil"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Edit Soal Matematika <span id="editModalTitleId"></span></h3>
                    <p class="text-xs text-slate-500">Perbarui rincian pertanyaan, pilihan, atau kunci jawaban</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors flex items-center justify-center">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Tipe Soal -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Soal <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-blue-500 transition-all font-semibold text-xs">
                        <input type="radio" name="question_type" id="edit_type_mc" value="multiple_choice" onchange="toggleChoiceFields('edit', this.value)" class="text-primary focus:ring-primary">
                        <span>Pilihan Ganda (A-E)</span>
                    </label>
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-blue-500 transition-all font-semibold text-xs">
                        <input type="radio" name="question_type" id="edit_type_fib" value="fill_in_the_blank" onchange="toggleChoiceFields('edit', this.value)" class="text-primary focus:ring-primary">
                        <span>Isian Singkat (Angka / Teks)</span>
                    </label>
                </div>
            </div>

            <!-- Teks Pertanyaan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Teks Pertanyaan <span class="text-rose-500">*</span></label>
                <textarea name="question_text" id="edit_question_text" rows="3" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all"></textarea>
            </div>

            <!-- Opsi Pilihan Ganda (A-E) -->
            <div id="editChoicesWrapper" class="space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <label class="block text-xs font-bold text-slate-700 mb-2">Opsi Pilihan Jawaban (A s/d E):</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">A</span>
                        <input type="text" name="choices[A]" id="edit_choice_A" placeholder="Opsi A" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">B</span>
                        <input type="text" name="choices[B]" id="edit_choice_B" placeholder="Opsi B" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">C</span>
                        <input type="text" name="choices[C]" id="edit_choice_C" placeholder="Opsi C" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">D</span>
                        <input type="text" name="choices[D]" id="edit_choice_D" placeholder="Opsi D" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                    <div class="flex items-center gap-2 sm:col-span-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">E</span>
                        <input type="text" name="choices[E]" id="edit_choice_E" placeholder="Opsi E (Opsional)" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs">
                    </div>
                </div>
            </div>

            <!-- Kunci Jawaban -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Kunci Jawaban Benar <span class="text-rose-500">*</span></label>
                <input type="text" name="correct_answer" id="edit_correct_answer" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary focus:bg-white transition-all">
            </div>

            <!-- Status Aktif -->
            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary">
                    <span class="text-xs font-bold text-slate-700">Aktifkan soal ini di Portal CBT kandidat</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createModal').classList.remove('flex');
}

function openEditModal(q) {
    document.getElementById('editModalTitleId').innerText = '#' + q.id;
    document.getElementById('editForm').action = "/master/math/" + q.id;
    document.getElementById('edit_question_text').value = q.question_text || '';
    document.getElementById('edit_correct_answer').value = q.correct_answer || '';
    document.getElementById('edit_is_active').checked = !!q.is_active;

    if (q.question_type === 'multiple_choice') {
        document.getElementById('edit_type_mc').checked = true;
        toggleChoiceFields('edit', 'multiple_choice');
        let choices = q.parsed_choices || q.choices || {};
        if (typeof choices === 'string') {
            try { choices = JSON.parse(choices); } catch(e) { choices = {}; }
        }
        document.getElementById('edit_choice_A').value = choices.A || choices.a || '';
        document.getElementById('edit_choice_B').value = choices.B || choices.b || '';
        document.getElementById('edit_choice_C').value = choices.C || choices.c || '';
        document.getElementById('edit_choice_D').value = choices.D || choices.d || '';
        document.getElementById('edit_choice_E').value = choices.E || choices.e || '';
    } else {
        document.getElementById('edit_type_fib').checked = true;
        toggleChoiceFields('edit', 'fill_in_the_blank');
        document.getElementById('edit_choice_A').value = '';
        document.getElementById('edit_choice_B').value = '';
        document.getElementById('edit_choice_C').value = '';
        document.getElementById('edit_choice_D').value = '';
        document.getElementById('edit_choice_E').value = '';
    }

    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

function toggleChoiceFields(prefix, type) {
    const wrapper = document.getElementById(prefix + 'ChoicesWrapper');
    if (type === 'multiple_choice') {
        wrapper.classList.remove('hidden');
    } else {
        wrapper.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
