@extends('layouts.app')

@section('title', 'Master Soal Kepribadian - ASYSTEM Support System')

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
        <span class="font-bold text-slate-800">Master Soal Kepribadian</span>
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
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-purple-700 via-indigo-600 to-primary-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-500/20 flex-shrink-0">
                <i class="fa-solid fa-brain"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Master Soal Kepribadian</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-50 text-purple-700 font-bold text-[10px] border border-purple-200">DISC / Florence Littauer</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Kelola 40 butir pernyataan profil kepribadian kerja yang diujikan kepada kandidat di portal CBT. Setiap butir merepresentasikan 4 temperamen: Melankolis (A), Sanguinis (B), Koleris (C), dan Plegmatis (D).
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 self-end md:self-auto">
            <a href="{{ route('master.math.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 font-bold text-xs transition-all flex items-center gap-2">
                <i class="fa-solid fa-calculator text-slate-400"></i>
                <span>Master Soal Matematika</span>
            </a>
        </div>
    </div>

    <!-- 4 STAT METRIC CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Butir Soal -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-lg flex-shrink-0 border border-purple-100">
                <i class="fa-solid fa-list-ol"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ $totalQuestions }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Total Butir Soal</div>
            </div>
        </div>

        <!-- 2. Kekuatan Diri (1-20) -->
        <div class="bg-white rounded-2xl p-4 border border-emerald-200/80 bg-emerald-50/20 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg flex-shrink-0 border border-emerald-200">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="text-xl font-black text-emerald-700">{{ $strengthsCount }}</div>
                <div class="text-[11px] font-semibold text-emerald-600">Kekuatan (No 1-20)</div>
            </div>
        </div>

        <!-- 3. Kelemahan Diri (21-40) -->
        <div class="bg-white rounded-2xl p-4 border border-amber-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg flex-shrink-0 border border-amber-100">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <div class="text-xl font-black text-amber-700">{{ $weaknessesCount }}</div>
                <div class="text-[11px] font-semibold text-slate-500">Kelemahan (No 21-40)</div>
            </div>
        </div>

        <!-- 4. Karakter DISC -->
        <div class="bg-white rounded-2xl p-4 border border-blue-200/80 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-lg flex-shrink-0 border border-blue-100">
                <i class="fa-solid fa-users-rays"></i>
            </div>
            <div>
                <div class="text-xs font-black text-slate-900 leading-tight">M / S / K / P</div>
                <div class="text-[11px] font-semibold text-slate-500">4 Karakter Lengkap</div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('master.personality.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 w-full">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari kata/sifat kepribadian (contoh: Logis, Antusias, Berani)..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:outline-none focus:border-primary transition-all">
            </div>

            <!-- Category Filter -->
            <div class="w-full sm:w-56">
                <select name="category" 
                        onchange="this.form.submit()"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:border-primary transition-all">
                    <option value="all" {{ $category === 'all' ? 'selected' : '' }}>Semua Bagian (1-40)</option>
                    <option value="strengths" {{ $category === 'strengths' ? 'selected' : '' }}>Kekuatan Diri (1-20)</option>
                    <option value="weaknesses" {{ $category === 'weaknesses' ? 'selected' : '' }}>Kelemahan Diri (21-40)</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>

            <!-- Reset Button -->
            @if(!empty($search) || $category !== 'all')
                <a href="{{ route('master.personality.index') }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-black text-slate-900">Daftar Butir Soal Kepribadian</h3>
                <p class="text-[11px] text-slate-400">Menampilkan {{ $questions->count() }} dari {{ $totalQuestions }} total butir soal terdaftar</p>
            </div>
            <div class="flex items-center gap-2 text-[11px] font-bold">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                    A = Melankolis
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                    B = Sanguinis
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200">
                    C = Koleris
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                    D = Plegmatis
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-16 text-center">No</th>
                        <th class="py-3.5 px-3 w-28">Kategori</th>
                        <th class="py-3.5 px-4">Pilihan A (Melankolis)</th>
                        <th class="py-3.5 px-4">Pilihan B (Sanguinis)</th>
                        <th class="py-3.5 px-4">Pilihan C (Koleris)</th>
                        <th class="py-3.5 px-4">Pilihan D (Plegmatis)</th>
                        <th class="py-3.5 px-4 w-20 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($questions as $q)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- No -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="w-7 h-7 rounded-xl bg-slate-100 font-black text-xs text-slate-700 inline-flex items-center justify-center border border-slate-200">
                                    {{ $q->id }}
                                </span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $q->category_badge_class }}">
                                    {{ $q->id <= 20 ? 'Kekuatan' : 'Kelemahan' }}
                                </span>
                            </td>

                            <!-- Pilihan A (Melankolis) -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-indigo-100 text-indigo-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">A</span>
                                    <span>{{ $q->pilihan_a }}</span>
                                </div>
                            </td>

                            <!-- Pilihan B (Sanguinis) -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-amber-100 text-amber-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">B</span>
                                    <span>{{ $q->pilihan_b }}</span>
                                </div>
                            </td>

                            <!-- Pilihan C (Koleris) -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-rose-100 text-rose-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">C</span>
                                    <span>{{ $q->pilihan_c }}</span>
                                </div>
                            </td>

                            <!-- Pilihan D (Plegmatis) -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800 text-xs flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 text-[9px] font-black flex items-center justify-center flex-shrink-0">D</span>
                                    <span>{{ $q->pilihan_d }}</span>
                                </div>
                            </td>

                            <!-- Aksi Edit -->
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode($q) }})"
                                        title="Edit Butir Pilihan"
                                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-primary-50 text-slate-600 hover:text-primary transition-all inline-flex items-center justify-center">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                                <p class="text-xs font-semibold">Tidak ada butir soal kepribadian yang cocok dengan filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- EDIT MODAL -->
<div id="editModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 my-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-base font-black">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">Edit Butir Soal Kepribadian #<span id="modalQuestionId"></span></h3>
                    <p class="text-[11px] text-slate-400" id="modalQuestionCategory"></p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="space-y-4 text-xs">
                <!-- Pilihan A (Melankolis) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 text-[10px] font-black flex items-center justify-center">A</span>
                        <span>Pilihan A (Melankolis - Analitis, Rapi, Tekun)</span>
                    </label>
                    <input type="text" name="pilihan_a" id="edit_pilihan_a" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                </div>

                <!-- Pilihan B (Sanguinis) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black flex items-center justify-center">B</span>
                        <span>Pilihan B (Sanguinis - Ramah, Populer, Komunikatif)</span>
                    </label>
                    <input type="text" name="pilihan_b" id="edit_pilihan_b" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                </div>

                <!-- Pilihan C (Koleris) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black flex items-center justify-center">C</span>
                        <span>Pilihan C (Koleris - Pemimpin, Berani, Tegas)</span>
                    </label>
                    <input type="text" name="pilihan_c" id="edit_pilihan_c" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                </div>

                <!-- Pilihan D (Plegmatis) -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black flex items-center justify-center">D</span>
                        <span>Pilihan D (Plegmatis - Damai, Tenang, Sabar, Stabil)</span>
                    </label>
                    <input type="text" name="pilihan_d" id="edit_pilihan_d" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md shadow-primary/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(data) {
    document.getElementById('modalQuestionId').textContent = data.id;
    document.getElementById('modalQuestionCategory').textContent = data.id <= 20 
        ? 'Bagian 1: Kekuatan Diri (Strengths)' 
        : 'Bagian 2: Kelemahan Diri (Weaknesses)';

    document.getElementById('edit_pilihan_a').value = data.pilihan_a || '';
    document.getElementById('edit_pilihan_b').value = data.pilihan_b || '';
    document.getElementById('edit_pilihan_c').value = data.pilihan_c || '';
    document.getElementById('edit_pilihan_d').value = data.pilihan_d || '';

    const form = document.getElementById('editForm');
    form.action = "{{ url('master/personality') }}/" + data.id;

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>
@endpush
