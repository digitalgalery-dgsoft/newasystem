@extends('layouts.app')

@section('title', 'Catatan Aktivitas Harian - Work Plan System')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false }">

    <!-- Page Header Card -->
    <div class="page-header-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-indigo-600 via-primary-600 to-blue-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-indigo-600/25 flex-shrink-0">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Catatan Aktivitas Harian</h1>
                    <span class="badge-pill bg-indigo-50 text-indigo-700 border-indigo-200">
                        <i class="fa-solid fa-clock-rotate-left text-[10px]"></i> DAILY WORK LOG
                    </span>
                    <span class="badge-pill bg-emerald-50 text-emerald-700 border-emerald-200 font-semibold">
                        <i class="fa-solid fa-building-user text-[10px]"></i> {{ $userDivisi }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Dokumentasi aktivitas operasional, capaian kerja harian, dan pencatatan kendala divisi untuk pelaporan rutin manajemen.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" 
                    @click="createModalOpen = true" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20">
                <i class="fa-solid fa-plus text-sm"></i>
                <span>Tambah Catatan Baru</span>
            </button>
            <a href="{{ route('workplan.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-table-columns text-indigo-600"></i>
                <span>Buka Kanban Board</span>
            </a>
            <a href="{{ route('workplan.export') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition-all shadow-sm">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between animate-in fade-in duration-200">
        <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between animate-in fade-in duration-200">
        <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    <!-- 4 STATISTIC METRIC CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
        <!-- 1. Total Catatan -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-list-ol"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">Total Catatan</div>
                <div class="text-xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalLogs) }}</div>
            </div>
        </div>

        <!-- 2. Catatan Hari Ini -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">Hari Ini</div>
                <div class="text-xl font-extrabold text-emerald-600 tracking-tight">{{ number_format($todayLogs) }}</div>
            </div>
        </div>

        <!-- 3. Total Divisi Terlibat -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">Divisi Terdaftar</div>
                <div class="text-xl font-extrabold text-indigo-600 tracking-tight">{{ count($distinctDivisions) }}</div>
            </div>
        </div>

        <!-- 4. User Aktif -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">Pengguna Aktif</div>
                <div class="text-sm font-extrabold text-slate-800 tracking-tight truncate">{{ $userName }}</div>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH TOOLBAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('workplan.daily') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            
            <!-- Keyword Search -->
            <div class="flex-1 relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-sm"></i>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari aktivitas kerja, divisi, kendala, atau nama karyawan..." 
                       class="w-full pl-10 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                @if(!empty($search))
                <a href="{{ route('workplan.daily', array_merge(request()->except('search'))) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Filter Divisi -->
                <div class="w-full sm:w-48">
                    <select name="divisi" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium text-slate-700 transition-all">
                        <option value="all">Semua Divisi</option>
                        @foreach($distinctDivisions as $div)
                            <option value="{{ $div }}" {{ $filterDivisi === $div ? 'selected' : '' }}>{{ $div }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal -->
                <div class="w-full sm:w-40">
                    <input type="date" 
                           name="tanggal" 
                           value="{{ $filterTanggal }}"
                           onchange="this.form.submit()"
                           class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-medium text-slate-700 transition-all">
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="flex items-center gap-1.5">
                    <button type="submit" class="px-3.5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition-all shadow-sm">
                        Filter
                    </button>
                    @if(!empty($search) || !empty($filterDivisi) || !empty($filterTanggal))
                    <a href="{{ route('workplan.daily') }}" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4 w-36">Tanggal & Waktu</th>
                        <th class="py-3.5 px-4 w-44">Karyawan</th>
                        <th class="py-3.5 px-4 w-36">Divisi</th>
                        <th class="py-3.5 px-4">Rincian Aktivitas Kerja</th>
                        <th class="py-3.5 px-4 w-60">Kendala / Masalah</th>
                        <th class="py-3.5 px-4 w-20 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($dailyLogs as $index => $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                            {{ $dailyLogs->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <div class="font-bold text-slate-800">
                                {{ $log->tanggal ? $log->tanggal->format('d M Y') : '-' }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                <i class="fa-regular fa-clock text-[9px] mr-1"></i>
                                {{ $log->waktu ? $log->waktu->format('H:i') : '-' }} WIB
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ App\Models\Task::getAvatarUrl($log->user) }}" 
                                     alt="{{ $log->user }}" 
                                     class="w-6 h-6 rounded-full object-cover ring-1 ring-slate-200 bg-slate-100 flex-shrink-0"
                                     onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?background=6366f1&color=fff&name={{ urlencode($log->user ?: 'User') }}';">
                                <span class="font-bold text-slate-800 truncate max-w-[140px] block" title="{{ $log->user }}">
                                    {{ $log->user ?: '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <i class="fa-solid fa-briefcase text-[9px]"></i>
                                {{ $log->divisi ?: 'Umum' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="text-slate-800 font-medium leading-relaxed max-w-xl">
                                {{ $log->aktivitas }}
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            @php
                                $hasKendala = !empty($log->kendala) && !in_array(strtolower(trim($log->kendala)), ['-', 'tidak ada', 'tidak ada kendala', 'belum ada kendala', 'aman', 'lancar']);
                            @endphp
                            @if($hasKendala)
                            <div class="p-2 rounded-xl bg-rose-50/80 border border-rose-200/80 text-rose-700 text-[11px] leading-snug font-medium flex items-start gap-1.5">
                                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xs mt-0.5 flex-shrink-0"></i>
                                <span>{{ $log->kendala }}</span>
                            </div>
                            @else
                            <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                {{ $log->kendala ?: 'Aman / Lancar' }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                            @if($isAdmin || $log->user === $userName)
                            <form method="POST" action="{{ route('workplan.daily.destroy', $log->kode) }}" onsubmit="return confirmDeleteWorkplan(event, 'Hapus catatan aktivitas ini?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-all" title="Hapus Catatan">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                            @else
                            <span class="text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                <i class="fa-regular fa-folder-open"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-700">Belum ada catatan aktivitas harian</div>
                            <p class="text-[11px] text-slate-400 mt-1">Gunakan tombol "Tambah Catatan Baru" di atas untuk menambahkan log operasional harian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dailyLogs->hasPages())
        <div class="p-4 border-t border-slate-200/80 bg-slate-50/50">
            {{ $dailyLogs->links() }}
        </div>
        @endif
    </div>

    <!-- ============================================================================== -->
    <!-- MODAL TAMBAH CATATAN HARIAN BARU                                               -->
    <!-- ============================================================================== -->
    <div id="createDailyModal" 
         x-show="createModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200"
             @click.away="createModalOpen = false">
            
            <!-- Header Modal -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-primary-50 text-primary flex items-center justify-center text-base font-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Catat Aktivitas Harian</h3>
                        <p class="text-[11px] text-slate-500">Laporkan aktivitas, progress, dan kendala divisi kerja.</p>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Form Body -->
            <form method="POST" action="{{ route('workplan.daily.store') }}" class="flex-1 overflow-y-auto p-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Pelaksanaan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" 
                               name="tanggal" 
                               required 
                               value="{{ date('Y-m-d') }}" 
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold text-slate-800 transition-all">
                    </div>

                    <!-- Divisi Kerja -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Divisi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="divisi" 
                               required 
                               value="{{ $userDivisi }}"
                               list="divisionSuggestions"
                               placeholder="Contoh: HRD, IT, SADATA..." 
                               class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold text-slate-800 transition-all">
                        <datalist id="divisionSuggestions">
                            @foreach($distinctDivisions as $d)
                                <option value="{{ $d }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <!-- User Pencatat (Read-only) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Karyawan Pelapor
                    </label>
                    <input type="text" 
                           value="{{ $userName }}" 
                           readonly 
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-semibold cursor-not-allowed">
                </div>

                <!-- Rincian Aktivitas -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Rincian Aktivitas Kerja <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="aktivitas" 
                              required 
                              rows="4" 
                              placeholder="Deskripsikan pekerjaan yang dikerjakan hari ini, hasil yang dicapai, atau koordinasi yang dilakukan..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 font-medium transition-all"></textarea>
                </div>

                <!-- Kendala / Masalah yang Dihadapi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kendala / Masalah yang Dihadapi
                    </label>
                    <textarea name="kendala" 
                              rows="2" 
                              placeholder="Ketik kendala operasional yang dihadapi (jika tidak ada kendala, boleh dikosongkan)..."
                              class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-700 transition-all"></textarea>
                    <span class="text-[10px] text-slate-400 mt-1 block">Jika dikosongkan, otomatis tersimpan sebagai "Belum ada kendala".</span>
                </div>

                <!-- Footer Buttons -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="createModalOpen = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-primary-600/20 flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Catatan</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function confirmDeleteWorkplan(event, message) {
    event.preventDefault();
    const form = event.target.closest('form');
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: message || 'Apakah Anda yakin ingin menghapus catatan aktivitas ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl shadow-2xl',
            confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
            cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}

@if(session('success'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: @json(session('success')),
        timer: 2500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
@endif

@if(session('error'))
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Perhatian!',
        text: @json(session('error')),
        confirmButtonColor: '#0F52BA',
        customClass: { popup: 'rounded-2xl' }
    });
});
@endif
</script>
@endpush
